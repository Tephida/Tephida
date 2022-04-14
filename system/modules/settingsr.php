<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;
use FluffyDollop\Support\Registry;
use Mozg\classes\Cache;

NoAjaxQuery();

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $act = (new Request)->filter('act');
//    $metatags['title'] = $lang['settings'];
    $server_time = Registry::get('server_time');

    switch ($act) {
        /** Изменение пароля */
        case 'newpass':
            NoAjaxQuery();
            $old_pass = md5(md5((new Request)->filter('old_pass')));
            $new_pass = md5(md5((new Request)->filter('new_pass')));
            $new_pass2 = md5(md5((new Request)->filter('new_pass2')));
            //Выводим текущий пароль
            /** @var array $row */
            $row = $db->super_query("SELECT user_password FROM `users` WHERE user_id = '{$user_id}'");
            if ($row['user_password'] == $old_pass) {
                if ($new_pass == $new_pass2) {
                    $db->query("UPDATE `users` SET user_password = '{$new_pass2}' WHERE user_id = '{$user_id}'");
                } else {
                    echo '2';
                }
            } else {
                echo '1';
            }

            break;

        /** Изменение имени */
        case "newname":
            NoAjaxQuery();
            $user_name = (new Request)->filter('name');
            $user_lastname = ucfirst((new Request)->filter('lastname'));
            //Проверка имени
            if (!empty($user_name)) {
                if (strlen($user_name) >= 2) {
                    if (!preg_match("/^[a-zA-Zа-яА-Я]+$/iu", $user_name)) {
                        $errors = 3;
                    }
                } else {
                    $errors = 2;
                }
            } else {
                $errors = 1;
            }
            //Проверка фамилии
            if (!empty($user_lastname)) {
                if (strlen($user_lastname) >= 2) {
                    if (!preg_match("/^[a-zA-Zа-яА-Я]+$/iu", $user_lastname)) {
                        $errors_lastname = 3;
                    }
                } else {
                    $errors_lastname = 2;
                }
            } else {
                $errors_lastname = 1;
            }
            if (!isset($errors)) {
                if (!isset($errors_lastname)) {
                    $user_name = ucfirst($user_name);
                    $user_lastname = ucfirst($user_lastname);
                    $db->query("UPDATE `users` SET user_name = '{$user_name}', user_lastname = '{$user_lastname}', user_search_pref = '{$user_name} {$user_lastname}' WHERE user_id = '{$user_id}'");
                    Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    Cache::mozgClearCache();
                }
            } else {
                echo $errors;
            }
            break;

        /** Сохранение настроек приватности */
        case "saveprivacy":
            NoAjaxQuery();
            $val_msg = (new Request)->int('val_msg');
            $val_wall1 = (new Request)->int('val_wall1');
            $val_wall2 = (new Request)->int('val_wall2');
            $val_wall3 = (new Request)->int('val_wall3');
            $val_info = (new Request)->int('val_info');
            if ($val_msg <= 0 || $val_msg > 3) $val_msg = 1;
            if ($val_wall1 <= 0 || $val_wall1 > 3) $val_wall1 = 1;
            if ($val_wall2 <= 0 || $val_wall2 > 3) $val_wall2 = 1;
            if ($val_wall3 <= 0 || $val_wall3 > 3) $val_wall3 = 1;
            if ($val_info <= 0 || $val_info > 3) $val_info = 1;
            $user_privacy = "val_msg|{$val_msg}||val_wall1|{$val_wall1}||val_wall2|{$val_wall2}||val_wall3|{$val_wall3}||val_info|{$val_info}||";
            $db->query("UPDATE `users` SET user_privacy = '{$user_privacy}' WHERE user_id = '{$user_id}'");
            Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);

            break;

        /** Приватность настройки */
        case "privacy":
            $sql_ = $db->super_query("SELECT user_privacy FROM `users` WHERE user_id = '{$user_id}'");
            $row = xfieldsdataload($sql_['user_privacy']);

            $meta_tags['title'] = 'Приватность настройки';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            $tpl->load_template('settings/privacy.tpl');
            $tpl->set('{val_msg}', $row['val_msg']);
            $tpl->set('{val_msg_text}', strtr($row['val_msg'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Никто')));
            $tpl->set('{val_wall1}', $row['val_wall1']);
            $tpl->set('{val_wall1_text}', strtr($row['val_wall1'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
            $tpl->set('{val_wall2}', $row['val_wall2']);
            $tpl->set('{val_wall2_text}', strtr($row['val_wall2'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
            $tpl->set('{val_wall3}', $row['val_wall3']);
            $tpl->set('{val_wall3_text}', strtr($row['val_wall3'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
            $tpl->set('{val_info}', $row['val_info']);
            $tpl->set('{val_info_text}', strtr($row['val_info'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
            $tpl->compile('info');

            $tpl->render();
            break;

        /** Добавление в черный список */
        case "addblacklist":
            NoAjaxQuery();
            $bad_user_id = (new Request)->int('bad_user_id');
            //Проверяем на существование юзера
            $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$bad_user_id}'");
            //Выводим свой блек лист для проверки
            $myRow = $db->super_query("SELECT user_blacklist FROM `users` WHERE user_id = '{$user_id}'");
            $array_blacklist = explode('|', $myRow['user_blacklist']);
            if ($row['cnt'] and !in_array($bad_user_id, $array_blacklist) and $user_id != $bad_user_id) {
                $db->query("UPDATE `users` SET user_blacklist_num = user_blacklist_num+1, user_blacklist = '{$myRow['user_blacklist']}|{$bad_user_id}|' WHERE user_id = '{$user_id}'");
                //Если юзер есть в др.
                if (CheckFriends($bad_user_id)) {
                    //Удаляем друга из таблицы друзей
                    $db->query("DELETE FROM `friends` WHERE user_id = '{$user_id}' AND friend_id = '{$bad_user_id}' AND subscriptions = 0");
                    //Удаляем у друга из таблицы
                    $db->query("DELETE FROM `friends` WHERE user_id = '{$bad_user_id}' AND friend_id = '{$user_id}' AND subscriptions = 0");
                    //Обновляем кол-друзей у юзера
                    $db->query("UPDATE `users` SET user_friends_num = user_friends_num-1 WHERE user_id = '{$user_id}'");
                    //Обновляем у друга которого удаляем кол-во друзей
                    $db->query("UPDATE `users` SET user_friends_num = user_friends_num-1 WHERE user_id = '{$bad_user_id}'");
                    //Чистим кеш владельцу стр и тому кого удаляем из др.
                    Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    Cache::mozgClearCacheFile('user_' . $bad_user_id . '/profile_' . $bad_user_id);
                    //Удаляем пользователя из кеш файл друзей
                    $openMyList = Cache::mozgCache("user_{$user_id}/friends");
                    Cache::mozgCreateCache("user_{$user_id}/friends", str_replace("u{$bad_user_id}|", "", $openMyList));
                    $openTakeList = Cache::mozgCache("user_{$bad_user_id}/friends");
                    Cache::mozgCreateCache("user_{$bad_user_id}/friends", str_replace("u{$user_id}|", "", $openTakeList));
                }
                $openMyList = Cache::mozgCache("user_{$user_id}/blacklist");
                Cache::mozgCreateCache("user_{$user_id}/blacklist", $openMyList . "|{$bad_user_id}|");
            }

            break;

        /** Удаление из черного списка */
        case "delblacklist":
            NoAjaxQuery();
            $bad_user_id = (new Request)->int('bad_user_id');
            //Проверяем на существование юзера
            $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$bad_user_id}'");
            //Выводим свой блеклист для проверка
            $myRow = $db->super_query("SELECT user_blacklist FROM `users` WHERE user_id = '{$user_id}'");
            $array_blacklist = explode('|', $myRow['user_blacklist']);
            if ($row['cnt'] and in_array($bad_user_id, $array_blacklist) and $user_id != $bad_user_id) {
                $myRow['user_blacklist'] = str_replace("|{$bad_user_id}|", "", $myRow['user_blacklist']);
                $db->query("UPDATE `users` SET user_blacklist_num = user_blacklist_num-1, user_blacklist = '{$myRow['user_blacklist']}' WHERE user_id = '{$user_id}'");
                $openMyList = Cache::mozgCache("user_{$user_id}/blacklist");
                Cache::mozgCreateCache("user_{$user_id}/blacklist", str_replace("|{$bad_user_id}|", "", $openMyList));
            }

            break;

        /** Черный список */
        case "blacklist":
            $meta_tags['title'] = 'Черный список';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            $row = $db->super_query("SELECT user_blacklist, user_blacklist_num FROM `users` WHERE user_id = '{$user_id}'");
            $tpl->load_template('settings/blacklist.tpl');
            $tpl->set('{cnt}', '<span id="badlistnum">' . $row['user_blacklist_num'] . '</span> ' . declWord($row['user_blacklist_num'], 'fave'));
            if ($row['user_blacklist_num']) {
                $tpl->set('[yes-users]', '');
                $tpl->set('[/yes-users]', '');
            } else $tpl->set_block("'\\[yes-users\\](.*?)\\[/yes-users\\]'si", "");
            $tpl->compile('info');
            if ($row['user_blacklist_num'] and $row['user_blacklist_num'] <= 100) {
                $tpl->load_template('settings/baduser.tpl');
                $array_blacklist = explode('|', $row['user_blacklist']);
                foreach ($array_blacklist as $user) {
                    if ($user) {
                        $infoUser = $db->super_query("SELECT user_photo, user_search_pref FROM `users` WHERE user_id = '{$user}'");
                        if ($infoUser['user_photo']) $tpl->set('{ava}', '/uploads/users/' . $user . '/50_' . $infoUser['user_photo']);
                        else $tpl->set('{ava}', '/images/no_ava_50.png');
                        $tpl->set('{name}', $infoUser['user_search_pref']);
                        $tpl->set('{user-id}', $user);
                        $tpl->compile('content');
                    }
                }
            } else msgbox('', $lang['settings_nobaduser'], 'info_2');

            $tpl->render();
            break;

        /** Смена e-mail */
        case "change_mail":
            //Отправляем письмо на обе почты
            include_once ENGINE_DIR . '/classes/mail.php';
            $config = settings_get();
            $mail = new \FluffyDollop\Support\ViiMail($config);
            $email = (new Request)->filter('email', 25000, true);
            //Проверка E-mail
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
                $ok_email = true;
            else
                $ok_email = false;
            $row = $db->super_query("SELECT user_email FROM `users` WHERE user_id = '{$user_id}'");
            $check_email = $db->super_query("SELECT COUNT(*) AS cnt FROM `users`  WHERE user_email = '{$email}'");
            if ($row['user_email'] and $ok_email and !$check_email['cnt']) {
                //Удаляем все пред. заявки
                $db->query("DELETE FROM `restore` WHERE email = '{$email}'");
                $salt = "abchefghjkmnpqrstuvwxyz0123456789";
                $rand_lost = '';
                for ($i = 0; $i < 15; $i++) {
                    $rand_lost .= $salt[random_int(0, 33)];
                }
                $hash = md5($server_time . $row['user_email'] . random_int(0, 100000) . $rand_lost);
                $message = <<<HTML
Вы получили это письмо, так как зарегистрированы на сайте
{$config['home_url']} и хотите изменить основной почтовый адрес.
Вы желаете изменить почтовый адрес с текущего ({$row['user_email']}) на {$email}
Для того чтобы Ваш основной e-mail на сайте {$config['home_url']} был
изменен, Вам необходимо пройти по ссылке:
{$config['home_url']}index.php?go=settings&code1={$hash}

Внимание: не забудьте, что после изменения почтового адреса при входе
на сайт Вам нужно будет указывать новый адрес электронной почты.

Если Вы не посылали запрос на изменение почтового адреса,
проигнорируйте это письмо.С уважением,
Администрация {$config['home_url']}
HTML;
                $mail->send($row['user_email'], 'Изменение почтового адреса', $message);
                //Вставляем в БД код 1
                $db->query("INSERT INTO `restore` SET email = '{$email}', hash = '{$hash}', ip = '{$_IP}'");
                $salt = "abchefghjkmnpqrstuvwxyz0123456789";
                for ($i = 0; $i < 15; $i++) {
                    $rand_lost .= $salt[random_int(0, 33)];
                }
                $hash = md5($server_time . $row['user_email'] . random_int(0, 300000) . $rand_lost);
                $message = <<<HTML
Вы получили это письмо, так как зарегистрированы на сайте
{$config['home_url']} и хотите изменить основной почтовый адрес.
Вы желаете изменить почтовый адрес с текущего ({$row['user_email']}) на {$email}
Для того чтобы Ваш основной e-mail на сайте {$config['home_url']} был
изменен, Вам необходимо пройти по ссылке:
{$config['home_url']}index.php?go=settings&code2={$hash}

Внимание: не забудьте, что после изменения почтового адреса при входе
на сайт Вам нужно будет указывать новый адрес электронной почты.

Если Вы не посылали запрос на изменение почтового адреса,
проигнорируйте это письмо.С уважением,
Администрация {$config['home_url']}
HTML;
                $mail->send($email, 'Изменение почтового адреса', $message);
                //Вставляем в БД код 2
                $db->query("INSERT INTO `restore` SET email = '{$email}', hash = '{$hash}', ip = '{$_IP}'");
            } else {
                echo '1';
            }

            break;

        //################### Оповещения ###################//
        case 'notify':
            $meta_tags['title'] = 'Общие настройки';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            /** @var array $row */
            $row = $db->super_query("SELECT notify FROM `users` WHERE user_id = '{$user_id}'");
            $tpl->load_template('settings/notify.tpl');
//            $block_data = xfieldsdataload($row['notify']);
            $arrlist = ['n_friends', 'n_wall', 'n_comm', 'n_comm_ph', 'n_comm_note', 'n_gifts', 'n_rec', 'n_im'];
            foreach($arrlist as $p){
//                if($block_data[$p]) {
//                    $tpl->set("{{$p}}", $p);
//                }
//                else {
//                    $tpl->set("{{$p}}", '0');
//                }
            }
            $tpl->compile('content');
            $tpl->render();
            echo 'ttt';
            break;

        /** Общие настройки */

        default:
            $mobile_speedbar = 'Общие настройки';
            $meta_tags['title'] = 'Общие настройки';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            $row = $db->super_query("SELECT user_name, user_lastname, user_email FROM `users` WHERE user_id = '{$user_id}'");
            //Загружаем вверх
            $tpl->load_template('settings/general.tpl');
            $tpl->set('{name}', $row['user_name']);
            $tpl->set('{lastname}', $row['user_lastname']);
            $tpl->set('{id}', $user_id);
            //Завершении смены E-mail
            $tpl->set('{code-1}', 'no_display');
            $tpl->set('{code-2}', 'no_display');
            $tpl->set('{code-3}', 'no_display');
            $code1 = strip_data((new Request)->filter('code1'));
            $code2 = strip_data((new Request)->filter('code2'));
            if (strlen($code1) == 32) {
                $code2 = '';
                $check_code1 = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$code1}' AND ip = '{$_IP}'");
                if ($check_code1['email']) {
                    $check_code2 = $db->super_query("SELECT COUNT(*) AS cnt FROM `restore` WHERE hash != '{$code1}' AND email = '{$check_code1['email']}' AND ip = '{$_IP}'");
                    if ($check_code2['cnt']) $tpl->set('{code-1}', '');
                    else {
                        $tpl->set('{code-1}', 'no_display');
                        $tpl->set('{code-3}', '');
                        //Меняем
                        $db->query("UPDATE `users` SET user_email = '{$check_code1['email']}' WHERE user_id = '{$user_id}'");
                        $row['user_email'] = $check_code1['email'];
                    }
                    $db->query("DELETE FROM `restore` WHERE hash = '{$code1}' AND ip = '{$_IP}'");
                }
            }
            if (strlen($code2) == 32) {
                $check_code2 = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$code2}' AND ip = '{$_IP}'");
                if ($check_code2['email']) {
                    $check_code1 = $db->super_query("SELECT COUNT(*) AS cnt FROM `restore` WHERE hash != '{$code2}' AND email = '{$check_code2['email']}' AND ip = '{$_IP}'");
                    if ($check_code1['cnt']) {
                        $tpl->set('{code-2}', '');
                    } else {
                        $tpl->set('{code-2}', 'no_display');
                        $tpl->set('{code-3}', '');
                        //Меняем
                        $db->query("UPDATE `users` SET user_email = '{$check_code2['email']}'  WHERE user_id = '{$user_id}'");
                        $row['user_email'] = $check_code2['email'];
                    }
                    $db->query("DELETE FROM `restore` WHERE hash = '{$code2}' AND ip = '{$_IP}'");
                }
            }
            //Email
            $substre = substr($row['user_email'], 0, 1);
            $epx1 = explode('@', $row['user_email']);
            $tpl->set('{email}', $substre . '*******@' . $epx1[1]);
            $tpl->compile('info');

            $tpl->render();
    }
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}