<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Registry, Status};
use FluffyDollop\Filesystem\Filesystem;
use FluffyDollop\Http\Request;
use Mozg\classes\Cache;

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $server_time = Registry::get('server_time');
    $metatags['title'] = $lang['editmyprofile'];
    $db = Registry::get('db');
    $user_info = $user_info ?? Registry::get('user_info');

    switch ($act) {

        //Загрузка фотографии
        case "upload":
            NoAjaxQuery();
            $user_id = $user_info['user_id'];
            $upload_dir = ROOT_DIR . '/uploads/users/';

            //Если нет папок юзера, то создаём её
            Filesystem::createDir($upload_dir . $user_id);
            Filesystem::createDir($upload_dir . $user_id . '/albums');

            //Если нет папки альбома, то создаём её
            $check_system_albums = $db->super_query("SELECT aid, cover FROM `albums` WHERE user_id = '{$user_id}' AND system = 1");
            if(!$check_system_albums) {
                $hash = md5(md5($server_time).md5($user_info['user_id']).md5($user_info['user_email']));
                $date_create = date('Y-m-d H:i:s', $server_time);
                $sql_privacy = '';
                $sql_ = $db->query("INSERT INTO `albums` (user_id, name, descr, ahash, adate, position, system, privacy) VALUES ('{$user_id}', 'Фотографии со страницы', '', '{$hash}', '{$date_create}', '0', '1', '{$sql_privacy}')");
                $aid_fors = $db->insert_id();
                $db->query("UPDATE `users` SET user_albums_num = user_albums_num+1 WHERE user_id = '{$user_id}'");
            } else {
                $aid_fors = $check_system_albums['aid'];
            }
            $album_dir = ROOT_DIR.'/uploads/users/'.$user_id.'/albums/'.$aid_fors.'/';
            Filesystem::createDir($album_dir);

            //Разрешенные форматы
            $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

            //Получаем данные о фотографии
            $image_tmp = $_FILES['uploadfile']['tmp_name'];
            $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
            $image_rename = substr(md5($server_time + random_int(1, 100000)), 0, 15); // имя фотографии
            $image_size = $_FILES['uploadfile']['size']; // размер файла
            $array = explode(".", $image_name);
            $type = end($array); // формат файла

            //Проверяем если, формат верный то пропускаем
            if (in_array($type, $allowed_files, true)) {
                if ($image_size < 5000000) {
                    $res_type = '.' . $type;
                    $upload_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/'; // Директория куда загружать
                    if (move_uploaded_file($image_tmp, $upload_dir . $image_rename . $res_type)) {

                        Filesystem::copy($upload_dir . $image_rename . $res_type, $album_dir.$image_rename.$res_type);

                        //Создание оригинала
                        $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                        $tmb->size_auto(770);
                        $tmb->jpeg_quality(95);
                        $tmb->save($upload_dir . 'o_' . $image_rename . $res_type);

                        //Создание главной фотографии
                        $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                        $tmb->size_auto(200, 1);
                        $tmb->jpeg_quality(97);
                        $tmb->save($upload_dir . $image_rename . $res_type);

                        //Создание уменьшенной копии 50х50
                        $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                        $tmb->size_auto('50x50');
                        $tmb->jpeg_quality(97);
                        $tmb->save($upload_dir . '50_' . $image_rename . $res_type);

                        $date = date('Y-m-d H:i:s', $server_time);

                        $position_all = $_SESSION['position_all'] ?? null;
                        if($position_all){
                            $position_all = $position_all+1;
                        } else {
                            $position_all = 100000;
                        }
                        $_SESSION['position_all'] = $position_all;

                        $db->query("INSERT INTO `photos` (album_id, photo_name, user_id, date, 
                      position, descr, comm_num, rating_all, rating_num, rating_max ) VALUES (
                            '{$aid_fors}', '{$image_rename}{$res_type}', 
                            '{$user_id}', '{$date}', '{$position_all}', '', 0, 0, 0, 0
                                                                             )");
                        $ins_id = $db->insert_id();

                        if(!$check_system_albums['cover'])
                            $db->query("UPDATE `albums` SET cover = '' WHERE aid = '{$aid_fors}'");
                        $db->query("UPDATE `albums` SET cover = '{$image_rename}{$res_type}' WHERE aid = '{$aid_fors}'");

                        $db->query("UPDATE `albums` SET photo_num = photo_num+1, adate = '{$date}' WHERE aid = '{$aid_fors}'");


                        //Создание уменьшенной копии 100х100
                        $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                        $tmb->size_auto('100x100');
                        $tmb->jpeg_quality(97);
                        $tmb->save($upload_dir . '100_' . $image_rename . $res_type);

                        //Создание маленькой копии
                        $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                        $tmb->size_auto('140x100');
                        $tmb->jpeg_quality(97);
                        $tmb->save($upload_dir . 'c_' . $image_rename . $res_type);

                        Filesystem::copy($upload_dir . 'c_' . $image_rename . $res_type, $album_dir. 'c_' . $image_rename.$res_type);

                        //Добавляем на стену
                        $row = $db->super_query("SELECT user_sex FROM `users` WHERE user_id = '{$user_id}'");
                        if ($row['user_sex'] == 2) {
                            $sex_text = 'обновила';
                        }
                        else {
                            $sex_text = 'обновил';
                        }

//                        $wall_text = "<div class=\"profile_update_photo\"><a href=\"\" onClick=\"Photo.Profile(\'{$user_id}\', \'{$image_rename}{$res_type}\'); return false\"><img src=\"/uploads/users/{$user_id}/o_{$image_rename}{$res_type}\" style=\"margin-top:3px\"></a></div>";

                        $wall_text = "<div class=\"profile_update_photo\"><a href=\"/photo{$user_id}_{$ins_id}_{$aid_fors}\" onClick=\"Photo.Show(this.href); return false\"><img src=\"/uploads/users/{$user_id}/o_{$image_rename}{$res_type}\" style=\"margin-top:3px\"></a></div>";


                        $db->query("INSERT INTO `wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$wall_text}', add_date = '{$server_time}', type = '{$sex_text} фотографию на странице:'");
                        $dbid = $db->insert_id();

                        $db->query("UPDATE `users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$user_id}'");

                        //Добавляем в ленту новостей
                        $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$wall_text}', obj_id = '{$dbid}', action_time = '{$server_time}'");

                        //Обновляем имя фотки в бд
                        $db->query("UPDATE `users` SET user_photo = '{$image_rename}{$res_type}', user_wall_id = '{$dbid}' WHERE user_id = '{$user_id}'");
                        $config = settings_get();
                        $photo = $config['home_url'] . 'uploads/users/' . $user_id . '/' . $image_rename . $res_type;

                        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                        Cache::mozgClearCache();
                        $status = Status::OK;
                    } else {
                        $photo = '';
                        $status = Status::BAD;
                    }
                } else {
                    $photo = '';
                    $status = Status::BIG_SIZE;
                }
            } else {
                $photo = '';
                $status = Status::BAD_FORMAT;
            }

            $response = [
                'status' => $status,
                'photo' => $photo,
            ];
            (new \FluffyDollop\Http\Response)->_e_json($response);
            break;

        //Удаление фотографии
        case "del_photo":
            NoAjaxQuery();
            $user_id = $user_info['user_id'];
            $upload_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
            $row = $db->super_query("SELECT user_photo, user_wall_id FROM `users` WHERE user_id = '{$user_id}'");
            if ($row['user_photo']) {
                $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE id = '{$row['user_wall_id']}'");
                if ($check_wall_rec['cnt']) {
                    $update_wall = ", user_wall_num = user_wall_num-1";
                    $db->query("DELETE FROM `wall` WHERE id = '{$row['user_wall_id']}'");
                    $db->query("DELETE FROM `news` WHERE obj_id = '{$row['user_wall_id']}'");
                } else {
                    $update_wall = null;
                }

                $db->query("UPDATE `users` SET user_photo = '', user_wall_id = '' {$update_wall} WHERE user_id = '{$user_id}'");

                Filesystem::delete($upload_dir . $row['user_photo']);
                Filesystem::delete($upload_dir . '50_' . $row['user_photo']);
                Filesystem::delete($upload_dir . '100_' . $row['user_photo']);
                Filesystem::delete($upload_dir . 'o_' . $row['user_photo']);
                Filesystem::delete($upload_dir . 'c_' . $row['user_photo']);
                //TODO удалить из альбома

                Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                Cache::mozgClearCache();
            }
            break;

        //Страница загрузки главной фотографии
        case "load_photo":
            NoAjaxQuery();
            $tpl->load_template('load_photo.tpl');
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        //Сохранение основных данных
        case "save_general":
            NoAjaxQuery();

            $post_user_sex = (new Request)->int('sex');
            if ($post_user_sex == 1 || $post_user_sex == 2) {
                $user_sex = $post_user_sex;
            } else {
                $user_sex = false;
            }

            $user_day = (new Request)->int('day');
            $user_month = (new Request)->int('month');
            $user_year = (new Request)->int('year');
            $user_country = (new Request)->int('country');
            $user_city = (new Request)->int('city');
            $user_birthday = $user_year . '-' . $user_month . '-' . $user_day;

            if ($user_sex) {
                $post_sp = (new Request)->int('sp');
                if ($post_sp >= 1 && $post_sp <= 7) {
                    $sp = $post_sp;
                } else {
                    $sp = false;
                }

                if ($sp) {
                    $sp_val = (new Request)->int('sp_val');
                    $user_sp = $sp . '|' . $sp_val;
                }
            }

            if ($user_country > 0) {
                $country_info = $db->super_query("SELECT name FROM `country` WHERE id = '" . $user_country . "'");
                $city_info = $db->super_query("SELECT name FROM `city` WHERE id = '" . $user_city . "'");

                $user_country_city_name = $country_info['name'] . '|' . $city_info['name'];
            } else {
                $user_city = 0;
                $user_country = 0;
                $user_country_city_name = '';
            }

            $db->query("UPDATE `users` SET user_sex = '{$user_sex}', user_day = '{$user_day}', user_month = '{$user_month}', user_year = '{$user_year}', user_country = '{$user_country}', user_city = '{$user_city}', user_country_city_name = '{$user_country_city_name}', user_birthday = '{$user_birthday}', user_sp = '{$user_sp}' WHERE user_id = '{$user_info['user_id']}'");

            Cache::mozgClearCacheFile('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);
            Cache::mozgClearCache();

            echo 'ok';

            break;

        //Сохранение контактов
        case "save_contact":
            NoAjaxQuery();

            $xfields = array();
            $xfields['vk'] = (new Request)->filter('vk', 200);
            $xfields['od'] = (new Request)->filter('od', 200);
            $xfields['phone'] = (new Request)->filter('phone', 200);
            $xfields['skype'] = (new Request)->filter('skype', 200);
            $xfields['fb'] = (new Request)->filter('fb', 200);
            $xfields['icq'] = (new Request)->filter('icq', 200);
            $xfields['site'] = (new Request)->filter('site', 200);

            $xfieldsdata = '';
            foreach ($xfields as $name => $value) {
                $value = str_replace("|", "&#124;", $value);
                if (strlen($value) > 0) {
                    $xfieldsdata .= $name . '|' . $value . '||';
                }
            }

            $db->query("UPDATE `users` SET user_xfields = '{$xfieldsdata}' WHERE user_id = '{$user_info['user_id']}'");

            Cache::mozgClearCacheFile('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);

            echo 'ok';

            break;

        //Сохранение интересов
        case "save_interests":
            NoAjaxQuery();

            $xfields = array();
            $xfields['activity'] = (new Request)->filter('activity', 5000);
            $xfields['interests'] = (new Request)->filter('interests', 5000);
            $xfields['myinfo'] = (new Request)->filter('myinfo', 5000);
            $xfields['music'] = (new Request)->filter('music', 5000);
            $xfields['kino'] = (new Request)->filter('kino', 5000);
            $xfields['books'] = (new Request)->filter('books', 5000);
            $xfields['games'] = (new Request)->filter('games', 5000);
            $xfields['quote'] = (new Request)->filter('quote', 5000);

            $xfieldsdata = '';
            foreach ($xfields as $name => $value) {
                $value = str_replace("|", "&#124;", $value);
                if (strlen($value) > 0) {
                    $xfieldsdata .= $name . '|' . $value . '||';
                }
            }

            $db->query("UPDATE `users` SET user_xfields_all = '{$xfieldsdata}' WHERE user_id = '{$user_info['user_id']}'");

            Cache::mozgClearCacheFile('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);

            echo 'ok';

            break;

        //Страница Редактирование контактов
        case "contact":
            $user_speedbar = $lang['editmyprofile'] . ' &raquo; ' . $lang['editmyprofile_contact'];
            $tpl->load_template('editprofile.tpl');
            $row = $db->super_query("SELECT user_xfields FROM `users` WHERE user_id = '{$user_info['user_id']}'");
            $xfields = xfieldsdataload($row['user_xfields']);
            $tpl->set('{vk}', stripslashes($xfields['vk']));
            $tpl->set('{od}', stripslashes($xfields['od']));
            $tpl->set('{fb}', stripslashes($xfields['fb']));
            $tpl->set('{skype}', stripslashes($xfields['skype']));
            $tpl->set('{icq}', stripslashes($xfields['icq']));
            $tpl->set('{phone}', stripslashes($xfields['phone']));
            $tpl->set('{site}', stripslashes($xfields['site']));
            $tpl->set_block("'\\[general\\](.*?)\\[/general\\]'si", "");
            $tpl->set_block("'\\[interests\\](.*?)\\[/interests\\]'si", "");
            $tpl->set_block("'\\[xfields\\](.*?)\\[/xfields\\]'si", "");
            $tpl->set('[contact]', '');
            $tpl->set('[/contact]', '');
            $tpl->compile('content');
            $tpl->clear();

            compile($tpl);
            break;

        //Страница Редактирование интересов
        case "interests":
            $user_speedbar = $lang['editmyprofile'] . ' &raquo; ' . $lang['editmyprofile_interests'];
            $tpl->load_template('editprofile.tpl');
            $row = $db->super_query("SELECT user_xfields_all FROM `users` WHERE user_id = '{$user_info['user_id']}'");
            $xfields = xfieldsdataload($row['user_xfields_all']);
            $tpl->set('{activity}', stripslashes($xfields['activity']));
            $tpl->set('{interests}', stripslashes($xfields['interests']));
            $tpl->set('{myinfo}', stripslashes($xfields['myinfo']));
            $tpl->set('{music}', stripslashes($xfields['music']));
            $tpl->set('{kino}', stripslashes($xfields['kino']));
            $tpl->set('{books}', stripslashes($xfields['books']));
            $tpl->set('{games}', stripslashes($xfields['games']));
            $tpl->set('{quote}', stripslashes($xfields['quote']));
            $tpl->set_block("'\\[contact\\](.*?)\\[/contact\\]'si", "");
            $tpl->set_block("'\\[general\\](.*?)\\[/general\\]'si", "");
            $tpl->set_block("'\\[xfields\\](.*?)\\[/xfields\\]'si", "");
            $tpl->set('[interests]', '');
            $tpl->set('[/interests]', '');
            $tpl->compile('content');
            $tpl->clear();

            compile($tpl);
            break;

        //Страница миниатюры
        case "miniature":

            $row = $db->super_query("SELECT user_photo FROM `users` WHERE user_id = '{$user_info['user_id']}'");

            if ($row['user_photo']) {

                $tpl->load_template('miniature/main.tpl');
                $tpl->set('{user-id}', $user_info['user_id']);
                $tpl->set('{ava}', $row['user_photo']);
                $tpl->compile('content');

                AjaxTpl($tpl);

            } else {
                echo '1';
            }

            break;

        //Сохранение миниатюры
        case "miniature_save":

            $row = $db->super_query("SELECT user_photo FROM `users` WHERE user_id = '{$user_info['user_id']}'");

            $i_left = (new Request)->int('i_left');
            $i_top = (new Request)->int('i_top');
            $i_width = (new Request)->int('i_width');
            $i_height = (new Request)->int('i_height');

            if ($row['user_photo'] and $i_width >= 100 and $i_height >= 100 and $i_left >= 0) {
                $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/{$row['user_photo']}");
                $tmb->size_auto($i_width . 'x' . $i_height, 0, "{$i_left}|{$i_top}");
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");

                $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");
                $tmb->size_auto("100x100", 1);
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");

                $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");
                $tmb->size_auto("50x50");
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/50_{$row['user_photo']}");

                echo $user_info['user_id'];

            } else {
                echo 'err';
            }
            break;

        default:

            //Страница Редактирование основное
            $user_speedbar = $lang['editmyprofile'] . ' &raquo; ' . $lang['editmyprofile_genereal'];

            $tpl->load_template('editprofile.tpl');

            $row = $db->super_query("SELECT user_name, user_lastname, user_sex, user_day, user_month, user_year, user_country, user_city, user_sp FROM `users` WHERE user_id = '{$user_info['user_id']}'");

            $tpl->set('{name}', $row['user_name']);
            $tpl->set('{lastname}', $row['user_lastname']);
            $tpl->set('{sex}', installationSelected($row['user_sex'], '<option value="1">мужской</option><option value="2">женский</option>'));
            $tpl->set('{user-day}', installationSelected($row['user_day'], '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>'));
            $tpl->set('{user-month}', installationSelected($row['user_month'], '<option value="1">Января</option><option value="2">Февраля</option><option value="3">Марта</option><option value="4">Апреля</option><option value="5">Мая</option><option value="6">Июня</option><option value="7">Июля</option><option value="8">Августа</option><option value="9">Сентября</option><option value="10">Октября</option><option value="11">Ноября</option><option value="12">Декабря</option>'));
            $tpl->set('{user-year}', installationSelected($row['user_year'], '<option value="1930">1930</option><option value="1931">1931</option><option value="1932">1932</option><option value="1933">1933</option><option value="1934">1934</option><option value="1935">1935</option><option value="1936">1936</option><option value="1937">1937</option><option value="1938">1938</option><option value="1939">1939</option><option value="1940">1940</option><option value="1941">1941</option><option value="1942">1942</option><option value="1943">1943</option><option value="1944">1944</option><option value="1945">1945</option><option value="1946">1946</option><option value="1947">1947</option><option value="1948">1948</option><option value="1949">1949</option><option value="1950">1950</option><option value="1951">1951</option><option value="1952">1952</option><option value="1953">1953</option><option value="1954">1954</option><option value="1955">1955</option><option value="1956">1956</option><option value="1957">1957</option><option value="1958">1958</option><option value="1959">1959</option><option value="1960">1960</option><option value="1961">1961</option><option value="1962">1962</option><option value="1963">1963</option><option value="1964">1964</option><option value="1965">1965</option><option value="1966">1966</option><option value="1967">1967</option><option value="1968">1968</option><option value="1969">1969</option><option value="1970">1970</option><option value="1971">1971</option><option value="1972">1972</option><option value="1973">1973</option><option value="1974">1974</option><option value="1975">1975</option><option value="1976">1976</option><option value="1977">1977</option><option value="1978">1978</option><option value="1979">1979</option><option value="1980">1980</option><option value="1981">1981</option><option value="1982">1982</option><option value="1983">1983</option><option value="1984">1984</option><option value="1985">1985</option><option value="1986">1986</option><option value="1987">1987</option><option value="1988">1988</option><option value="1989">1989</option><option value="1990">1990</option><option value="1991">1991</option><option value="1992">1992</option><option value="1993">1993</option><option value="1994">1994</option><option value="1995">1995</option><option value="1996">1996</option><option value="1997">1997</option><option value="1998">1998</option><option value="1999">1999</option><option value="2000">2000</option><option value="2001">2001</option><option value="2002">2002</option><option value="2003">2003</option><option value="2004">2004</option><option value="2005">2005</option><option value="2006">2006</option><option value="2007">2007</option>'));

            //################## Загружаем Страны ##################//
            $sql_country = $db->super_query("SELECT * FROM `country` ORDER by `name` ASC", true);
            $all_country = '';
            foreach ($sql_country as $row_country) {
                $all_country .= '<option value="' . $row_country['id'] . '">' . stripslashes($row_country['name']) . '</option>';
            }

            $tpl->set('{country}', installationSelected($row['user_country'], $all_country));

            //################## Загружаем Города ##################//
            $sql_city = $db->super_query("SELECT id, name FROM `city` WHERE id_country = '{$row['user_country']}' ORDER by `name` ASC", true);
            $all_city = '';
            foreach ($sql_city as $row2) {
                $all_city .= '<option value="' . $row2['id'] . '">' . stripslashes($row2['name']) . '</option>';
            }

            $tpl->set('{city}', installationSelected($row['user_city'], $all_city));

            $user_sp = explode('|', $row['user_sp']);
            if ($user_sp[1]) {
                $rowSp = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '{$user_sp[1]}'");
                $tpl->set('{sp-name}', $rowSp['user_search_pref']);
                $tpl->set_block("'\\[sp\\](.*?)\\[/sp\\]'si", "");

                if ($row['user_sex'] == 1) {
                    if ($user_sp[0] == 2) {
                        $tpl->set('{sp-text}', 'Подруга:');
                    } elseif ($user_sp[0] == 3) {
                        $tpl->set('{sp-text}', 'Невеста:');
                    } else if ($user_sp[0] == 4) {
                        $tpl->set('{sp-text}', 'Жена:');
                    } else if ($user_sp[0] == 5) {
                        $tpl->set('{sp-text}', 'Любимая:');
                    } else {
                        $tpl->set('{sp-text}', 'Партнёр:');
                    }
                } else {
                    if ($user_sp[0] == 2) {
                        $tpl->set('{sp-text}', 'Друг:');
                    } elseif ($user_sp[0] == 3)
                        $tpl->set('{sp-text}', 'Жених:');
                    else if ($user_sp[0] == 4)
                        $tpl->set('{sp-text}', 'Муж:');
                    else if ($user_sp[0] == 5)
                        $tpl->set('{sp-text}', 'Любимый:');
                    else {
                        $tpl->set('{sp-text}', 'Партнёр:');
                    }
                }
            } else {
                $tpl->set('[sp]', '');
                $tpl->set('[/sp]', '');
            }

            if ($row['user_sex'] == 2) {
                $tpl->set('[user-m]', '');
                $tpl->set('[/user-m]', '');
                $tpl->set_block("'\\[user-w\\](.*?)\\[/user-w\\]'si", "");
            } elseif ($row['user_sex'] == 1) {
                $tpl->set('[user-w]', '');
                $tpl->set('[/user-w]', '');
                $tpl->set_block("'\\[user-m\\](.*?)\\[/user-m\\]'si", "");
            } else {
                $tpl->set('[sp-all]', '');
                $tpl->set('[/sp-all]', '');
                $tpl->set('[user-m]', '');
                $tpl->set('[/user-m]', '');
                $tpl->set('[user-w]', '');
                $tpl->set('[/user-w]', '');
            }

            $tpl->copy_template = str_replace("[instSelect-sp-{$user_sp[0]}]", 'selected', $tpl->copy_template);
            $tpl->set_block("'\\[instSelect-(.*?)\\]'si", "");

            $tpl->set_block("'\\[contact\\](.*?)\\[/contact\\]'si", "");
            $tpl->set_block("'\\[interests\\](.*?)\\[/interests\\]'si", "");
            $tpl->set_block("'\\[xfields\\](.*?)\\[/xfields\\]'si", "");
            $tpl->set('[general]', '');
            $tpl->set('[/general]', '');
            $tpl->compile('content');
            $tpl->clear();

            compile($tpl);
    }
} else {
    $user_speedbar = 'Информация';
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}
