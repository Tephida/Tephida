<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Registry};
use FluffyDollop\Filesystem\Filesystem;
use FluffyDollop\Http\Request;
use Mozg\classes\Cache;
use Mozg\classes\download;

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################### Загрузка файла ###################//
        case "upload":
            NoAjaxQuery();

            //Получаем данные о фотографии
            $file_tmp = $_FILES['uploadfile']['tmp_name'];
            $file_name = $_FILES['uploadfile']['name']; // оригинальное название для определения формата
            $file_size = $_FILES['uploadfile']['size']; // размер файла
            $type = end(explode(".", $file_name)); // формат файла

            //Разрешенные форматы
            $allowed_files = array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'rtf', 'pdf', 'png', 'jpg', 'gif', 'psd', 'mp3', 'djvu', 'fb2', 'ps', 'jpeg', 'txt');

            //Проверяем если, формат верный то пропускаем
            if (in_array(strtolower($type), $allowed_files)) {

                if ($file_size < 10000000) {

                    $res_type = strtolower('.' . $type);

                    //Директория загрузки
                    $upload_dir = ROOT_DIR . "/uploads/doc/{$user_id}/";

                    //Если нет папки юзера, то создаём её
                    Filesystem::createDir($upload_dir);

                    $downl_file_name = substr(md5($file_name . random_int(0, 1000) . $server_time), 0, 25);

                    //Загружаем сам файл
                    if (move_uploaded_file($file_tmp, $upload_dir . $downl_file_name . $res_type)) {

                        $dsize = Filesystem::formatsize($file_size);
                        $file_name = (new Request)->textFilter($file_name, 25000, true);

                        //Обновляем кол-во док. у юзера
                        $db->query("UPDATE `users` SET user_doc_num = user_doc_num+1 WHERE user_id = '{$user_id}'");

                        if (!$file_name) {
                            $file_name = 'Без названия.' . $res_type;
                        }

                        $strLn = strlen($file_name);
                        if ($strLn > 50) {
                            $file_name = str_replace('.' . $res_type, '', $file_name);
                            $file_name = substr($file_name, 0, 50) . '...' . $res_type;
                        }

                        //Вставляем файл в БД
                        $db->query("INSERT INTO `doc` SET duser_id = '{$user_id}', dname = '{$file_name}', dsize = '{$dsize}', ddate = '{$server_time}', ddownload_name = '{$downl_file_name}{$res_type}'");

                        echo $file_name . '"' . $db->insert_id() . '"' . $dsize . '"' . strtolower($type) . '"' . langdate('сегодня в H:i', $server_time);

                        Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|user_{$user_id}/docs");

                    }

                } else {
                    echo 1;
                }

            }
            break;

        //################### Удаление документа ###################//
        case "del":
            NoAjaxQuery();

            $did = (new Request)->int('did');

            $row = $db->super_query("SELECT duser_id, ddownload_name FROM `doc` WHERE did = '{$did}'");

            if ($row['duser_id'] == $user_id) {

                Filesystem::delete(ROOT_DIR . "/uploads/doc/{$user_id}/" . $row['ddownload_name']);

                $db->query("DELETE FROM `doc` WHERE did = '{$did}'");

                //Обновляем кол-во док. у юзера
                $db->query("UPDATE `users` SET user_doc_num = user_doc_num-1 WHERE user_id = '{$user_id}'");

                Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|user_{$user_id}/docs");
                Cache::mozgClearCacheFile("wall/doc{$did}");

            }

            break;

        //################### Сохранение отред.данных ###################//
        case "editsave":
            NoAjaxQuery();

            $did = (new Request)->int('did');
            $name = (new Request)->filter('name', 25000, true);
            $strLn = strlen($name);
            if ($strLn > 50) {
                $name = substr($name, 0, 50);
            }

            $row = $db->super_query("SELECT duser_id FROM `doc` WHERE did = '{$did}'");

            if ($row['duser_id'] == $user_id and !empty($name)) {

                $db->query("UPDATE `doc`SET dname = '{$name}' WHERE did = '{$did}'");

                Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|user_{$user_id}/docs");
                Cache::mozgClearCacheFile("wall/doc{$did}");

            }

            break;


        //################### Скачивание документа с сервера ###################//
        case "download":

            NoAjaxQuery();

            $did = (new Request)->int('did');

            $row = $db->super_query("SELECT duser_id, ddownload_name, dname FROM `doc` WHERE did = '{$did}'");

            if ($row) {
                $filename = str_replace(array('/', '\\', 'php', 'tpl'), '', $row['ddownload_name']);
                define('FILE_DIR', "uploads/doc/{$row['duser_id']}/");
                include ENGINE_DIR . '/classes/download.php';
                $config['files_max_speed'] = 0;
                $format = end(explode('.', $filename));
                $row['dname'] = str_replace('.' . $format, '', $row['dname']) . '.' . $format;
                if (file_exists(FILE_DIR . $filename) and $filename) {
                    $file = new download(FILE_DIR . $filename, $row['dname'], 1, $config['files_max_speed']);
                    $file->download_file();
                }
            } else {
                header("Location: /");
            }

            break;

        //################### Страница всех загруженных документов ###################//
        case "list":

            $metatags['title'] = 'Документы';

            $sql_limit = 20;
            $page_cnt = (new Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt = $page_cnt * $sql_limit;
            } else {
                $page_cnt = 0;
            }

            if ($page_cnt) {
                NoAjaxQuery();
            }

            $sql_ = $db->super_query("SELECT did, dname, ddate, ddownload_name, dsize FROM `doc` WHERE duser_id = '{$user_id}' ORDER by `ddate` DESC LIMIT {$page_cnt}, {$sql_limit}", true);

            $rowUser = $db->super_query("SELECT user_doc_num FROM `users` WHERE user_id = '{$user_id}'");

            if (!$page_cnt) {

                $tpl->load_template('doc/top_list.tpl');
                $tpl->set('{doc-num}', $rowUser['user_doc_num']);
                $tpl->compile('content');

            }

            $tpl->load_template('doc/doc_list.tpl');
            foreach ($sql_ as $row) {

                $tpl->set('{name}', stripslashes($row['dname']));
                $tpl->set('{format}', end(explode('.', $row['ddownload_name'])));
                $tpl->set('{did}', $row['did']);
                $tpl->set('{size}', $row['dsize']);
                $date_str = megaDate($row['ddate']);
                $tpl->set('{date}', $date_str);
                $tpl->compile('content');
            }

            if ($page_cnt) {
                AjaxTpl($tpl);
            }

            if ($rowUser['user_doc_num'] > 20) {

                $tpl->load_template('doc/bottom_list.tpl');
                $tpl->compile('content');

            }

            compile($tpl);
            break;

        //################### Страница всех загруженных документов для прикрепления BOX ###################//
        default:

            NoAjaxQuery();

            $sql_limit = 20;
            $page_cnt = (new Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt *= $sql_limit;
            } else {
                $page_cnt = 0;
            }

            $sql_ = $db->super_query("SELECT did, dname, ddate, ddownload_name FROM `doc` WHERE duser_id = '{$user_id}' ORDER by `ddate` DESC LIMIT {$page_cnt}, {$sql_limit}", true);

            if (!$page_cnt) {
                $rowUser = $db->super_query("SELECT user_doc_num FROM `users` WHERE user_id = '{$user_id}'");

                $tpl->load_template('doc/top.tpl');
                $tpl->set('{doc-num}', $rowUser['user_doc_num']);
                $tpl->compile('content');
            }

            $tpl->load_template('doc/doc.tpl');
            foreach ($sql_ as $row) {

                $tpl->set('{name}', stripslashes($row['dname']));
                $tpl->set('{format}', end(explode('.', $row['ddownload_name'])));
                $tpl->set('{did}', $row['did']);
                $date_str = megaDate($row['ddate']);
                $tpl->set('{date}', $date_str);
                $tpl->compile('content');
            }

            if (!$page_cnt && $rowUser['user_doc_num'] > 20) {
                $tpl->load_template('doc/bottom.tpl');
                $tpl->compile('content');
            }

            AjaxTpl($tpl);

    }

    $tpl->clear();
//    $db->free();

} else {
    echo 'no_log';
}