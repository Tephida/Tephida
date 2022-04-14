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

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $user_speedbar = $lang['blog_descr'];
    $db = Registry::get('db');

    switch ($act) {

        //################### Страница добавления ###################//
        case "add":
            if ($user_info['user_group'] == 1) {
                $tpl->load_template('blog/add.tpl');
                $tpl->compile('content');
            }

            compile($tpl);
            break;

        //################### Добавление новости в БД ###################//
        case "send":
            NoAjaxQuery();
            if ($user_info['user_group'] == 1) {
                //Подключаем парсер
                include ENGINE_DIR . '/classes/Parse.php';
                $parse = new parse();

                $title = (new Request)->filter('title', 25000, true);
                $text = $parse->BBparse((new Request)->filter('text'));

                function BBimg($source)
                {
                    return "<img src=\"{$source}\" alt=\"\" />";
                }

                $text = preg_replace("#\\[img\\](.*?)\\[/img\\]#ies", "\BBimg('\\1')", $text);

                if (!empty($title) and !empty($text))
                    $db->query("INSERT INTO `blog` SET title = '{$title}', story = '{$text}', date = '{$server_time}'");
            }

            break;

        //################### Удаление новости в БД ###################//
        case "del":
            NoAjaxQuery();
            if ($user_info['user_group'] == 1) {
                $id = (new Request)->int('id');
                $db->query("DELETE FROM `blog` WHERE id = '{$id}'");
            }
            break;

        //################### Страница редактирования ###################//
        case "edit":
            if ($user_info['user_group'] == 1) {
                $id = (new Request)->int('id');
                $row = $db->super_query("SELECT title, story FROM `blog` WHERE id = '{$id}'");
                if ($row) {
                    //Подключаем парсер
                    include ENGINE_DIR . '/classes/Parse.php';
                    $parse = new parse();

                    function BBdecodeImg($source)
                    {
                        return '[img]' . $source . '[/img]';
                    }

                    $row['story'] = preg_replace("#\\<img src=\"(.*?)\\\" alt=\"\" />#ies", "\BBdecodeImg('\\1')", $row['story']);

                    $tpl->load_template('blog/edit.tpl');
                    $tpl->set('{story}', $parse->BBdecode(stripslashes(myBrRn($row['story']))));
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{id}', $id);
                    $tpl->compile('content');
                } else {
                }
            } else {
            }

            compile($tpl);
            break;

        //################### Сохранение отредактированых ###################//
        case "save":
            NoAjaxQuery();
            if ($user_info['user_group'] == 1) {
                //Подключаем парсер
                include ENGINE_DIR . '/classes/Parse.php';
                $parse = new parse();

                $title = (new Request)->filter('title', 25000, true);
                $text = $parse->BBparse((new Request)->filter('text'));
                $id = (new Request)->int('id');

                function BBimg($source)
                {
                    return "<img src=\"{$source}\" alt=\"\" />";
                }

                $text = preg_replace("#\\[img\\](.*?)\\[/img\\]#ies", "\BBimg('\\1')", $text);

                if (!empty($title) and !empty($text))
                    $db->query("UPDATE `blog` SET title = '{$title}', story = '{$text}' WHERE id = '{$id}'");
            }

            break;

        //################### Загрузка фотографии ###################//
        case "upload":
            NoAjaxQuery();
            if ($user_info['user_group'] == 1) {
                //Если нет папки альбома, то создаём её
                $album_dir = ROOT_DIR . "/uploads/blog/";

                //Разрешенные форматы
                $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

                //Получаем данные о фотографии
                $image_tmp = $_FILES['uploadfile']['tmp_name'];
                $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
                $image_rename = substr(md5($server_time + rand(1, 100000)), 0, 20); // имя фотографии
                $image_size = $_FILES['uploadfile']['size']; // размер файла
                $type = end(explode(".", $image_name)); // формат файла

                //Проверяем если, формат верный то пропускаем
                if (in_array(strtolower($type), $allowed_files)) {
                    if ($image_size < 5000000) {
                        $res_type = strtolower('.' . $type);

                        if (move_uploaded_file($image_tmp, $album_dir . $image_rename . $res_type)) {
                            //Создание оригинала
                            $tmb = new Thumbnail($album_dir . $image_rename . $res_type);
                            $tmb->size_auto('570', 1);
                            $tmb->jpeg_quality('100');
                            $tmb->save($album_dir . $image_rename . $res_type);
                            $config = settings_get();
                            $img_url = $config['home_url'] . 'uploads/blog/' . $image_rename . $res_type;

                            //Результат для ответа
                            echo $img_url;
                        } else
                            echo 'big_size';
                    } else
                        echo 'big_size';
                } else
                    echo 'bad_format';
            }

            break;

        default:
            $id = (new Request)->int('id');
            if ($id) {
                $sqlWhere = "WHERE id = '{$id}'";
            } else {
                $sqlWhere = '';
            }

            //Вывод последней новости
            $row = $db->super_query("SELECT id, title, story, date FROM `blog` {$sqlWhere} ORDER by `date` DESC");
            if (!$row)
                $row = $db->super_query("SELECT id, title, story, date FROM `blog` ORDER by `date` DESC");

            $tpl->load_template('blog/story.tpl');
            $date_str = megaDate($row['date'], 1, 1);
            $tpl->set('{date}', $date_str);
            $tpl->set('{story}', stripslashes($row['story']));
            $tpl->set('{title}', stripslashes($row['title']));
            $tpl->set('{id}', $row['id']);

            //Вывод последних 20 новостей
            $sql_ = $db->super_query("SELECT id, title FROM `blog` ORDER by `date` DESC LIMIT 0, 20", true);
            $cnt = 0;
            $lastNews = '';
            foreach ($sql_ as $rowLast) {
                $cnt++;
                $rowLast['title'] = stripslashes($rowLast['title']);

                if ((new Request)->int('id') == $rowLast['id'] or $cnt == 1 and !(new Request)->int('id'))
                    $lastNews .= "<div><a href=\"/blog?id={$rowLast['id']}\" class=\"bloglnkactive\" onClick=\"Page.Go(this.href); return false\">{$rowLast['title']}</a></div>";
                else
                    $lastNews .= "<a href=\"/blog?id={$rowLast['id']}\" onClick=\"Page.Go(this.href); return false\">{$rowLast['title']}</a>";
            }

            $tpl->set('{last-news}', $lastNews);

            $tpl->compile('content');
            compile($tpl);
    }
//    $tpl->clear();
//    $db->free();
} else {
//	$user_speedbar = $lang['no_infooo'];

    $tpl->load_template('info.tpl');
    $tpl->set('{error}', $lang['not_logged']);
    $tpl->set('{title}', '');
    $tpl->compile('content');
    $tpl->clear();
//    $db->free();
    compile($tpl);
//	msgbox('', $lang['not_logged'], 'info');
}