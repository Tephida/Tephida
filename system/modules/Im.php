<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use Mozg\classes\DB;
use Mozg\classes\Module;

class Im extends Module
{
    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    final public function main(): bool
    {
        $lang = $this->lang;
        $db = $this->db;
        $user_info = $this->user_info;
        $logged = $this->logged;
        $params = [];
        if ($logged) {
            $user_id = $user_info['user_id'];

            //################### Вывод всех диалогов ###################//
            $params['title'] = 'Диалоги';

            //Вывод диалогов
            $sql_ = DB::getDB()->run("SELECT tb1.msg_num, im_user_id, tb2.user_search_pref, user_photo 
                FROM `im` tb1, `users` tb2 WHERE tb1.iuser_id = ? AND tb1.im_user_id = tb2.user_id ORDER by `idate` DESC LIMIT 0, 50", $user_id);
            foreach ($sql_ as $key => $row) {
                $sql_[$key]['name'] = $row['user_search_pref'];
                $sql_[$key]['uid'] = $row['im_user_id'];
                if ($row['user_photo']) {
                    $sql_[$key]['ava'] = '/uploads/users/' . $row['im_user_id'] . '/50_' . $row['user_photo'];
                } else {
                    $sql_[$key]['ava'] = '/images/no_ava_50.png';
                }
                if ($row['msg_num']) {
                    $sql_[$key]['msg_num'] = '<div class="im_new fl_l" id="msg_num' . $row['im_user_id'] . '">' . $row['msg_num'] . '</div>';
                } else {
                    $sql_[$key]['msg_num'] = '';
                }
            }
            $params['dialog'] = $sql_;

            //header сообщений
            $params['inbox'] = true;
            $params['outbox'] = false;
            $params['review'] = false;
            return view('im.im', $params);
        }

        $params['title'] = $lang['no_infooo'];
        $params['info'] = $lang['not_logged'];
        return view('info.info', $params);
    }
}