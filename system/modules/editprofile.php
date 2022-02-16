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

if (Registry::get('logged')) {
    $act = requestFilter('act');
    $server_time = Registry::get('server_time');
    $metatags['title'] = $lang['editmyprofile'];
    $db = Registry::get('db');

    switch ($act) {

        //Загрузка фотографии
        case "upload":
            NoAjaxQuery();

            //Подключаем класс для фотографий
            include ENGINE_DIR . '/classes/images.php';

            $user_id = $user_info['user_id'];
            $uploaddir = ROOT_DIR . '/uploads/users/';

            //Если нет папок юзера, то создаём её
            Filesystem::createDir($uploaddir . $user_id);
            Filesystem::createDir($uploaddir . $user_id . '/albums');

            //Разрешенные форматы
            $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

            //Получаем данные о фотографии
            $image_tmp = $_FILES['uploadfile']['tmp_name'];
            $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
            $image_rename = substr(md5($server_time + rand(1, 100000)), 0, 15); // имя фотографии
            $image_size = $_FILES['uploadfile']['size']; // размер файла
            $type = end(explode(".", $image_name)); // формат файла

            //Проверяем если, формат верный то пропускаем
            if (in_array($type, $allowed_files)) {
                if ($image_size < 5000000) {
                    $res_type = '.' . $type;
                    $uploaddir = ROOT_DIR . '/uploads/users/' . $user_id . '/'; // Директория куда загружать
                    if (move_uploaded_file($image_tmp, $uploaddir . $image_rename . $res_type)) {

                        //Создание оригинала
                        $tmb = new thumbnail($uploaddir . $image_rename . $res_type);
                        $tmb->size_auto(770);
                        $tmb->jpeg_quality(95);
                        $tmb->save($uploaddir . 'o_' . $image_rename . $res_type);

                        //Создание главной фотографии
                        $tmb = new thumbnail($uploaddir . $image_rename . $res_type);
                        $tmb->size_auto(200, 1);
                        $tmb->jpeg_quality(97);
                        $tmb->save($uploaddir . $image_rename . $res_type);

                        //Создание уменьшенной копии 50х50
                        $tmb = new thumbnail($uploaddir . $image_rename . $res_type);
                        $tmb->size_auto('50x50');
                        $tmb->jpeg_quality(97);
                        $tmb->save($uploaddir . '50_' . $image_rename . $res_type);

                        //Создание уменьшенной копии 100х100
                        $tmb = new thumbnail($uploaddir . $image_rename . $res_type);
                        $tmb->size_auto('100x100');
                        $tmb->jpeg_quality(97);
                        $tmb->save($uploaddir . '100_' . $image_rename . $res_type);

                        //Добавляем на стену
                        $row = $db->super_query("SELECT user_sex FROM `users` WHERE user_id = '{$user_id}'");
                        if ($row['user_sex'] == 2)
                            $sex_text = 'обновила';
                        else
                            $sex_text = 'обновил';

                        $wall_text = "<div class=\"profile_update_photo\"><a href=\"\" onClick=\"Photo.Profile(\'{$user_id}\', \'{$image_rename}{$res_type}\'); return false\"><img src=\"/uploads/users/{$user_id}/o_{$image_rename}{$res_type}\" style=\"margin-top:3px\"></a></div>";

                        $db->query("INSERT INTO `wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$wall_text}', add_date = '{$server_time}', type = '{$sex_text} фотографию на странице:'");
                        $dbid = $db->insert_id();

                        $db->query("UPDATE `users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$user_id}'");

                        //Добавляем в ленту новостей
                        $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$wall_text}', obj_id = '{$dbid}', action_time = '{$server_time}'");

                        //Обновляем имя фотки в бд
                        $db->query("UPDATE `users` SET user_photo = '{$image_rename}{$res_type}', user_wall_id = '{$dbid}' WHERE user_id = '{$user_id}'");

                        echo $config['home_url'] . 'uploads/users/' . $user_id . '/' . $image_rename . $res_type;

                        mozg_clear_cache_file('user_' . $user_id . '/profile_' . $user_id);
                        mozg_clear_cache();
                    } else
                        echo 'bad';
                } else
                    echo 'big_size';
            } else
                echo 'bad_format';

            die();
            break;

        //Удаление фотографии
        case "del_photo":
            NoAjaxQuery();
            $user_id = $user_info['user_id'];
            $uploaddir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
            $row = $db->super_query("SELECT user_photo, user_wall_id FROM `users` WHERE user_id = '{$user_id}'");
            if ($row['user_photo']) {
                $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE id = '{$row['user_wall_id']}'");
                if ($check_wall_rec['cnt']) {
                    $update_wall = ", user_wall_num = user_wall_num-1";
                    $db->query("DELETE FROM `wall` WHERE id = '{$row['user_wall_id']}'");
                    $db->query("DELETE FROM `news` WHERE obj_id = '{$row['user_wall_id']}'");
                } else
                    $update_wall = null;

                $db->query("UPDATE `users` SET user_photo = '', user_wall_id = '' {$update_wall} WHERE user_id = '{$user_id}'");

                Filesystem::delete($uploaddir . $row['user_photo']);
                Filesystem::delete($uploaddir . '50_' . $row['user_photo']);
                Filesystem::delete($uploaddir . '100_' . $row['user_photo']);
                Filesystem::delete($uploaddir . 'o_' . $row['user_photo']);
                Filesystem::delete($uploaddir . '130_' . $row['user_photo']);

                mozg_clear_cache_file('user_' . $user_id . '/profile_' . $user_id);
                mozg_clear_cache();
            }
            die();
            break;

        //Страница загрузки главной фотографии
        case "load_photo":
            NoAjaxQuery();
            $tpl->load_template('load_photo.tpl');
            $tpl->compile('content');
            AjaxTpl();
            die();
            break;

        //Сохранение основных данных
        case "save_general":
            NoAjaxQuery();

            $post_user_sex = intFilter('sex');
            if ($post_user_sex == 1 or $post_user_sex == 2)
                $user_sex = $post_user_sex;
            else
                $user_sex = false;

            $user_day = intFilter('day');
            $user_month = intFilter('month');
            $user_year = intFilter('year');
            $user_country = intFilter('country');
            $user_city = intFilter('city');
            $user_birthday = $user_year . '-' . $user_month . '-' . $user_day;

            if ($user_sex) {
                $post_sp = intFilter('sp');
                if ($post_sp >= 1 and $post_sp <= 7)
                    $sp = $post_sp;
                else
                    $sp = false;

                if ($sp) {
                    $sp_val = intFilter('sp_val');
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

            mozg_clear_cache_file('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);
            mozg_clear_cache();

            echo 'ok';

            die();
            break;

        //Сохранение контактов
        case "save_contact":
            NoAjaxQuery();

            $xfields = array();
            $xfields['vk'] = requestFilter('vk', 200);
            $xfields['od'] = requestFilter('od', 200);
            $xfields['phone'] = requestFilter('phone', 200);
            $xfields['skype'] = requestFilter('skype',  200);
            $xfields['fb'] = requestFilter('fb',  200);
            $xfields['icq'] = requestFilter('icq',  200);
            $xfields['site'] = requestFilter('site',  200);

            $xfieldsdata = '';
            foreach ($xfields as $name => $value) {
                $value = str_replace("|", "&#124;", $value);
                if (strlen($value) > 0)
                    $xfieldsdata .= $name . '|' . $value . '||';
            }

            $db->query("UPDATE `users` SET user_xfields = '{$xfieldsdata}' WHERE user_id = '{$user_info['user_id']}'");

            mozg_clear_cache_file('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);

            echo 'ok';

            die();

        //Сохранение интересов
        case "save_interests":
            NoAjaxQuery();

            $xfields = array();
            $xfields['activity'] = requestFilter('activity', 5000);
            $xfields['interests'] = requestFilter('interests',  5000);
            $xfields['myinfo'] = requestFilter('myinfo',  5000);
            $xfields['music'] = requestFilter('music',  5000);
            $xfields['kino'] = requestFilter('kino',  5000);
            $xfields['books'] = requestFilter('books',  5000);
            $xfields['games'] = requestFilter('games',  5000);
            $xfields['quote'] = requestFilter('quote',  5000);

            $xfieldsdata = '';
            foreach ($xfields as $name => $value) {
                $value = str_replace("|", "&#124;", $value);
                if (strlen($value) > 0)
                    $xfieldsdata .= $name . '|' . $value . '||';
            }

            $db->query("UPDATE `users` SET user_xfields_all = '{$xfieldsdata}' WHERE user_id = '{$user_info['user_id']}'");

            mozg_clear_cache_file('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);

            echo 'ok';

            die();

        //Сохранение доп.полей
        case "save_xfields":

            $xfields = profileload();

            $postedxfields = requestFilter('xfields');

            $newpostedxfields = array();

            $xfieldsdata = xfieldsdataload($xfieldsid);

            foreach ($xfields as $name => $value) {

                $newpostedxfields[$value[0]] = $postedxfields[$value[0]];

                if ($value[2] == "select") {
                    $options = explode("\r\n", $value[3]);

                    $newpostedxfields[$value[0]] = $options[$postedxfields[$value[0]]] . '|1';
                }

            }

            $postedxfields = $newpostedxfields;

            foreach ($postedxfields as $xfielddataname => $xfielddatavalue) {

                if (!$xfielddatavalue) {
                    continue;
                }

                $expxfielddatavalue = explode('|', $xfielddatavalue);

                if ($expxfielddatavalue[1])
                    $xfielddatavalue = str_replace('|1', '', textFilter($xfielddatavalue));
                else
                    $xfielddatavalue = textFilter($xfielddatavalue);

                if (isset($xfielddatavalue) and !empty($xfielddatavalue)) {
                    $xfielddataname = str_replace("|", "&#124;", $xfielddataname);
                    $xfielddatavalue = str_replace("|", "&#124;", $xfielddatavalue);
                    $filecontents[] = "$xfielddataname|$xfielddatavalue";
                }
            }

            if (isset($filecontents))
                $filecontents = implode("||", $filecontents);
            else
                $filecontents = '';

            $db->query("UPDATE `users` SET xfields = '{$filecontents}' WHERE user_id = '{$user_info['user_id']}'");

            mozg_clear_cache_file('user_' . $user_info['user_id'] . '/profile_' . $user_info['user_id']);

            exit;
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
            break;

        //Страница Редактирование доп.полей
        case "all":
            $user_speedbar = $lang['editmyprofile'] . ' &raquo; Другое';
            $tpl->load_template('editprofile.tpl');

            $xfields = profileload();

            $row = $db->super_query("SELECT xfields FROM `users` WHERE user_id = '" . $user_info['user_id'] . "'");

            $xfieldsdata = xfieldsdataload($row['xfields']);

            $output = '';
            $for_js_list = '';

            foreach ($xfields as $name => $value) {

                $fieldvalue = $xfieldsdata[$value[0]];
                $fieldvalue = stripslashes($fieldvalue);

                $output .= "<div class=\"texta\">{$value[1]}:</div>";

                $for_js_list .= "'xfields[{$value[0]}]': $('#{$value[0]}').val(), ";

                if ($value[2] == "textarea") {

                    $output .= '<textarea id="' . $value[0] . '" class="inpst" style="width:300px;height:50px;">' . myBrRn($fieldvalue) . '</textarea>';

                } elseif ($value[2] == "text") {

                    $output .= '<input type="text" id="' . $value[0] . '" class="inpst" maxlength="100" value="' . $fieldvalue . '" style="width:300px;" />';

                } elseif ($value[2] == "select") {

                    $output .= '<select class="inpst" id="' . $value[0] . '">';
                    $output .= '<option value="">- Не выбрано -</option>';

                    $variable = explode("\r\n", $value[3]);
                    foreach ($variable as $index => $value) {

                        $value = str_replace("'", "&#039;", $value);
                        $output .= "<option value=\"$index\"" . ($fieldvalue == $value ? " selected" : "") . ">$value</option>\r\n";

                    }

                    $output .= '</select>';

                }

                $output .= '<div class="mgclr"></div>';

            }

            $for_js_list = substr($for_js_list, 0, (strlen($for_js_list) - 2));

            $tpl->set('{xfields}', $output);
            $tpl->set('{for-js-list}', $for_js_list);

            $tpl->set_block("'\\[contact\\](.*?)\\[/contact\\]'si", "");
            $tpl->set_block("'\\[general\\](.*?)\\[/general\\]'si", "");
            $tpl->set_block("'\\[interests\\](.*?)\\[/interests\\]'si", "");
            $tpl->set('[xfields]', '');
            $tpl->set('[/xfields]', '');
            $tpl->compile('content');
            $tpl->clear();
            break;

        //Страница миниатюры
        case "miniature":

            $row = $db->super_query("SELECT user_photo FROM `users` WHERE user_id = '{$user_info['user_id']}'");

            if ($row['user_photo']) {

                $tpl->load_template('miniature/main.tpl');
                $tpl->set('{user-id}', $user_info['user_id']);
                $tpl->set('{ava}', $row['user_photo']);
                $tpl->compile('content');

                AjaxTpl();

            } else
                echo '1';

            exit();

            break;

        //Сохранение миниатюры
        case "miniature_save":

            $row = $db->super_query("SELECT user_photo FROM `users` WHERE user_id = '{$user_info['user_id']}'");

            $i_left = intFilter('i_left');
            $i_top = intFilter('i_top');
            $i_width = intFilter('i_width');
            $i_height = intFilter('i_height');

            if ($row['user_photo'] and $i_width >= 100 and $i_height >= 100 and $i_left >= 0) {

                include_once ENGINE_DIR . '/classes/images.php';

                $tmb = new thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/{$row['user_photo']}");
                $tmb->size_auto($i_width . "x" . $i_height, 0, "{$i_left}|{$i_top}");
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");

                $tmb = new thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");
                $tmb->size_auto("100x100", 1);
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");

                $tmb = new thumbnail(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/100_{$row['user_photo']}");
                $tmb->size_auto("50x50");
                $tmb->jpeg_quality(100);
                $tmb->save(ROOT_DIR . "/uploads/users/{$user_info['user_id']}/50_{$row['user_photo']}");

                echo $user_info['user_id'];

            } else
                echo 'err';

            exit();

            break;

//################### Загрузка обложки ###################//
        case "upload_cover":

            NoAjaxQuery();

            //Получаем данные о файле
            $image_tmp = $_FILES['uploadfile']['tmp_name'];
            $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для определения формата
            $image_rename = substr(md5($server_time + rand(1, 100000)), 0, 20); // имя файла
            $image_size = $_FILES['uploadfile']['size']; // размер файла
            $type = end(explode(".", $image_name)); // формат файла

            $max_size = 1024 * 7000;

            //Проверка размера
            if ($image_size <= $max_size) {

                //Разрешенные форматы
                $allowed_files = explode(', ', 'jpg, jpeg, jpe, png, gif');

                //Проверяем если, формат верный то пропускаем
                if (in_array(strtolower($type), $allowed_files)) {

                    $res_type = strtolower('.' . $type);

                    $upDir = ROOT_DIR . "/uploads/users/{$user_info['user_id']}/";

                    $rImg = $upDir . $image_rename . $res_type;

                    if (move_uploaded_file($image_tmp, $rImg)) {

                        //Подключаем класс для фотографий
                        include_once ENGINE_DIR . '/classes/images.php';

                        //Создание маленькой копии
                        $tmb = new thumbnail($rImg);
                        $tmb->size_auto('800', 1);
                        $tmb->jpeg_quality('100');
                        $tmb->save($rImg);

                        //Выводим и удаляем пред. обложку
                        $row = $db->super_query("SELECT user_cover FROM `users` WHERE user_id = '{$user_info['user_id']}'");
                        if ($row) {

                            Filesystem::delete($upDir . $row['user_cover']);

                        }

                        $imgData = getimagesize($rImg);
                        $rImgsData = round($imgData[1] / ($imgData[0] / 800));

                        //Обновляем обложку в базе
                        $pos = round(($rImgsData / 2) - 100);

                        if ($rImgsData <= 230) {
                            $rImgsData = 230;
                            $pos = 0;
                        }

                        $db->query("UPDATE `users` SET user_cover = '{$image_rename}{$res_type}', user_cover_pos = '{$pos}' WHERE user_id = '{$user_info['user_id']}'");

                        echo $user_info['user_id'] . '/' . $image_rename . $res_type . '|' . $rImgsData;

                        //Чистим кеш
                        mozg_clear_cache_file("user_{$user_info['user_id']}/profile_{$user_info['user_id']}");

                    }

                } else
                    echo 2;

            } else
                echo 1;

            exit();

            break;

        //################### Сохранение новой позиции обложки ###################//
        case "savecoverpos":
            NoAjaxQuery();
            $pos = intFilter('pos');
            $db->query("UPDATE `users` SET user_cover_pos = '{$pos}' WHERE user_id = '{$user_info['user_id']}'");

            //Чистим кеш
            mozg_clear_cache_file("user_{$user_info['user_id']}/profile_{$user_info['user_id']}");
            exit();
            break;

        //################### Удаление обложки ###################//
        case "delcover":

            NoAjaxQuery();

            //Выводим и удаляем пред. Обложку
            $row = $db->super_query("SELECT user_cover FROM `users` WHERE user_id = '{$user_info['user_id']}'");
            if ($row) {
                $upDir = ROOT_DIR . "/uploads/users/{$user_info['user_id']}/";
                Filesystem::delete($upDir . $row['user_cover']);
            }

            $db->query("UPDATE `users` SET user_cover_pos = '', user_cover = '' WHERE user_id = '{$user_info['user_id']}'");

            //Чистим кеш
            mozg_clear_cache_file("user_{$user_info['user_id']}/profile_{$user_info['user_id']}");

            exit();

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
            foreach ($sql_country as $row_country)
                $all_country .= '<option value="' . $row_country['id'] . '">' . stripslashes($row_country['name']) . '</option>';

            $tpl->set('{country}', installationSelected($row['user_country'], $all_country));

            //################## Загружаем Города ##################//
            $sql_city = $db->super_query("SELECT id, name FROM `city` WHERE id_country = '{$row['user_country']}' ORDER by `name` ASC", true);
            $all_city = '';
            foreach ($sql_city as $row2)
                $all_city .= '<option value="' . $row2['id'] . '">' . stripslashes($row2['name']) . '</option>';

            $tpl->set('{city}', installationSelected($row['user_city'], $all_city));

            $user_sp = explode('|', $row['user_sp']);
            if ($user_sp[1]) {
                $rowSp = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '{$user_sp[1]}'");
                $tpl->set('{sp-name}', $rowSp['user_search_pref']);
                $tpl->set_block("'\\[sp\\](.*?)\\[/sp\\]'si", "");

                if ($row['user_sex'] == 1) {
                    if ($user_sp[0] == 2)
                        $tpl->set('{sp-text}', 'Подруга:');
                    elseif ($user_sp[0] == 3)
                        $tpl->set('{sp-text}', 'Невеста:');
                    else if ($user_sp[0] == 4)
                        $tpl->set('{sp-text}', 'Жена:');
                    else if ($user_sp[0] == 5)
                        $tpl->set('{sp-text}', 'Любимая:');
                    else
                        $tpl->set('{sp-text}', 'Партнёр:');
                } else {
                    if ($user_sp[0] == 2)
                        $tpl->set('{sp-text}', 'Друг:');
                    elseif ($user_sp[0] == 3)
                        $tpl->set('{sp-text}', 'Жених:');
                    else if ($user_sp[0] == 4)
                        $tpl->set('{sp-text}', 'Муж:');
                    else if ($user_sp[0] == 5)
                        $tpl->set('{sp-text}', 'Любимый:');
                    else
                        $tpl->set('{sp-text}', 'Партнёр:');
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
    }

} else {
    $user_speedbar = 'Информация';
    msgbox('', $lang['not_logged'], 'info');
}