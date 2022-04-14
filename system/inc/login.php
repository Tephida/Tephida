<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Cookie;
use FluffyDollop\Support\Registry;
use Mozg\classes\TplCp;
use Mozg\Models\Users;

header('Content-type: text/html; charset=utf-8');

$config = settings_get();
$admin_index = $config['admin_index'];
$admin_link = $config['home_url'] . $admin_index;

$db = require ENGINE_DIR . '/data/db.php';
Registry::set('db', $db);

$_IP = $_SERVER['REMOTE_ADDR'];
$_BROWSER = $_SERVER['HTTP_USER_AGENT'];

//Если делаем выход
if (isset($_GET['act']) && $_GET['act'] === 'logout') {
    Cookie::remove('user_id');
    Cookie::remove('password');
    Cookie::remove('hid');
    unset($_SESSION['user_id']);
    session_destroy();
    session_unset();
    header('Location: /');
//    session_destroy();
//    session_unset();
//    $logged = false;
//    Registry::set('logged', false);
//    $user_info = array();

//    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
//    $tpl->load_template('login.tpl');
//    $tpl->set('{error_log}', '');
//    $tpl->set('{admin_link}', $admin_link);
//    $tpl->compile('content');
//    $tpl->render();

//    header("Location: {$admin_link}");
//    echo '1';

    exit();
}

//Если есть данные сесии
if (isset($_SESSION['user_id']) > 0) {
    $logged = true;
    Registry::set('logged', true);
    $logged_user_id = (int)$_SESSION['user_id'];
    $user_info = Users::login($logged_user_id, 'control_panel');

//Если есть данные о COOKIE то проверяем
} elseif (isset($_COOKIE['user_id']) > 0 && $_COOKIE['password'] && $_COOKIE['hid']) {
    $cookie_user_id = (int)$_COOKIE['user_id'];
    $user_info = $db->super_query("SELECT user_id, user_email, user_group, user_password, user_hid 
FROM `users` WHERE user_id = '" . $cookie_user_id . "' AND user_group = '1'");
    $user_info = Users::login($cookie_user_id, 'control_panel');
    //Если пароль и HID совпадает то пропускаем
    if (($user_info['user_password'] === $_COOKIE['password']) &&
        ($user_info['user_hid'] === $_COOKIE['password'] . md5(md5($_IP)))) {
        $_SESSION['user_id'] = $user_info['user_id'];

        //Вставляем лог в бд
        $db->query("UPDATE `log` SET browser = '" . $_BROWSER . "', ip = '" . $_IP . "' 
        WHERE uid = '" . $user_info['user_id'] . "'");

        $logged = true;
        Registry::set('logged', true);
    } else {
        $user_info = [];
        $logged = false;
        Registry::set('logged', false);
    }
} else {
    $user_info = [];
    $logged = false;
    Registry::set('logged', false);
}

//Если данные поступили через пост и пользователь не авторизован
if (isset($_POST['log_in']) && !isset($_SESSION['user_id'])) {
    //Приготавливаем данные
    $email = (new \FluffyDollop\Http\Request)->filter('email');
    $password = stripslashes($_POST['pass']);

    //Проверяем правильность e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_log = 'Доступ отключён!';
    } elseif (strlen($password) >= 0 && strlen($email) > 0) {
        $md5_pass = md5(md5($password));
        $check_user = $db->super_query("SELECT user_id FROM `users` 
            WHERE user_email = '" . $email . "' AND user_password = '" . $md5_pass . "' AND user_group = 1");

        //Если есть юзер то пропускаем
        if ($check_user) {
            //Hash ID
            $hid = $md5_pass . md5(md5($_IP));

            //Устанавливаем в сессию ИД юзера
            $_SESSION['user_id'] = (int)$check_user['user_id'];

            //Обновляем хэш входа
            $db->query("UPDATE `users` SET user_hid = '" . $hid . "' WHERE user_id = '" . $check_user['user_id'] . "'");

            //Записываем COOKIE
            Cookie::append('user_id', (int)$check_user['user_id'], 365);
            Cookie::append('password', $md5_pass, 365);
            Cookie::append('hid', $hid, 365);
            header("Location: {$admin_link}");
        } else {
            $error_log = 'Доступ отключён!';
        }
    } else {
        $error_log = 'Доступ отключён!';
    }
} else {
    $error_log = '';
}

if (!$logged) {
    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
    $tpl->load_template('login.tpl');
    $tpl->set('{error_log}', $error_log ?? '');
    $tpl->set('{admin_link}', $admin_link);
    $tpl->compile('content');
    $tpl->render();
} elseif ($user_info['user_group'] == 1) {
    include ADMIN_DIR . '/mod.php';
} else {
    $config = settings_get();

    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
    $tpl->load_template('info/info_red.tpl');
    $tpl->set('{error}', 'У вас недостаточно прав для просмотра этого раздела. <a href="'
        . $admin_link . '?act=logout">Выйти</a>');
    $tpl->set('{admin_link}', $admin_link);
    $tpl->set('{title}', 'Информация');
    $tpl->compile('content');
    $tpl->render();
}