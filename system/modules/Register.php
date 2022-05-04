<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use Mozg\classes\{Cache, DB, Email, Module, ViewEmail};
use FluffyDollop\Http\Request;

use FluffyDollop\Http\Response;

use FluffyDollop\Support\{Registry, Status, Cookie};
use FluffyDollop\Filesystem\Filesystem;
use JetBrains\PhpStorm\NoReturn;

final class Register extends Module
{
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function send()
    {
        if (!Registry::get('logged')) {
            $server_time = Registry::get('server_time');
//    NoAjaxQuery();
            //Код безопасности
            $session_sec_code = $_SESSION['sec_code'] ?? null;
            $sec_code = (new Request)->filter('sec_code');
            //Если код введенный юзером совпадает, то пропускаем, иначе выводим ошибку
            if ($sec_code == $session_sec_code) {
                //Входные POST Данные

                $user_name = (new Request)->filter('name');
                $user_lastname = (new Request)->filter('lastname');
                $user_email = (new Request)->filter('email', 100, true);
                $user_name = ucfirst($user_name);
                $user_lastname = ucfirst($user_lastname);
                $user_sex = (new Request)->int('sex');
                if ($user_sex < 0 || $user_sex > 2) {
                    $user_sex = 0;
                }
                $user_day = (new Request)->int('day');
                if ($user_day < 0 || $user_day > 31) {
                    $user_day = 0;
                }
                $user_month = (new Request)->int('month');
                if ($user_month < 0 || $user_month > 12) {
                    $user_month = 0;
                }
                $user_year = (new Request)->int('year');
                if ($user_year < 1930 || $user_year > 2007) {
                    $user_year = 0;
                }
                $user_country = (new Request)->int('country');
                if ($user_country < 0 || $user_country > 10) {
                    $user_country = 0;
                }
                $user_city = (new Request)->int('city');
                if ($user_city < 0 || $user_city > 1587) {
                    $user_city = 0;
                }
                $password_first = (new Request)->filter('password_first');
                $password_second = (new Request)->filter('password_second');

                $user_birthday = $user_year . '-' . $user_month . '-' . $user_day;

                $errors = array();
                $err_str = '';

                //Проверка имени
                if (strlen($user_name) >= 2) {
                    $errors[] = 0;
                } else {
                    $err_str .= 'no_name|' . $user_name . '|';
                }
                //Проверка фамилии
                if (strlen($user_lastname) >= 2) {
                    $errors[] = 0;
                } else {
                    $err_str .= 'no_lastname|' . $user_lastname . '|';
                }
                //Проверка E-mail
                if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 0;
                } else {
                    $err_str .= 'no_email|' . $user_email . '|';
                }
                //Проверка Паролей
                if (strlen($password_first) >= 6 && $password_first == $password_second) {
                    $errors[] = 0;
                } else {
                    $err_str .= 'no_password|' . $password_first . ' ' . $password_second . '|';
                }
                $allEr = count($errors);

                //Если нет ошибок, то пропускаем и добавляем в базу
                if ($allEr === 4) {
                    $check_email = DB::getDB()->row('SELECT COUNT(*) AS cnt FROM `users` WHERE user_email = ?', $user_email);
                    if (!$check_email['cnt']) {
                        $md5_pass = md5(md5($password_first));
                        $user_group = '5';
                        if ($user_country > 0 || $user_city > 0) {
                            $country_info = DB::getDB()->row('SELECT name FROM `country` WHERE id = ?', $user_country);
                            $city_info = DB::getDB()->row('SELECT name FROM `city` WHERE id = ?', $user_city);
                            $user_country_city_name = $country_info['name'] . '|' . $city_info['name'];
                        } else {
                            $user_country_city_name = '|';
                        }
                        $user_search_pref = $user_name . ' ' . $user_lastname;
                        //Hash ID
                        $_IP = null;//FIXME
                        $hid = $md5_pass . md5(md5($_IP));

                        $id = DB::getDB()->insert('users', [
                            'user_last_visit' => $server_time,
                            'user_email' => $user_email,
                            'user_password' => $md5_pass,
                            'user_name' => $user_name,
                            'user_lastname' => $user_lastname,
                            'user_sex' => $user_sex,
                            'user_day' => $user_day,
                            'user_month' => $user_month,
                            'user_year' => $user_year,
                            'user_country' => $user_country,
                            'user_city' => $user_city,
                            'user_reg_date' => $server_time,
                            'user_lastdate' => $server_time,
                            'user_group' => $user_group,
                            'user_hid' => $hid,
                            'user_country_city_name' => $user_country_city_name,
                            'user_search_pref' => $user_search_pref,
                            'user_birthday' => $user_birthday,
                            'user_privacy' => 'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||',
                        ]);
                        //Устанавливаем в сессию ИД юзера
                        $_SESSION['user_id'] = (int)$id;
                        //Записываем COOKIE
                        Cookie::append("user_id", (int)$id, 365);
                        Cookie::append("password", md5(md5($password_first)), 365);
                        Cookie::append("hid", $hid, 365);
                        //Создаём папку юзера в кеше
                        Cache::mozgCreateFolderCache("user_{$id}");
                        //Директория юзеров
                        $upload_dir = ROOT_DIR . '/uploads/users/';

                        Filesystem::createDir($upload_dir . $id);
                        Filesystem::createDir($upload_dir . $id . '/albums');

                        //Если юзер регистрировался по ссылке, то начисляем юзеру 10 убм
                        $ref_id = $_SESSION['ref_id'] ?? null;

                        if ($ref_id) {
                            //Проверяем на накрутку убм, что юзер не сам регистрирует анкеты
                            $check_ref = DB::getDB()->row('SELECT COUNT(*) AS cnt FROM `log` WHERE ip =  ?', $_IP);
                            if (!$check_ref['cnt']) {
                                $ref_id = (int)$ref_id;
                                //Даём +10 убм
//                                $db->query("UPDATE `users` SET user_balance = user_balance+10 WHERE user_id = '{$ref_id}'");
                                DB::getDB()->update('users', [
                                    'user_balance+10',
                                ], [
                                    'user_id' => $ref_id
                                ]);
                                //Вставляем ид регистратора
//                                $db->query("INSERT INTO `invites` SET uid = '{$ref_id}', ruid = '{$id}'");

                                DB::getDB()->insert('invites', [
                                    'uid' => $ref_id,
                                    'ruid' => $id,
                                ]);
                            }
                        }
                        //Вставляем лог в бд
                        $_BROWSER = $_BROWSER ?? null;
//                        $db->query("INSERT INTO `log` SET uid = '{$id}', browser = '{$_BROWSER}', ip = '{$_IP}'");

                        DB::getDB()->insert('log', [
                            'uid' => $id,
                            'browser' => $_BROWSER,
                            'ip' => $_IP,
                        ]);
                        $status = Status::OK;
                    } else {
                        $status = Status::BAD_MAIL;
                        $id = 0;
                    }
                } else {
                    $status = Status::NOT_VALID;
                    $id = 0;
                }
            } else {
                $status = Status::PERMISSION;
                $id = 0;
            }
        } else {
            $status = Status::LOGGED;
            $id = 0;
        }
        $response = array(
            'status' => $status,
            'user_id' => $id,
        );

        (new Response)->_e_json($response);

    }

    /**
     * @throws \ErrorException|\JsonException
     */
    public function login(): void
    {
        $params = [
            'title' => 'Login',
        ];
        view('auth.login', $params);

//        $tpl = new TpLSite($this->tpl_dir_name);
//        $tpl->load_template('login.tpl');
//        $tpl->compile('content');
//        $tpl->renderAjax();
    }

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function rules()
    {
        $result = [
            'title' => 'rules',
            'content' => view_json('register.rules', [])
        ];
        header('Content-Type: application/json');
        return print json_encode($result, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function step2()
    {
        $params = [
            'rndval' => time()
        ];
        $result = [
            'title' => 'rules',
            'content' => view_json('register.step2', $params)
        ];
        header('Content-Type: application/json');
        return print json_encode($result, JSON_THROW_ON_ERROR);
    }

    /**
     * todo json response
     * @throws \Exception
     */
    public function step3()
    {
        //Код безопасности
        $session_sec_code = $_SESSION['sec_code'];
        $sec_code = $_POST['sec_code'];

        if ($sec_code === $session_sec_code) {

            //POST данные
            $user_email = (new Request)->filter('email');

            //Проверка E-mail
//            if(preg_match('/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i', $user_email)) {
//                $ok_email = true;
//            }
//            else {
//                $ok_email = false;
//            }

            //todo
            if (true) {
                //Проверка на блок email сервиса
/*                $exp_user_email = explode('@', $user_email);
                $config = settings_get();
                try {
                    if (isset($config['bad_email'])){
                        $bad_server = explode(', ', $config['bad_email']);
                        if ($config['bad_email']) {
                            foreach ($bad_server as $serv) {
                                if ($exp_user_email[1] == $serv) {
                                    $bad = true;
                                }
                            }
                        }
                        $bad = $bad ?? false;
                        if ($bad) {
                            echo '4';
                            exit;
                        }
                    }

                }catch (\Error){

                }*/

                /** @var array $check_email */
                $check_email = DB::getDB()->row('SELECT COUNT(*) AS cnt FROM `users` WHERE user_email = ?', $user_email);
                if ($check_email['cnt']) {
                    echo '3';
                } else {
                    //Удаляем все предыдущие запросы на регистрацию этого email
//                    $db->query("DELETE FROM `restore` WHERE email = '{$user_email}'");

                    DB::getDB()->delete('restore', [
                        'email' => $user_email
                    ]);

                    $salt = 'abchefghjkmnpqrstuvwxyz0123456789';
                    $rand_lost = '';
                    for ($i = 0; $i < 15; $i++) {
                        $rand_lost .= $salt[random_int(0, 33)];
                    }
                    $hash = md5(time() . $user_email . random_int(0, 100000) . $rand_lost);
                    $_IP = $_SERVER['REMOTE_ADDR'];//fixme
                    //Вставляем в базу
//                    $db->query("INSERT INTO `restore` SET email = '{$user_email}', hash = '{$hash}', ip = '{$_IP}'");

                    DB::getDB()->insert('restore', [
                        'email' => $user_email,
                        'hash' => $hash,
                        'ip' => $_IP,
                    ]);

                    //Отправляем письмо на почту для восстановления
                    /** @var array $dictionary */
                    $dictionary = $this->lang;
                    $config = settings_get();
                    $variables = [
                        'home_url' => $config['home_url'],
                        'hash' => $hash,
                    ];
                    $message = (new ViewEmail('register.email', $variables))->run();
                    Email::send($user_email, $dictionary['thanks_reg'], $message);
//                    echo "{$config['home_url']}register/activate?hash={$hash}";
                }
            } else {
                echo '2';
            }
        } else {
            echo '1';
        }
    }

    /**
     * @throws \Exception|\ErrorException
     */
    public function activate()
    {
        try {
            $hash = strip_data($_GET['hash']);
            $_IP = $_SERVER['REMOTE_ADDR'];
            $row = DB::getDB()->row('SELECT email FROM `restore` WHERE hash = ? AND ip = ?', $hash, $_IP);
            if ($row) {
                $salt = 'abchefghjkmnpqrstuvwxyz0123456789';
                $rand_lost = '';
                for ($max_var = 0; $max_var < 15; $max_var++) {
                    $rand_lost .= $salt[random_int(0, 33)];
                }
                $new_hash = md5(time() . $row['email'] . random_int(0, 100000) . $rand_lost);
                DB::getDB()->update('restore', [
                    'hash' => $new_hash,
                ], [
                    'email' => $row['email']
                ]);

                $params = [
                    'title' => $config['home'],
                    'hash' => $new_hash
                ];

                view('register.step3', $params);
            } else {
                $params = [
                    'title' => $config['home'],
                ];
                view('register.old', $params);
            }
        }catch (\Error $error){
        }

    }

    /**
     * @throws \Exception
     */
    public function finish(): void
    {
        $db = Registry::get('db');
        //Проверка hash
        $hash = strip_data($_POST['hash']);

        $_IP = $_SERVER['REMOTE_ADDR'];
        /** @var array $row */
        $row = DB::getDB()->row('SELECT email FROM `restore` WHERE hash = ? AND ip = ?', $hash, $_IP);

        if ($row['email']) {

            //Входные POST Данные
            $user_name = (new Request)->filter('reg_name');
            $user_lastname = (new Request)->filter('reg_lastname');

            $user_name = ucfirst($user_name);
            $user_lastname = ucfirst($user_lastname);

            $password_first = (new Request)->filter('reg_pass1');
            $password_second = (new Request)->filter('reg_pass2');

            $errors = [];

            //Проверка имени
            if (!empty($user_name)) {
                $errors[] = 0;
            }

            //Проверка фамилии
            if (!empty($user_lastname)) {
                $errors[] = 0;
            }

            //Проверка Паролей
            if (strlen($password_first) >= 6 && $password_first == $password_second) {
                $errors[] = 0;
            }

            $all_err = \count($errors);

            //Если нет ошибок, то пропускаем и добавляем в базу
            if ($all_err === 3) {

                //Login hash ID
                $hid = md5(md5($_IP));

                $md5_pass = md5(md5($password_first));
                $user_group = '5';
                $user_search_pref = $user_name . ' ' . $user_lastname;
//                $row['email'] = $row['email'];
                $config = settings_get();

                //Вставляем юзера в базу
                $server_time = time();

                $reg_user_id = DB::getDB()->insert('users', [
                    'user_email' => $row['email'],
                    'user_password' => $md5_pass,
                    'user_name' => $user_name,
                    'user_lastname' => $user_lastname,
                    'user_reg_date' => $server_time,
                    'user_lastdate' => $server_time,
                    'user_group' => $user_group,
                    'user_hid' => $hid,
                    'user_search_pref' => $user_search_pref,
                    'user_active' => '1',
                    'user_privacy' => 'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||',
                ]);

//                $reg_user_id = $db->insert_id();

                //Если юзер добавился в базу, то входим на сайт
                if ($reg_user_id) {

                    //Устанавливаем в сессию ИД юзера
                    $_SESSION['user_id'] = $reg_user_id;

                    //Записываем COOKIE
                    Cookie::append('user_id', $reg_user_id, 365);
                    Cookie::append('password', md5(md5($password_first)), 365);
                    Cookie::append('hid', $hid, 365);

                    //Создаём папку юзера в кеше
                    Cache::mozgCreateFolderCache("user_{$reg_user_id}");

                    //Директория юзеров
                    $upload_dir = ROOT_DIR . '/uploads/users/';

                    Filesystem::createDir($upload_dir . $reg_user_id);
                    Filesystem::createDir($upload_dir . $reg_user_id . '/albums');

                    //Выдача бонуса рефералу
                    /*                    $bonus = false;
                                        if($_SESSION['ref_id'] && $bonus){
                                            //Проверяем на накрутку бонусов, что юзер не сам регистрирует анкеты
                                            $check_ref = $db->super_query("SELECT COUNT(*) AS cnt FROM `log` WHERE ip = '{$_IP}'");
                                            $ref_id = (int)$_SESSION['ref_id'];

                                            if($check_ref['cnt']){
                                                //Если ip совпадает, то просто записываем юзера как приглашенного без выдачи бонуса
                                                $db->query("INSERT INTO `invite_users` SET ref_id = '{$ref_id}', uid = '{$id}', bonus = '0', nbonus = '3', date = '{$server_time}', ref_ip = '{$row['ip']}', ip = '{$_IP}'");
                                            }else{

                                                if($config['nbonus'] == 1){

                                                    //Даём рефералу бонус
                                                    $config['ref_bonus'] = (int)$config['ref_bonus'];
                                                    $db->query("UPDATE `users` SET user_balance = user_balance+'{$config['ref_bonus']}' WHERE user_id = '{$ref_id}'");

                                                }elseif($config['nbonus'] == 2){

                                                    //Даём рефералу бонус
                                                    $config['ref_bonus'] = (int)$config['ref_bonus'];
                                                    $db->query("UPDATE `users` SET user_rating = user_rating + '{$config['ref_bonus']}' WHERE user_id = '{$ref_id}'");

                                                }

                                                //Вставяляем рефералу ид регистратора
                                                $db->query("INSERT INTO `invite_users` SET ref_id = '{$ref_id}', uid = '{$id}', bonus = '{$config['ref_bonus']}', nbonus = '{$config['nbonus']}', date = '{$server_time}', ref_ip = '{$row['ip']}', ip = '{$_IP}'");
                                            }
                                        }*/

                    //Вставляем лог в бд
                    $_BROWSER = '';
//                    $db->query("INSERT INTO `log` SET uid = '{$reg_user_id}', browser = '{$_BROWSER}', ip = '{$_IP}'");

                    DB::getDB()->insert('log', [
                        'uid' => $reg_user_id,
                        'browser' => $_BROWSER,
                        'ip' => $_IP,
                    ]);

                    //Удаляем ссылку регистрации на этот email
                    DB::getDB()->delete('restore', [
                        'email' => $row['email']
                    ]);
//                    $db->query("INSERT INTO `users_param` SET user_id = '{$id}'");
                }
            } else {
                echo '2';
            }

        } else {
            echo '1';
        }
    }

    /**
     * @throws \ErrorException|\JsonException
     */
    #[NoReturn] public function main(): void
    {
        $config = settings_get();

        $params = [
            'title' => $config['home'],
            'available' => 'main'
        ];

        view('reg', $params);
    }
}