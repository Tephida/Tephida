<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG'))
    die('Hacking attempt!');

NoAjaxQuery();

if ($logged) {
    $act = requestFilter('act');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################### Страница всех подарков ###################//
        case "view":
            NoAjaxQuery();
            $for_user_id = intFilter('user_id');

            $sql_ = $db->super_query("SELECT gid, img, price FROM `gifts_list` ORDER by `gid` DESC", true);

            foreach ($sql_ as $gift) {

                if ($config['temp'] == 'mobile')

                    echo "<a href=\"\" class=\"gifts_onegif\" onClick=\"gifts.select('{$gift['img']}', '{$for_user_id}'); return false\"><img src=\"/uploads/gifts/{$gift['img']}.png\" /><div class=\"gift_count\" id=\"g{$gift['img']}\">{$gift['price']} голос</div></a>";

                else

                    echo "<a href=\"\" class=\"gifts_onegif\" onMouseOver=\"gifts.showgift('{$gift['img']}')\" onMouseOut=\"gifts.showhide('{$gift['img']}')\" onClick=\"gifts.select('{$gift['img']}', '{$for_user_id}'); return false\"><img src=\"/uploads/gifts/{$gift['img']}.png\" /><div class=\"gift_count no_display\" id=\"g{$gift['img']}\">{$gift['price']} голос</div></a>";

            }

            $row = $db->super_query("SELECT user_balance FROM `users` WHERE user_id = '{$user_id}'");

            echo "<style>#box_bottom_left_text{padding-top:6px;float:left}</style><script>$('#box_bottom_left_text').html('У Вас <b>{$row['user_balance']} голос.</b>&nbsp;');</script><div class=\"clr\"></div>";

            die();
            break;

        //################### Отправка подарка в БД ###################//
        case "send":
            NoAjaxQuery();
            $for_user_id = intFilter('for_user_id');
            $gift = intFilter('gift');
            $privacy = intFilter('privacy']);
            if ($privacy < 0 or $privacy > 3)
                $privacy = 1;
            $msg = requestFilter('msg');
            $gifts = $db->super_query("SELECT price FROM `gifts_list` WHERE img = '" . $gift . "'");

            //Выводим текущий баланс свой
            $row = $db->super_query("SELECT user_balance FROM `users` WHERE user_id = '{$user_id}'");
            if ($gifts['price'] and $user_id != $for_user_id) {
                if ($row['user_balance'] >= $gifts['price']) {
                    $db->query("INSERT INTO `gifts` SET uid = '{$for_user_id}', gift = '{$gift}', msg = '{$msg}', privacy = '{$privacy}', gdate = '{$server_time}', from_uid = '{$user_id}', status = 1");
                    $db->query("UPDATE `users` SET user_balance = user_balance-{$gifts['price']} WHERE user_id = '{$user_id}'");
                    $db->query("UPDATE `users` SET user_gifts = user_gifts+1 WHERE user_id = '{$for_user_id}'");

                    //Вставляем событие в моментальные оповещения
                    $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$for_user_id}'");

                    $update_time = $server_time - 70;

                    if ($check2['user_last_visit'] >= $update_time) {

                        if ($privacy == 3) {

                            $user_info['user_photo'] = '';
                            $user_info['user_search_pref'] = 'Неизвестный отправитель';
                            $from_user_id = $for_user_id;

                        } else
                            $from_user_id = $user_id;

                        $action_update_text = "<img src=\"/uploads/gifts/{$gift}.png\" width=\"50\" align=\"right\" />{$msg}";

                        $db->query("INSERT INTO `updates` SET for_user_id = '{$for_user_id}', from_user_id = '{$from_user_id}', type = '7', date = '{$server_time}', text = '{$action_update_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/gifts{$for_user_id}?new=1'");

                        mozg_create_cache("user_{$for_user_id}/updates", 1);

                        //ИНАЧЕ Добавляем +1 юзеру для оповещения
                    } else {

                        $cntCacheNews = mozg_cache("user_{$for_user_id}/new_gift");
                        mozg_create_cache("user_{$for_user_id}/new_gift", ($cntCacheNews + 1));

                    }

                    mozg_mass_clear_cache_file("user_{$for_user_id}/profile_{$for_user_id}|user_{$for_user_id}/gifts");

                    //Если цена подарка выше бонусного, то начисляем цену подарка на рейтинг
                    if ($gifts['price'] > $config['bonus_rate']) {

                        //Начисляем
                        $db->query("UPDATE `users` SET user_rating = user_rating + {$gifts['price']} WHERE user_id = '{$user_id}'");

                        //Чистим кеш
                        mozg_clear_cache_file("user_{$user_id}/profile_{$user_id}");

                    }

                    //Отправка уведомления на E-mail
                    if ($config['news_mail_6'] == 'yes') {
                        $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $for_user_id . "'");
                        if ($rowUserEmail['user_email']) {
                            include_once ENGINE_DIR . '/classes/mail.php';
                            $mail = new vii_mail($config);
                            $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
                            $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '6'");
                            $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
                            $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
                            $rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'] . 'gifts' . $for_user_id, $rowEmailTpl['text']);
                            $mail->send($rowUserEmail['user_email'], 'Вам отправили новый подарок', $rowEmailTpl['text']);
                        }
                    }
                } else
                    echo '1';
            }
            die();
            break;

        //################### Удаление подарка ###################//
        case "del":
            NoAjaxQuery();
            $gid = intFilter('gid']);
            $row = $db->super_query("SELECT uid FROM `gifts` WHERE gid = '{$gid}'");
            if ($user_id == $row['uid']) {
                $db->query("DELETE FROM `gifts` WHERE gid = '{$gid}'");
                $db->query("UPDATE `users` SET user_gifts = user_gifts-1 WHERE user_id = '{$user_id}'");
                mozg_mass_clear_cache_file("user_{$user_id}/profile_{$user_id}|user_{$user_id}/gifts");
            }
            die();
            break;

        default:

            //################### Всех подарков пользователя ###################//
            $metatags['title'] = $lang['gifts'];
            $uid = intFilter('uid');

            $page = intFilter('page', 1);
            $gcount = 15;
            $limit_page = ($page - 1) * $gcount;

            $owner = $db->super_query("SELECT user_name, user_gifts FROM `users` WHERE user_id = '{$uid}'");

            $tpl->load_template('gifts/head.tpl');
            $tpl->set('{uid}', $uid);
            if ($user_id == $uid) {
                $tpl->set('[owner]', '');
                $tpl->set('[/owner]', '');
                $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
            } else {
                $tpl->set('[not-owner]', '');
                $tpl->set('[/not-owner]', '');
                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
            }
            $tpl->set('{name}', gramatikName($owner['user_name']));
            $tpl->set('{gifts-num}', '<span id="num">' . $owner['user_gifts'] . '</span> ' . gram_record($owner['user_gifts'], 'gifts'));
            if ($owner['user_gifts']) {
                $tpl->set('[yes]', '');
                $tpl->set('[/yes]', '');
                $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
            } else {
                $tpl->set('[no]', '');
                $tpl->set('[/no]', '');
                $tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si", "");
            }

            if (requestFilter('new') and $user_id == $uid) {
                $tpl->set('[new]', '');
                $tpl->set('[/new]', '');
                $tpl->set_block("'\\[no-new\\](.*?)\\[/no-new\\]'si", "");
                $sql_where = "AND status = 1";
                $gcount = 50;
                mozg_create_cache("user_{$user_id}/new_gift", '');
            } else {
                $tpl->set('[no-new]', '');
                $tpl->set('[/no-new]', '');
                $tpl->set_block("'\\[new\\](.*?)\\[/new\\]'si", "");
            }

            $tpl->compile('info');
            if ($owner['user_gifts']) {
                $sql_ = $db->super_query("SELECT tb1.gid, gift, from_uid, msg, gdate, privacy, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `gifts` tb1, `users` tb2 WHERE tb1.uid = '{$uid}' AND tb1.from_uid = tb2.user_id {$sql_where} ORDER by `gdate` DESC LIMIT {$limit_page}, {$gcount}", true);
                $tpl->load_template('gifts/gift.tpl');
                foreach ($sql_ as $row) {
                    $tpl->set('{id}', $row['gid']);
                    $tpl->set('{uid}', $row['from_uid']);
                    if ($row['privacy'] == 1 or $user_id == $row['from_uid'] or $user_id == $uid and $row['privacy'] != 3) {
                        $tpl->set('{author}', $row['user_search_pref']);
                        $tpl->set('{msg}', stripslashes($row['msg']));
                        $tpl->set('[link]', '<a href="/u' . $row['from_uid'] . '" onClick="Page.Go(this.href); return false">');
                        $tpl->set('[/link]', '</a>');
                        OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);
                    } else {
                        $tpl->set('{author}', 'Неизвестный отправитель');
                        $tpl->set('{msg}', '');
                        $tpl->set('{online}', '');
                        $tpl->set('[link]', '');
                        $tpl->set('[/link]', '');
                    }
                    $tpl->set('{gift}', $row['gift']);
                    megaDate($row['gdate'], 1, 1);
                    $tpl->set('[privacy]', '');
                    $tpl->set('[/privacy]', '');
                    if ($row['privacy'] == 3 and $user_id == $uid) {
                        $tpl->set('{msg}', stripslashes($row['msg']));
                        $tpl->set_block("'\\[privacy\\](.*?)\\[/privacy\\]'si", "");
                    }
                    if ($row['privacy'] == 1 or $user_id == $row['from_uid'] or $user_id == $uid and $row['privacy'] != 3)
                        if ($row['user_photo'])
                            $tpl->set('{ava}', '/uploads/users/' . $row['from_uid'] . '/50_' . $row['user_photo']);
                        else
                            $tpl->set('{ava}', '{theme}/images/no_ava_50.png');
                    else
                        $tpl->set('{ava}', '{theme}/images/no_ava_50.png');

                    if ($user_id == $uid) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                    } else
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");

                    if ($sql_where)
                        $db->query("UPDATE `gifts` SET status = 0 WHERE gid = '{$row['gid']}'");

                    $tpl->compile('content');
                }
                navigation($gcount, $owner['user_gifts'], "/gifts{$uid}?page=");

                if ($sql_where and !$sql_)
                    msgbox('', '<br /><br />Новых подарков еще нет.<br /><br /><br />', 'info_2');
            }
    }
    $tpl->clear();
    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
}