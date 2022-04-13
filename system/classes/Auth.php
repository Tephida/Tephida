<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Mozg\classes;

use ErrorException;
use FluffyDollop\Http\Request;
use FluffyDollop\Support\{Cookie, Registry};
use JsonException;
use Mozg\Models\Users;

class Auth
{
    /**
     * @throws ErrorException|JsonException
     */
    final public function login(): mixed
    {
        $_IP = $_SERVER['REMOTE_ADDR'];
        $act = (new Request)->filter('act');
//Если делаем выход
        if ($act === 'logout') {
            Cookie::remove('user_id');
            Cookie::remove('password');
            Cookie::remove('hid');
            unset($_SESSION['user_id']);
            session_destroy();
            session_unset();
            header('Location: /');
        }
//Если есть данные сессии
        if (isset($_SESSION['user_id']) > 0) {
            $logged = true;
            Registry::set('logged', true);
            $logged_user_id = (int)$_SESSION['user_id'];
            $user_info = Users::login($logged_user_id, 'site');
//Если есть данные о сессии, но нет информации о юзере, то выкидываем его

            if (!$user_info['user_id']) {
                header('Location: /index.php?act=logout');
            }

            Registry::set('user_info', $user_info);
//Если юзер нажимает "Главная", и он зашел не с моб версии. То скидываем на его стр.
//            $host_site = $_SERVER['QUERY_STRING'];
//    if (!$host_site && $config['temp'] !== 'mobile') {
//        header('Location: /u' . $user_info['user_id']);
//    }
            //Если есть данные о COOKIE, то проверяем
        } elseif (isset($_COOKIE['user_id']) > 0 && $_COOKIE['password'] && $_COOKIE['hid']) {
            $cookie_user_id = (int)$_COOKIE['user_id'];
            $user_info = Users::login($cookie_user_id, 'site');
//Если пароль и HID совпадает, то пропускаем
            if ($user_info['user_password'] === $_COOKIE['password'] && $user_info['user_hid'] === $_COOKIE['password'] . md5(md5($_IP))) {
                $_SESSION['user_id'] = $user_info['user_id'];
                $device = get_device();
                $device_str = serialize($device);
//Вставляем лог в бд
//        $db->query("UPDATE `log` SET browser = '" . $device['browser'] . "', ip = '" . $_IP . "', device = '" . $device_str . "' WHERE uid = '" . $user_info['user_id'] . "'");

                DB::getDB()->update('log', [
                    'browser' => $device['browser'],
                    'ip' => $_IP,
                    'device' => $device_str,
                ], [
                    'uid' => $user_info['user_id']
                ]);

//Удаляем все ранние события
//        $db->query("DELETE FROM `updates` WHERE for_user_id = '{$user_info['user_id']}'");

                DB::getDB()->delete('updates', [
                    'for_user_id' => $user_info['user_id']
                ]);

                $logged = true;
                Registry::set('logged', true);
                Registry::set('user_info', $user_info);
            } else {
                $logged = false;
                Registry::set('logged', false);
            }
            //Если юзер нажимает "Главная" и он зашел не с моб версии, то скидываем на его стр.
//            $host_site = $_SERVER['QUERY_STRING'];
//            if ($logged && !$host_site && $config['temp'] !== 'mobile') {
//        header('Location: /u' . $user_info['user_id']);
//            }
        } else {
            $logged = false;
            Registry::set('logged', false);
        }
//Если данные поступили через пост и пользователь не авторизован
        if (isset($_POST['log_in']) && !$logged) {
//Приготавливаем данные
            $email = (new Request)->filter('email');
            $password = md5(md5(stripslashes((new Request)->filter('password'))));
//Проверяем правильность e-mail
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $params = [
                    'title' => 'Restore',
                ];
                view('auth.restore_error', $params);
                exit();
            }
            if (!empty($email)) {
                /** @var array $check_user user id */
//        $check_user = $db->super_query("SELECT user_id FROM `users` WHERE user_email = '" . $email . "' AND user_password = '" . $password . "'");
                $check_user = DB::getDB()->row('SELECT user_id FROM `users` WHERE user_email = ? AND user_password = ?', $email, $password);

//Если есть юзер то пропускаем
                if ($check_user) {
//Hash ID
                    $hid = $password . md5(md5($_IP));
//Обновляем хэш входа
//            $db->query("UPDATE `users` SET user_hid = '" . $hid . "' WHERE user_id = '" . $check_user['user_id'] . "'");

                    DB::getDB()->update('users', [
                        'user_hid' => $hid
                    ], [
                        'user_id' => $check_user['user_id']
                    ]);

//Удаляем все ранние события
//            $db->query("DELETE FROM `updates` WHERE for_user_id = '{$check_user['user_id']}'");

                    DB::getDB()->delete('updates', [
                        'for_user_id' => $check_user['user_id']
                    ]);

//Устанавливаем в сессию ИД юзера
                    $_SESSION['user_id'] = (int)$check_user['user_id'];
//Записываем COOKIE
                    Cookie::append('user_id', (string)$check_user['user_id'], 365);
                    Cookie::append('password', $password, 365);
                    Cookie::append('hid', $hid, 365);
                    $device = get_device();
                    $device_str = serialize($device);
//Вставляем лог в бд
//            $db->query("UPDATE `log` SET browser = '" . $device['browser'] . "', ip = '" . $_IP . "', device = '" . $device_str . "' WHERE uid = '" . $user_info['user_id'] . "'");

                    DB::getDB()->update('log', [
                        'browser' => $device['browser'],
                        'ip' => $_IP,
                        'device' => $device_str,
                    ], [
                        'uid' => $check_user['user_id']
                    ]);

//            if ($config['temp'] !== 'mobile') {
//                header('Location: /u' . $check_user['user_id']);
//            } else {
                    header('Location: /');
//            }
                } else {
                    $params = [
                        'title' => 'Restore',
                    ];
                    view('auth.restore_error', $params);
                    exit();
                }
            } else {
                $params = [
                    'title' => 'Restore',
                ];
                view('auth.restore_error', $params);
                exit();
            }
        }
        return '';
    }
}