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

if ($ajax == 'yes')
    NoAjaxQuery();

if (Registry::get('logged') == false) {
    $db = Registry::get('db');
    $act = requestFilter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $metatags['title'] = $lang['restore_title'];
    $server_time = Registry::get('server_time');

    switch ($act) {

        //################### Проверка данных на восстановления ###################//
        case "next":
            NoAjaxQuery();
            $email = requestFilter('email');
            $check = $db->super_query("SELECT user_id, user_search_pref, user_photo FROM `users` WHERE user_email = '{$email}'");
            if ($check) {
                if ($check['user_photo'])
                    $check['user_photo'] = "/uploads/users/{$check['user_id']}/50_{$check['user_photo']}";
                else
                    $check['user_photo'] = "{theme}/images/no_ava_50.png";

                echo $check['user_search_pref'] . "|" . $check['user_photo'];
            } else
                echo 'no_user';

            die();
            break;

        //################### Отправка данных на почту на восстановления ###################//
        case "send":
            NoAjaxQuery();
            $email = requestFilter('email');
            $check = $db->super_query("SELECT user_name FROM `users` WHERE user_email = '{$email}'");
            if ($check) {
                //Удаляем все предыдущие запросы на воостановление
                $db->query("DELETE FROM `restore` WHERE email = '{$email}'");

                $salt = "abchefghjkmnpqrstuvwxyz0123456789";
                $rand_lost = '';
                for ($i = 0; $i < 15; $i++) {
                    $rand_lost .= $salt[rand(0, 33)];
                }
                $hash = md5($server_time . $email . rand(0, 100000) . $rand_lost . $check['user_name']);

                //Вставляем в базу
                $db->query("INSERT INTO `restore` SET email = '{$email}', hash = '{$hash}', ip = '{$_IP}'");

                //Отправляем письмо на почту для восстановления
                include_once ENGINE_DIR . '/classes/mail.php';
                $mail = new vii_mail($config);
                $message = <<<HTML
Здравствуйте, {$check['user_name']}.

Чтобы сменить ваш пароль, пройдите по этой ссылке:
{$config['home_url']}restore?act=prefinish&h={$hash}

Мы благодарим Вас за участие в жизни нашего сайта.

{$config['home_url']}
HTML;
                $mail->send($email, $lang['lost_subj'], $message);
            }
            die();
            break;

        //################### Страница смены пароля ###################//
        case "prefinish":
            $hash = strip_data(requestFilter('h'));
            $row = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
            if ($row) {
                $info = $db->super_query("SELECT user_name FROM `users` WHERE user_email = '{$row['email']}'");
                $tpl->load_template('restore/prefinish.tpl');
                $tpl->set('{name}', $info['user_name']);

                $salt = "abchefghjkmnpqrstuvwxyz0123456789";
                $rand_lost = '';
                for ($i = 0; $i < 15; $i++) {
                    $rand_lost .= $salt[rand(0, 33)];
                }
                $newhash = md5($server_time . $row['email'] . rand(0, 100000) . $rand_lost);
                $tpl->set('{hash}', $newhash);
                $db->query("UPDATE `restore` SET hash = '{$newhash}' WHERE email = '{$row['email']}'");

                $tpl->compile('content');
            } else {
                $speedbar = $lang['no_infooo'];
                msgbox('', $lang['restore_badlink'], 'info');
            }
            break;

        //################### Смена пароля ###################//
        case "finish":
            NoAjaxQuery();
            $hash = strip_data(requestFilter('hash'));
            $row = $db->super_query("SELECT email FROM `restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
            if ($row) {

//				$_POST['new_pass'] = ajax_utf8($_POST['new_pass']);
//				$_POST['new_pass2'] = ajax_utf8($_POST['new_pass2']);

                $new_pass = md5(md5(requestFilter('new_pass')));
                $new_pass2 = md5(md5(requestFilter('new_pass2')));

                if (strlen($new_pass) >= 6 and $new_pass == $new_pass2) {
                    $db->query("UPDATE `users` SET user_password = '{$new_pass}' WHERE user_email = '{$row['email']}'");
                    $db->query("DELETE FROM `restore` WHERE email = '{$row['email']}'");
                }
            }
            die();
            break;

        default:
            $tpl->load_template('restore/main.tpl');
            $tpl->compile('content');
    }
    $tpl->clear();
    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
}