<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use Mozg\classes\Status;

class Dialog
{
    /** @var int ID юзера */
    public int $user_id;
    /** @var Mysql Объект базы данных */
    private Mysql $db;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
        $this->db = Registry::get('db');
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
        $user_info = $this->db->super_query("SELECT user_id, user_photo, user_search_pref FROM `users` WHERE user_id = '{$this->user_id}'");
        AntiSpam::check('messages');
        if ($room_id) {
            $for_user_id = 0;
        }
        $attach_files = str_replace('vote|', 'hack|', $attach_files);
        AntiSpam::check('identical', $msg . $attach_files);
        if (!empty($msg) || !empty($attach_files)) {
            if (!$room_id) {
                $row = $this->db->super_query("SELECT user_privacy FROM `users` WHERE user_id = '" . $for_user_id . "'");
            } else {
                $row = $this->db->super_query("SELECT id FROM `room_users` WHERE room_id = '" . $room_id . "' and oid2 = '" . $this->user_id . "' and type = 0");
            }
            if ($row) {
                if (!$room_id) {
                    $user_privacy = xfieldsdataload($row['user_privacy']);
                    $CheckBlackList = CheckBlackList($for_user_id);
                    if ($user_privacy['val_msg'] == 2) {
                        $check_friend = CheckFriends($for_user_id);
                    } else {
                        $check_friend = null;
                    }
                    if (!$CheckBlackList and $user_privacy['val_msg'] == 1 or $user_privacy['val_msg'] == 2 and $check_friend) {
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
                        $sqlUsers = $this->db->super_query("SELECT oid2 FROM `room_users` WHERE room_id = '" . $room_id . "' and type = 0", true);
                        foreach ($sqlUsers as $rowUser)
                            $user_ids[] = $rowUser['oid2'];
                    }
                    $this->db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . time() . "', history_user_id = '" . $this->user_id . "', attach = '" . $attach_files . "'");
                    $dbid = $this->db->insert_id();
                    $user_ids = array_diff($user_ids, array($this->user_id));
                    foreach ($user_ids as $k => $v) {
                        $this->db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v . "'");
                        $check_im_2 = $this->db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v . "' AND im_user_id = '" . ($room_id ? 0 : $this->user_id) . "' AND room_id = '" . $room_id . "'");
                        if (!$check_im_2) {
                            $this->db->query("INSERT INTO im SET iuser_id = '" . $v . "', im_user_id = '" . ($room_id ? 0 : $this->user_id) . "', room_id = '" . $room_id . "', msg_num = 1, idate = '" . time() . "', all_msg_num = 1");
                        } else {
                            $this->db->query("UPDATE im  SET idate = '" . time() . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                        }
                        $check2 = $this->db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v}'");
                        $update_time = time() - 70;
                        if ($check2['user_last_visit'] >= $update_time) {
                            $msg_lnk = '/messages#' . ($room_id ? 'c' . $room_id : $this->user_id);
                            $this->db->query("INSERT INTO `updates` SET for_user_id = '{$v}', from_user_id = '{$this->user_id}', type = '8', date = '{time()}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                            mozg_create_cache("user_{$v}/updates", 1);
                        }
                        mozg_clear_cache_file('user_' . $v . '/im');
                        mozg_create_cache('user_' . $v . '/im_update', '1');
                        mozg_create_cache("user_{$v}/typograf{$this->user_id}", "");
                    }
                    $check_im = $this->db->super_query("SELECT id FROM `im` WHERE iuser_id = '" . $this->user_id . "' AND im_user_id = '" . $for_user_id . "' AND room_id = '" . $room_id . "'");
                    if (!$check_im) {
                        $this->db->query("INSERT INTO im SET iuser_id = '" . $this->user_id . "', im_user_id = '" . $for_user_id . "', room_id = '" . $room_id . "', idate = '" . time() . "', all_msg_num = 1");
                    } else {
                        $this->db->query("UPDATE im  SET idate = '" . time() . "', all_msg_num = all_msg_num+1 WHERE id = '" . $check_im['id'] . "'");
                    }
                    return array(
                        'status' => Status::OK,
                        'id' => $dbid,
                        'user_photo' => $user_info['user_photo'],
                        'user_name' => $user_info['user_search_pref']
                    );
                }
                return array(
                    'status' => Status::PRIVACY
                );
            }
            return array(
                'status' => Status::NOT_USER
            );
        }
        return array(
            'status' => Status::NOT_VALID
        );

    }

    /**
     * @param int $msg_id
     * @return bool "false" - если не найдено
     */
    final public function read(int $msg_id): bool
    {
        $check = $this->db->super_query("SELECT id, id2, date, room_id, history_user_id, room_id, read_ids, user_ids FROM `messages` WHERE id = '" . $msg_id . "' and find_in_set('{$this->user_id}', user_ids) AND not find_in_set('{$this->user_id}', del_ids) AND not find_in_set('{$this->user_id}', read_ids) and history_user_id != '{$this->user_id}'");
        if ($check) {
            $read_ids = explode(',', $check['read_ids']);
            $read_ids[] = $this->user_id;
            $this->db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$check['id']}'");
            $this->db->query("UPDATE `users` SET user_pm_num = user_pm_num-1 WHERE user_id = '" . $this->user_id . "'");
            if (!$check['room_id']) {
                $user_ids = explode(',', $check['user_ids']);
                $im_user_id = $user_ids[0] == $this->user_id ? $user_ids[1] : $user_ids[0];
            } else {
                $im_user_id = 0;
            }
            $this->db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $this->user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $check['room_id'] . "'");
            mozg_clear_cache_file('user_' . $check['history_user_id'] . '/im');
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
                mozg_create_cache("user_{$for_user_id}/typograf{$this->user_id}", "");
                return true;
            }
            if ($action === 'stop') {
                mozg_create_cache("user_{$for_user_id}/typograf{$this->user_id}", 1);
                return true;
            }
            throw new \ErrorException('not action');
        }
        return false;
    }

    final public function delete(int $room_id, int $im_user_id): bool
    {
        if ($room_id > 0) {
            $im_user_id = 0;
        }
        $row = $this->db->super_query("SELECT id, msg_num, all_msg_num FROM `im` WHERE iuser_id = '{$this->user_id}' AND im_user_id = '{$im_user_id}' AND room_id = '{$room_id}'");
        if ($row) {
            $sql = $this->db->super_query("SELECT id, read_ids, room_id, history_user_id, del_ids FROM `messages` WHERE " . ($room_id ? "room_id = '{$room_id}'" : "room_id = 0 and find_in_set('{$im_user_id}', tb1.user_ids)") . " and find_in_set('{$user_id}', user_ids) AND not find_in_set('{$user_id}', del_ids)");
            if ($sql) {
                foreach ($sql as $row2) {
                    $del_ids = $row2['del_ids'] ? explode(',', $row2['del_ids']) : array();
                    $del_ids[] = $user_id;
                    $del_ids = implode(',', $del_ids);
                    $this->db->query("UPDATE `messages` SET del_ids = '{$del_ids}' WHERE id = '{$row2['id']}'");
                    $read_ids = explode(',', $row2['read_ids']);
                    if ($row['history_user_id'] !== $user_id && !in_array($user_id, $read_ids, true)) {
                        $read_ids[] = $user_id;
                        $this->db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$row2['id']}'");
                        $this->db->query("UPDATE `users` SET user_pm_num = user_pm_num-1 WHERE user_id = '" . $user_id . "'");
                        if (!$row2['room_id']) {
                            $user_ids = explode(',', $row2['user_ids']);
                            $im_user_id = $user_ids[0] === $user_id ? $user_ids[1] : $user_ids[0];
                        } else {
                            $im_user_id = 0;
                        }
                        $this->db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $row2['room_id'] . "'");
                        mozg_clear_cache_file('user_' . $row2['history_user_id'] . '/im');
                    }
                }
            }
            if ($row['msg_num']) {
                $this->db->query("UPDATE `users` SET user_pm_num = user_pm_num-{$row['msg_num']} WHERE user_id = '{$user_id}'");
            }
            $this->db->query("DELETE FROM `im` WHERE id = '{$row['id']}'");
            return true;
        }
        return false;
    }
}