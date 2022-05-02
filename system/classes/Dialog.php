<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use Mozg\Security\AntiSpam;
use FluffyDollop\Support\Registry;
use FluffyDollop\Support\Status;

class Dialog
{
    /** @var int ID юзера */
    public int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;

    }

    /**
     * Отправка сообщения
     *
     * @param int $for_user_id Юзер, которому отправляем сообщение
     * @param int $room_id Группа или 0
     * @param string $msg Сообщение
     * @param string $attach_files Прикрепленный контент к сообщению
     * @return array Номер сообщения, статус, имя пользователя и фото пользователя
     */
    final public function send(int $for_user_id, int $room_id, string $msg, string $attach_files): array
    {
        /** @var array $user_info */
        $user_info = DB::getDB()->row(
            "SELECT user_id, user_photo, user_search_pref FROM `users` WHERE user_id = ?", $this->user_id);
        if (Flood::check('messages')) {
            return [
                'status' => Status::LIMIT
            ];
        }
        if ($room_id) {
            $for_user_id = 0;
        }
        $attach_files = str_replace('vote|', 'hack|', $attach_files);
        if (Flood::check('identical', $msg . $attach_files)) {
            return array(
                'status' => Status::LIMIT
            );
        }

        if (!empty($msg) || !empty($attach_files)) {
            if ($room_id == 0) {
                /** @var array $row */
                $row = DB::getDB()->row("SELECT user_privacy FROM `users` WHERE user_id = ?", $for_user_id);
            } else {
                /** @var array $row */
                $row = DB::getDB()->row(
                    "SELECT id FROM `room_users` WHERE room_id = ? and oid2 = ? and type = 0",
                    $room_id, $this->user_id
                );
            }
            if ($row) {
                if ($room_id == 0) {
                    $user_privacy = unserialize($row['user_privacy']);
                    $CheckBlackList = CheckBlackList($for_user_id);
                    if ($user_privacy['val_msg'] == 2) {
                        $check_friend = CheckFriends($for_user_id);
                    } else {
                        $check_friend = null;
                    }
                    if (!$CheckBlackList and $user_privacy['val_msg'] == 1 or
                        $user_privacy['val_msg'] == 2 and $check_friend) {
                        $xPrivasy = 1;
                    } else {
                        $xPrivasy = 0;
                    }
                } else {
                    $xPrivasy = 1;
                }
                if ($xPrivasy && $this->user_id !== $for_user_id) {
                    AntiSpam::LogInsert('identical', $msg . $attach_files);
                    if (!$room_id && !CheckFriends($for_user_id))
                        AntiSpam::LogInsert('messages');
                    $user_ids = array();
                    if (!$room_id) {
                        $user_ids[] = $for_user_id;
                        $user_ids[] = $this->user_id;
                    } else {
                        $sqlUsers = DB::getDB()->run(
                            "SELECT oid2 FROM `room_users` WHERE room_id = ? and type = 0",
                            $room_id
                        );
                        foreach ($sqlUsers as $rowUser)
                            $user_ids[] = $rowUser['oid2'];
                    }

                    DB::getDB()->insert('messages', [
                        'user_ids' => implode(',', $user_ids),
                        'text' => $msg,
                        'room_id' => $room_id,
                        'date' => time(),
                        'history_user_id' => $this->user_id,
                        'attach' => $attach_files,
                    ]);

                    $dbid = DB::getDB()->lastInsertId();
                    $user_ids = array_diff($user_ids, array($this->user_id));
                    foreach ($user_ids as $k => $v) {
                        DB::getDB()->query(
                            "UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v . "'");
                        $check_im_2 = DB::getDB()->row(
                            "SELECT id FROM im WHERE iuser_id = ? AND im_user_id = ? AND room_id = ?",
                            $v, ($room_id ? 0 : $this->user_id), $room_id
                        );
                        if (!$check_im_2) {
                            DB::getDB()->insert('im', [
                                'iuser_id' => $v,
                                'im_user_id' => ($room_id ? 0 : $this->user_id),
                                'room_id' => $room_id,
                                'msg_num' => 1,
                                'idate' => time(),
                                'all_msg_num' => 1,
                            ]);
                        } else {
                            DB::getDB()->update('im', [
                                'idate' => time(),
                                'msg_num+1',
                                'all_msg_num+1',
                            ], [
                                'id' => $check_im_2['id']
                            ]);
                        }
                        /** @var array $check2 */
                        $check2 = DB::getDB()->row(
                            "SELECT user_last_visit FROM `users` WHERE user_id = ?", $v);
                        $update_time = time() - 70;
                        if ($check2['user_last_visit'] >= $update_time) {
                            $msg_lnk = '/messages#' . ($room_id ? 'c' . $room_id : $this->user_id);

                            DB::getDB()->insert('updates', [
                                'for_user_id' => $v,
                                'from_user_id' => $this->user_id,
                                'type' => '8',
                                'date' => time(),
                                'text' => $msg,
                                'user_photo' => $user_info['user_photo'],
                                'user_search_pref' => $user_info['user_search_pref'],
                                'lnk' => $msg_lnk,
                            ]);

                            Cache::mozgCreateCache("user_{$v}/updates", 1);
                        }
                        Cache::mozgClearCacheFile('user_' . $v . '/im');
                        Cache::mozgCreateCache('user_' . $v . '/im_update', '1');
                        Cache::mozgCreateCache("user_{$v}/typograf{$this->user_id}", "");
                    }
                    $check_im = DB::getDB()->row(
                        "SELECT id FROM `im` WHERE iuser_id = ? AND im_user_id = ? AND room_id = ?",
                        $this->user_id, $for_user_id, $room_id
                    );
                    if (!$check_im) {
                        DB::getDB()->insert('im', [
                            'iuser_id' => $this->user_id,
                            'im_user_id' => $for_user_id,
                            'room_id' => $room_id,
                            //'msg_num' => 1,
                            'idate' => time(),
                            'all_msg_num' => 1,
                        ]);
                    } else {
                        DB::getDB()->update('im', [
                            'idate' => time(),
                            //'msg_num+1',
                            'all_msg_num+1',
                        ], [
                            'id' => $check_im['id']
                        ]);
                    }
                    return [
                        'status' => Status::OK,
                        'id' => $dbid,
                        'user_photo' => $user_info['user_photo'],
                        'user_name' => $user_info['user_search_pref']
                    ];
                }
                return [
                    'status' => Status::PRIVACY
                ];
            }
            return [
                'status' => Status::NOT_USER
            ];
        }

        return [
            'status' => Status::NOT_VALID
        ];

    }

    /**
     * @param int $msg_id
     * @return bool "false" - если не найдено
     */
    final public function read(int $msg_id): bool
    {
        $check = DB::getDB()->row(
            "SELECT id, id2, date, room_id, history_user_id, room_id, read_ids, user_ids 
            FROM `messages` WHERE id = ? AND find_in_set( ? , user_ids) 
            AND not find_in_set( ? , del_ids) AND not find_in_set( ? , read_ids) 
            AND history_user_id != ?", $msg_id, $this->user_id, $this->user_id, $this->user_id, $this->user_id);
        if ($check) {
            $read_ids = explode(',', $check['read_ids']);
            $read_ids[] = $this->user_id;
            DB::getDB()->update('messages', [
                'read_ids' => implode(',', $read_ids),
            ], [
                'id' => $check['id']
            ]);
            DB::getDB()->update('users', [
                'user_pm_num-1',
            ], [
                'user_id' => $this->user_id
            ]);
            if (!$check['room_id']) {
                $user_ids = explode(',', $check['user_ids']);
                $im_user_id = $user_ids[0] == $this->user_id ? $user_ids[1] : $user_ids[0];
            } else {
                $im_user_id = 0;
            }
            DB::getDB()->update('im', [
                'user_pm_num-1',
            ], [
                'iuser_id' => $this->user_id,
                'im_user_id' => $im_user_id,
                'room_id' => $check['room_id'],
            ]);
            Cache::mozgClearCacheFile('user_' . $check['history_user_id'] . '/im');
            return true;
        }
        return false;
    }

    /**
     * @throws \ErrorException
     */
    final public function typograf(int $room_id, int $for_user_id, string $action): bool
    {
        if ($room_id === 0) {
            if ($action === 'start') {
                Cache::mozgCreateCache("user_{$for_user_id}/typograf{$this->user_id}", "");
                return true;
            }
            if ($action === 'stop') {
                Cache::mozgCreateCache("user_{$for_user_id}/typograf{$this->user_id}", 1);
                return true;
            }
            throw new \ErrorException('not action');
        }
        return false;
    }

    final public function delete(int $room_id, int $im_user_id, $user_id): bool
    {
        if ($room_id > 0) {
            $im_user_id = 0;
        }
        $row = DB::getDB()->row(
            "SELECT id, msg_num, all_msg_num FROM `im` WHERE iuser_id = ? AND im_user_id = ? AND room_id = ?",
            $this->user_id, $im_user_id, $room_id);
        if ($row) {
            $sql = DB::getDB()->row("SELECT id, read_ids, room_id, history_user_id, del_ids 
                FROM `messages` 
                WHERE " .
                ($room_id ? "room_id = '{$room_id}'" : "room_id = 0 and find_in_set('{$im_user_id}', tb1.user_ids)") .
                " and find_in_set(?, user_ids) AND not find_in_set(?, del_ids)", $user_id, $user_id);
            if ($sql) {
                foreach ($sql as $row2) {
                    $del_ids = $row2['del_ids'] ? explode(',', $row2['del_ids']) : array();
                    $del_ids[] = $user_id;
                    $del_ids = implode(',', $del_ids);

                    DB::getDB()->update('messages', [
                        'del_ids' => $del_ids,
                    ], [
                        'id' => $row2['id'],
                    ]);
                    $read_ids = explode(',', $row2['read_ids']);
                    if ($row['history_user_id'] !== $user_id && !in_array($user_id, $read_ids, true)) {
                        $read_ids[] = $user_id;
                        DB::getDB()->update('messages', [
                            'read_ids' => implode(',', $read_ids),
                        ], [
                            'id' => $row2['id'],
                        ]);
                        DB::getDB()->update('users', [
                            'user_pm_num-2',
                        ], [
                            'user_id' => $user_id,
                        ]);
                        if (!$row2['room_id']) {
                            $user_ids = explode(',', $row2['user_ids']);
                            $im_user_id = $user_ids[0] === $user_id ? $user_ids[1] : $user_ids[0];
                        } else {
                            $im_user_id = 0;
                        }
                        DB::getDB()->update('im', [
                            'msg_num-1',
                        ], [
                            'iuser_id' => $user_id,
                            'im_user_id' => $im_user_id,
                            'room_id' => $row2['room_id'],
                        ]);
                        Cache::mozgClearCacheFile('user_' . $row2['history_user_id'] . '/im');
                    }
                }
            }
            if ($row['msg_num']) {
                DB::getDB()->update('users', [
                    'user_pm_num-' . $row['msg_num'],
                ], [
                    'user_id' => $user_id,
                ]);

            }
            DB::getDB()->delete('im', [
                'id' => $row['id']
            ]);
            return true;
        }
        return false;
    }
}