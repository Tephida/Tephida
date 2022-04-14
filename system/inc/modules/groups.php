<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Filesystem, Registry};
use Mozg\classes\TplCp;

$db = Registry::get('db');

//Редактирование
$act = (new \FluffyDollop\Http\Request)->filter('act');
if ($act === 'edit') {
    $id = (new \FluffyDollop\Http\Request)->int('id');

    //SQL Запрос на вывод информации
    $row = $db->super_query("SELECT title, descr, comments, photo FROM `communities` WHERE id = '" . $id . "'");
    if ($row) {
        if (isset($_POST['save'])) {
            $title = (new \FluffyDollop\Http\Request)->filter('title', 25000, true);
            $descr = (new \FluffyDollop\Http\Request)->filter('descr');

            if (!empty($title) && !empty($descr)) {
                if ($_POST['comments']) {
                    $comments = 1;
                } else {
                    $comments = 0;
                }

                if ($_POST['del_photo']) {
                    Filesystem::delete(ROOT_DIR . '/uploads/groups/' . $id . '/' . $row['photo']);
                    $sql_line_del = ", photo = ''";
                } else {
                    $sql_line_del = '';
                }

                $db->query("UPDATE `communities` SET title = '" . $title . "', descr = '" . $descr . "', comments = '" . $comments . "' " . $sql_line_del . " WHERE id = '" . $id . "'");

                $tpl = new TplCp(ADMIN_DIR . '/tpl/');
                $tpl->load_template('info/info_red.tpl');
                $tpl->set('{error}', 'Сообщество успешно отредактировано');
                $tpl->set('{admin_link}', $admin_link ?? '');
                $tpl->set('{title}', 'Информация');
                $tpl->compile('content');
                $tpl->render();
            } else {
                $tpl = new TplCp(ADMIN_DIR . '/tpl/');
                $tpl->load_template('info/info_red.tpl');
                $tpl->set('{error}', 'Заполните все поля');
                $tpl->set('{admin_link}', $admin_link ?? '');
                $tpl->set('{title}', 'Информация');
                $tpl->compile('content');
                $tpl->render();
            }
        } else {
            $row['title'] = stripslashes($row['title']);
            $row['descr'] = stripslashes(myBrRn($row['descr']));

            if ($row['comments']) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            $tpl = new TplCp(ADMIN_DIR . '/tpl/');
            $tpl->load_template('groups/edit.tpl');
            $tpl->set('{title}', $row['title']);
            $tpl->set('{descr}', $row['descr']);
            $tpl->set('{checked}', $checked);
            $tpl->set('{groups_num}', '');
            $config = settings_get();
            $tpl->set('{admin_index}', $config['admin_index']);
            $tpl->compile('content');
            $tpl->render();
        }
    } else {
        $tpl = new TplCp(ADMIN_DIR . '/tpl/');
        $tpl->load_template('info/info_red.tpl');
        $tpl->set('{error}', 'Сообщество не найдено');
        $tpl->set('{admin_link}', $admin_link ?? '');
        $tpl->set('{title}', 'Информация');
        $tpl->compile('content');
        $tpl->render();
    }
} else {
    $se_uid = (new \FluffyDollop\Http\Request)->int('se_uid');
    $se_user_id = (new \FluffyDollop\Http\Request)->int('se_user_id');
    $sort = (new \FluffyDollop\Http\Request)->int('sort');
    $se_name = (new \FluffyDollop\Http\Request)->filter('se_name', 25000, true);
    $ban = (new \FluffyDollop\Http\Request)->filter('ban');
    $delete = (new \FluffyDollop\Http\Request)->filter('delete');

    $where_sql = '';
    if ($se_uid || $sort || $se_name || $se_user_id || $ban || $delete) {
        if ($se_uid) {
            $where_sql .= "AND id = '" . $se_uid . "' ";
        }
        if ($se_user_id) {
            $where_sql .= "AND real_admin = '" . $se_user_id . "' ";
        }
        $query = strtr($se_name, array(' ' => '%')); //Заменяем пробелы на проценты чтоб поиск был точнее
        if ($se_name) {
            $where_sql .= "AND title LIKE '%" . $query . "%' ";
        }
        if ($ban) {
            $where_sql .= "AND ban = 1 ";
            $checked_ban = "checked";
        }
        if ($delete) {
            $where_sql .= "AND del = 1 ";
            $checked_delete = "checked";
        }
        if ($sort === 5) {
            $where_sql = "AND photo != '' ";
        }
        if ($sort === 1) {
            $order_sql = "`title` ASC";
        } else if ($sort === 2) {
            $order_sql = "`date` ASC";
        } else if ($sort === 3) {
            $order_sql = "`traf` DESC";
        } else if ($sort === 4) {
            $order_sql = "`rec_num` DESC";
        } else {
            $order_sql = "`date` DESC";
        }
    } else {
        $order_sql = "`date` DESC";
    }

    $checked_ban = $checked_ban ?? "checked";
    $checked_delete = $checked_delete ?? "checked";

    //Выводим список людей
    $page = (new \FluffyDollop\Http\Request)->int('page', 1);
    $g_count = 20;
    $limit_page = ($page - 1) * $g_count;

    $sql_ = $db->super_query("SELECT tb1.id, title, date, traf, real_admin, del, ban, rec_num, tb2.user_name FROM `communities` tb1, `users` tb2 WHERE tb1.real_admin = tb2.user_id {$where_sql} ORDER by {$order_sql} LIMIT {$limit_page}, {$g_count}", 1);

//Кол-во людей считаем
    $numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities` WHERE id != '' {$where_sql}");


    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
    $tpl->load_template('groups/groups.tpl');
    $users = '';
    foreach ($sql_ as $row) {
        $row['title'] = stripslashes($row['title']);
        $row['date'] = langdate('j M Y в H:i', strtotime($row['date']));

        if ($row['del']) {
            $color = 'color:red';
        } else if ($row['ban']) {
            $color = 'color:blue';
        } else {
            $color = '';
        }
        $tpl->set('{real_admin}', $row['real_admin']);
        $tpl->set('{user_name}', $row['user_name']);
        $tpl->set('{id}', $row['id']);
        $tpl->set('{rec_num}', $row['rec_num']);
        $tpl->set('{traf}', $row['traf']);
        $tpl->set('{date}', $row['date']);
        $tpl->set('{title}', $row['title']);
        $tpl->set('{color}', $color);

        $tpl->compile('groups');
    }

    $query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);

    $tpl->load_template('groups/main.tpl');
    $tpl->set('{groups}', $tpl->result['groups'] ?? '');
    $tpl->set('{navigation}', navigationNew($g_count, $numRows['cnt'], '?' . $query_string . '&page='));
    $selsorlist = installationSelected($sort, '<option value="1">по алфавиту</option><option value="2">по дате создания</option><option value="3">по количеству участников</option><option value="4">по количеству записей на стене</option><option value="5">только с фото</option>');
    $tpl->set('{selsorlist}', $selsorlist);
    $tpl->set('{se_uid}', $se_uid);
    $tpl->set('{se_name}', $se_name);
    $tpl->set('{se_user_id}', $se_user_id);
    $tpl->set('{checked_ban}', $checked_ban);
    $tpl->set('{checked_delet}', $checked_delete);
    $tpl->set('{groups_num}', $numRows['cnt']);
    $config = settings_get();
    $tpl->set('{admin_index}', $config['admin_index']);
    $tpl->compile('content');
    $tpl->render();
}