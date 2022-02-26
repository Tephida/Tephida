<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use Mozg\classes\Filesystem;
use Mozg\classes\Module;
use Mozg\classes\Registry;
use Mozg\classes\Status;
use Mozg\classes\TpLSite;

class Register extends Module
{
    /**
     * @throws \JsonException
     */
    public function send()
    {
        if (!Registry::get('logged')) {
            $db = Registry::get('db');
            $server_time = Registry::get('server_time');
//    NoAjaxQuery();
            //Код безопасности
            $session_sec_code = $_SESSION['sec_code'] ?? null;
            $sec_code = requestFilter('sec_code');
            //Если код введенный юзером совпадает, то пропускаем, иначе выводим ошибку
            if ($sec_code == $session_sec_code) {
                //Входные POST Данные

                $user_name = requestFilter('name');
                $user_lastname = requestFilter('lastname');
                $user_email = requestFilter('email', 100, true);
                $user_name = ucfirst($user_name);
                $user_lastname = ucfirst($user_lastname);
                $user_sex = intFilter('sex');
                if ($user_sex < 0 or $user_sex > 2)
                    $user_sex = 0;
                $user_day = intFilter('day');
                if ($user_day < 0 or $user_day > 31)
                    $user_day = 0;
                $user_month = intFilter('month');
                if ($user_month < 0 or $user_month > 12)
                    $user_month = 0;
                $user_year = intFilter('year');
                if ($user_year < 1930 or $user_year > 2007)
                    $user_year = 0;
                $user_country = intFilter('country');
                if ($user_country < 0 or $user_country > 10)
                    $user_country = 0;
                $user_city = intFilter('city');
                if ($user_city < 0 or $user_city > 1587)
                    $user_city = 0;
                $password_first = requestFilter('password_first');
                $password_second = requestFilter('password_second');

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
                if ($allEr == 4) {
                    $check_email = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_email = '{$user_email}'");
                    if (!$check_email['cnt']) {
                        $md5_pass = md5(md5($password_first));
                        $user_group = '5';
                        if ($user_country > 0 or $user_city > 0) {
                            $country_info = $db->super_query("SELECT name FROM `country` WHERE id = '" . $user_country . "'");
                            $city_info = $db->super_query("SELECT name FROM `city` WHERE id = '" . $user_city . "'");
                            $user_country_city_name = $country_info['name'] . '|' . $city_info['name'];
                        } else {
                            $user_country_city_name = '' . '|' . '';
                        }
                        $user_search_pref = $user_name . ' ' . $user_lastname;
                        //Hash ID
                        $_IP = $_IP ?? null;
                        $hid = $md5_pass . md5(md5($_IP));
                        $db->query("INSERT INTO `users` (user_last_visit, user_email, user_password, user_name, user_lastname, user_sex, user_day, user_month, user_year, user_country, user_city, user_reg_date, user_lastdate, user_group, user_hid, user_country_city_name, user_search_pref, user_birthday, user_privacy) VALUES ('{$server_time}', '{$user_email}', '{$md5_pass}', '{$user_name}', '{$user_lastname}', '{$user_sex}', '{$user_day}', '{$user_month}', '{$user_year}', '{$user_country}', '{$user_city}', '{$server_time}', '{$server_time}', '{$user_group}', '{$hid}', '{$user_country_city_name}', '{$user_search_pref}', '{$user_birthday}', 'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||')");
                        $id = $db->insert_id();
                        //Устанавливаем в сессию ИД юзера
                        $_SESSION['user_id'] = intval($id);
                        //Записываем COOKIE
                        set_cookie("user_id", intval($id), 365);
                        set_cookie("password", md5(md5($password_first)), 365);
                        set_cookie("hid", $hid, 365);
                        //Создаём папку юзера в кеше
                        mozg_create_folder_cache("user_{$id}");
                        //Директория юзеров
                        $upload_dir = ROOT_DIR . '/uploads/users/';

                        Filesystem::createDir($upload_dir . $id);
                        Filesystem::createDir($upload_dir . $id . '/albums');

                        //Если юзер регистрировался по ссылке, то начисляем юзеру 10 убм
                        $ref_id = $_SESSION['ref_id'] ?? null;

                        if ($ref_id) {
                            //Проверяем на накрутку убм, что юзер не сам регистрирует анкеты
                            $check_ref = $db->super_query("SELECT COUNT(*) AS cnt FROM `log` WHERE ip = '{$_IP}'");
                            if (!$check_ref['cnt']) {
                                $ref_id = intval($ref_id);
                                //Даём +10 убм
                                $db->query("UPDATE `users` SET user_balance = user_balance+10 WHERE user_id = '{$ref_id}'");
                                //Вставляем ид регистратора
                                $db->query("INSERT INTO `invites` SET uid = '{$ref_id}', ruid = '{$id}'");
                            }
                        }
                        //Вставляем лог в бд
                        $_BROWSER = $_BROWSER ?? null;
                        $db->query("INSERT INTO `log` SET uid = '{$id}', browser = '{$_BROWSER}', ip = '{$_IP}'");
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
        _e_json($response);
    }

    /**
     * @throws \ErrorException
     */
    public function login()
    {
        $tpl = new TpLSite($this->tpl_dir_name);
        $tpl->load_template('login.tpl');
        $tpl->compile('content');
        $tpl->renderAjax();
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function main()
    {
//        $tpl = $this->tpl;//todo
        $config = settings_get();
        $meta_tags['title'] = $config['home'];
        $tpl = new TpLSite($this->tpl_dir_name, $meta_tags);
        $tpl->load_template('reg.tpl');
        $db = Registry::get('db');
//################## Загружаем Страны ##################//
        $sql_country = $db->super_query("SELECT * FROM `country` ORDER by `name` ASC", true);
        $all_country = '';
        foreach ($sql_country as $row_country) {
            $all_country .= '<option value="' . $row_country['id'] . '">' . stripslashes($row_country['name']) . '</option>';
        }
        $tpl->set('{country}', $all_country);
        $tpl->compile('content');
        $tpl->render();
    }
}