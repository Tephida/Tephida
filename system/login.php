<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use Mozg\classes\Registry;
use Mozg\classes\TpLSite;

$_IP = $_SERVER['REMOTE_ADDR'];
$_BROWSER = $_SERVER['HTTP_USER_AGENT'];
//Если делаем выход
$act = requestFilter('act');
$db = Registry::get('db');
$config = settings_get();

if ($act == 'logout') {
    set_cookie("user_id", "", 0);
    set_cookie("password", "", 0);
    set_cookie("hid", "", 0);
    unset($_SESSION['user_id']);
    session_destroy();
    session_unset();
    $logged = false;
    Registry::set('logged', false);
    $user_info = array();
    header('Location: /');
}
//Если есть данные сессии
if (isset($_SESSION['user_id']) > 0) {
    $logged = true;
    Registry::set('logged', true);
    $logged_user_id = intval($_SESSION['user_id']);
    $user_info = $db->super_query("SELECT user_id, user_email, user_group, user_friends_demands, user_pm_num, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, invties_pub_num FROM `users` WHERE user_id = '" . $logged_user_id . "'");
    //Если есть данные о сессии, но нет информации о юзере, то выкидываем его
    if (!$user_info['user_id'])
        header('Location: /index.php?act=logout');

    Registry::set('user_info', $user_info);

    //Если юзер нажимает "Главная", и он зашел не с моб версии. То скидываем на его стр.
    $host_site = $_SERVER['QUERY_STRING'];
    if (!$host_site and $config['temp'] != 'mobile')
        header('Location: /u' . $user_info['user_id']);
    //Если есть данные о COOKIE, то проверяем

} elseif (isset($_COOKIE['user_id']) > 0 AND $_COOKIE['password'] AND $_COOKIE['hid']) {
    $cookie_user_id = intval($_COOKIE['user_id']);
    $user_info = $db->super_query("SELECT user_id, user_email, user_group, user_password, user_hid, user_friends_demands, user_pm_num, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, user_new_mark_photos, user_search_pref, user_status, user_last_visit, invties_pub_num FROM `users` WHERE user_id = '" . $cookie_user_id . "'");
    //Если пароль и HID совпадает, то пропускаем
    if ($user_info['user_password'] == $_COOKIE['password'] AND $user_info['user_hid'] == $_COOKIE['password'] . md5(md5($_IP))) {
        $_SESSION['user_id'] = $user_info['user_id'];
        //Вставляем лог в бд
        $db->query("UPDATE `log` SET browser = '" . $_BROWSER . "', ip = '" . $_IP . "' WHERE uid = '" . $user_info['user_id'] . "'");
        //Удаляем все рание события
        $db->query("DELETE FROM `updates` WHERE for_user_id = '{$user_info['user_id']}'");
        $logged = true;
        Registry::set('logged', true);
        Registry::set('user_info', $user_info);
    } else {
        $user_info = array();
        $logged = false;
        Registry::set('logged', false);
    }
    //Если юзер нажимает "Главная" и он зашел не с моб версии, то скидываем на его стр.
    $host_site = $_SERVER['QUERY_STRING'];
    if ($logged AND !$host_site AND $config['temp'] != 'mobile')
        header('Location: /u' . $user_info['user_id']);
} else {
    $user_info = array();
    $logged = false;
    Registry::set('logged', false);
}
//Если данные поступили через пост и пользователь не авторизован
if (isset($_POST['log_in']) && !$logged) {
    //Приготавливаем данные
    $email = requestFilter('email');
    $password = md5(md5(stripslashes($_POST['password'])));
    //Проверяем правильность e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        msgbox('', $lang['not_loggin'] . '<br /><a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>', 'info_red');
    } else {
        //Считаем кол-во символов в пароле и email
        if (!empty($email)) {
            $check_user = $db->super_query("SELECT user_id FROM `users` WHERE user_email = '" . $email . "' AND user_password = '" . $password . "'");
            //Если есть юзер то пропускаем
            if ($check_user) {
                //Hash ID
                $hid = $password . md5(md5($_IP));
                //Обновляем хэш входа
                $db->query("UPDATE `users` SET user_hid = '" . $hid . "' WHERE user_id = '" . $check_user['user_id'] . "'");
                //Удаляем все ранние события
                $db->query("DELETE FROM `updates` WHERE for_user_id = '{$check_user['user_id']}'");
                //Устанавливаем в сессию ИД юзера
                $_SESSION['user_id'] = (int)$check_user['user_id'];
                //Записываем COOKIE
                set_cookie("user_id", (int)$check_user['user_id'], 365);
                set_cookie("password", $password, 365);
                set_cookie("hid", $hid, 365);
                //Вставляем лог в бд
                $db->query("UPDATE `log` SET browser = '" . $_BROWSER . "', ip = '" . $_IP . "' WHERE uid = '" . $check_user['user_id'] . "'");
                if ($config['temp'] != 'mobile') {
                    header('Location: /u' . $check_user['user_id']);
                } else {
                    header('Location: /');
                }
            } else {
                $tpl = new TpLSite(ROOT_DIR . '/templates/' . $config['temp']);
                $tpl->load_template('info_red.tpl');
                $tpl->set('{error}', '<a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>');
                $tpl->compile('content');
                $tpl->render();

//                msgbox('', $lang['not_loggin'] . '<br /><br /><a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>', 'info_red');
            }
        } else {
            $tpl = new TpLSite(ROOT_DIR . '/templates/' . $config['temp']);
            $tpl->load_template('info_red.tpl');
            $tpl->set('{error}', '<a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>');
            $tpl->compile('content');
            $tpl->render();

//            msgbox('', $lang['not_loggin'] . '<br /><br /><a href="/restore" onClick="Page.Go(this.href); return false">Забыли пароль?</a>', 'info_red');
        }
    }
}