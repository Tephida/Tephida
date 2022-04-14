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
use Mozg\classes\{Flood};

NoAjaxQuery();

if (Registry::get('logged')) {
    $lang = Registry::get('lang');
    $db = Registry::get('db');
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $server_time = Registry::get('server_time');
//	$act = $_GET['act'];y
    $user_id = $user_info['user_id'];
    $metatags['title'] = $lang['support_title'];
    $page = (new Request)->int('page', 1);
    $gcount = 20;
    $limit_page = ($page - 1) * $gcount;

    switch ($act) {

        //################### Страница создание нового вопроса  ###################//
        case "new":
            $meta_tags['title'] = 'Новый вопрос';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            $mobile_speedbar = 'Новый вопрос';
            $tpl->load_template('support/new.tpl');
            $tpl->set('{uid}', $user_id);
            $tpl->compile('content');

            $tpl->render();
            break;

        //################### Отправка нового вопроса  ###################//
        case "send":
            NoAjaxQuery();
            if (Flood::check('support')) {
                echo 'limit';
            } else {
                $title = (new Request)->filter('title', 25000, true);
                $question = (new Request)->filter('question');
                $limitTime = $server_time - 3600;
                $rowLast = $db->super_query("SELECT COUNT(*) AS cnt FROM `support` WHERE сdate > '{$limitTime}'");
                if (!$rowLast['cnt'] and !empty($title) and !empty($question) and $user_info['user_group'] != 4) {
                    Flood::LogInsert('support');
                    $question = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $question);
                    $db->query("INSERT INTO `support` SET title = '{$title}', question = '{$question}', suser_id = '{$user_id}', sfor_user_id = '{$user_id}', sdate = '{$server_time}', сdate = '{$server_time}'");
                    $dbid = $db->insert_id();
                    $row = $db->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$user_id}'");

                    $meta_tags['title'] = 'Новый вопрос';
                    $config = settings_get();
                    $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
                    $tpl = new TpLSite($tpl_dir_name, $meta_tags);

                    $tpl->load_template('support/show.tpl');
                    $tpl->set('{title}', stripslashes($title));
                    $tpl->set('{question}', stripslashes($question));
                    $tpl->set('{qid}', $dbid);
                    $date_str = megaDate($server_time);
                    $tpl->set('{date}', $date_str);
                    $tpl->set('{status}', 'Вопрос ожидает обработки.');
                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{uid}', $user_id);
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $user_id . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $tpl->set('{answers}', '');
                    $tpl->compile('content');
                    AjaxTpl($tpl);
                    echo 'r|x' . $dbid;
                } else {
                    echo 'limit';
                }
            }


            break;

        //################### Удаление вопроса  ###################//
        case "delet":
            NoAjaxQuery();
            $qid = (new Request)->int('qid');
            $row = $db->super_query("SELECT suser_id FROM `support` WHERE id = '{$qid}'");
            if ($row['suser_id'] == $user_id || $user_info['user_group'] == 4) {
                $db->query("DELETE FROM `support` WHERE id = '{$qid}'");
                $db->query("DELETE FROM `support_answers` WHERE qid = '{$qid}'");
            }

            break;

        //################### Удаление Ответа  ###################//
        case "delet_answer":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $row = $db->super_query("SELECT auser_id FROM `support_answers` WHERE id = '{$id}'");
            if ($row['auser_id'] == $user_id || $user_info['user_group'] == 4) {
                $db->query("DELETE FROM `support_answers` WHERE id = '{$id}'");
            }

            break;

        //################### Закрытие вопроса  ###################//
        case "close":
            NoAjaxQuery();
            $qid = (new Request)->int('qid');
            if ($user_info['user_group'] == 4) {
                $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `support` WHERE id = '{$qid}'");
                if ($row['cnt']) {
                    $db->query("UPDATE `support` SET sfor_user_id = 0 WHERE id = '{$qid}'");
                }
            }

            break;

        //################### Отправка ответа ###################//
        case "answer":
            NoAjaxQuery();
            $qid = (new Request)->int('qid');
            $answer = (new Request)->filter('answer');
            $check = $db->super_query("SELECT suser_id FROM `support` WHERE id = '{$qid}'");
            if ($check['suser_id'] == $user_id or $user_info['user_group'] == 4 and isset($answer) and !empty($answer)) {
                if ($user_info['user_group'] == 4) {
                    $auser_id = 0;
                    $db->query("UPDATE `users` SET user_support = user_support+1 WHERE user_id = '{$check['suser_id']}'");
                } else {
                    $auser_id = $user_id;
                }

                $answer = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $answer);

                $db->query("INSERT INTO `support_answers` SET qid = '{$qid}', auser_id = '{$auser_id}', adate = '{$server_time}', answer = '{$answer}'");
                $db->query("UPDATE `support` SET sfor_user_id = '{$auser_id}', sdate = '{$server_time}' WHERE id = '{$qid}'");

                $row = $db->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$user_id}'");

                $meta_tags['title'] = 'Новый вопрос';
                $config = settings_get();
                $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
                $tpl = new TpLSite($tpl_dir_name, $meta_tags);

                $tpl->load_template('support/answer.tpl');
                if (!$auser_id) {
                    $tpl->set('{name}', 'Агент поддержки');
                    $tpl->set('{ava}', '/images/support.png');
                    $tpl->set_block("'\\[no-agent\\](.*?)\\[/no-agent\\]'si", "");
                } else {
                    $tpl->set('{name}', $row['user_search_pref']);
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $user_id . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }

                    $tpl->set('[no-agent]', '');
                    $tpl->set('[/no-agent]', '');
                }

                if ($auser_id == $user_id || $user_info['user_group'] == 4) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                } else {
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }

                $tpl->set('{uid}', $user_id);
                $tpl->set('{answer}', stripslashes($answer));
                $date_str = megaDate($server_time);
                $tpl->set('{date}', $date_str);
                $tpl->compile('content');
                AjaxTpl($tpl);
            }

            break;

        //################### Просмотр вопроса ###################//
        case "show":

            break;

        //################### Просмотр всех вопросов ###################//
        default:
            $mobile_speedbar = 'Помощь';

            $meta_tags['title'] = 'Помощь';
            $config = settings_get();
            $tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
            $tpl = new TpLSite($tpl_dir_name, $meta_tags);

            if ($user_info['user_support'] and $user_info['user_group'] != 4) {
                $db->query("UPDATE `users` SET user_support = 0 WHERE user_id = '{$user_id}'");
            }

            if ($user_info['user_group'] == 4) {
                $sql_where = "ORDER by `sdate` DESC";
                $sql_where_cnt = "";
            } else {
                $sql_where = "AND tb1.suser_id = '{$user_id}' ORDER by `sdate` DESC";
                $sql_where_cnt = "WHERE suser_id = '{$user_id}'";
            }

            $sql_ = $db->super_query("SELECT tb1.id, title, suser_id, sfor_user_id, sdate, tb2.user_photo, user_search_pref FROM `support` tb1, `users` tb2 WHERE tb1.suser_id = tb2.user_id {$sql_where} LIMIT {$limit_page}, {$gcount}", true);

            $tpl->load_template('support/head.tpl');
            if ($sql_) {
                $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `support` {$sql_where_cnt}");

                if ($user_info['user_group'] == 4) {
                    $tpl->set('{cnt}', $count['cnt'] . ' ' . declWord($count['cnt'], 'questions'));
                } else {
                    $tpl->set('{cnt}', 'Вы задали ' . $count['cnt'] . ' ' . declWord($count['cnt'], 'questions'));
                }
            } else {
                $tpl->set('{cnt}', '');
            }

            $tpl->compile('info');

            if ($sql_) {
                $tpl->load_template('support/question.tpl');
                foreach ($sql_ as $row) {
                    $tpl->set('{title}', stripslashes($row['title']));
                    $date_str = megaDate($row['sdate']);
                    $tpl->set('{date}', $date_str);
                    if ($row['sfor_user_id'] == $row['suser_id'] or $user_info['user_group'] == 4) {
                        if ($row['sfor_user_id'] == $row['suser_id']) {
                            $tpl->set('{status}', 'Вопрос ожидает обработки.');
                        } else {
                            $tpl->set('{status}', 'Есть ответ.');
                        }
                        $tpl->set('{name}', $row['user_search_pref']);
                        $tpl->set('{answer}', '');
                        if ($row['user_photo']) {
                            $tpl->set('{ava}', '/uploads/users/' . $row['suser_id'] . '/50_' . $row['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }
                    } else {
                        $tpl->set('{name}', 'Агент поддержки');
                        $tpl->set('{status}', 'Есть ответ.');
                        $tpl->set('{ava}', '/images/support.png');
                        $tpl->set('{answer}', 'ответил');
                    }
                    $tpl->set('{qid}', $row['id']);
                    $tpl->compile('content');
                }
                navigation($gcount, $count['cnt'], '/support?page=');

//                compile($tpl);
            } else if ($user_info['user_group'] == 4) {
                msgbox('', $lang['support_no_quest3'], 'info_2');
            } else {
                msgbox('', $lang['support_no_quest2'], 'info_2');
            }
            $tpl->render();
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}