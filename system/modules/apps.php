<?php
/*
	Appointment: Игры
	File: apps.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if (!defined('MOZG')) die('Hacking attempt!');
if ($logged) {
    $act = $_GET['act'];
    $user_id = $user_info['user_id'];
    switch ($act) {
            //################### Страница приложения ###################//
            
        case "view":
            NoAjaxQuery();
            $id = intval($_POST['id']);
            //Выводим данные о игре
            $row = $db->super_query("SELECT id, title, poster, descr, traf FROM `" . PREFIX . "_games` WHERE id = '{$id}'");
            $tpl->load_template('apps/view.tpl');
            if ($row) {
                if ($row['poster']) $tpl->set('{poster}', "/uploads/apps/{$row['id']}/{$row['poster']}");
                else $tpl->set('{poster}', "/uploads/no_app.gif");
                $tpl->set('{title}', stripslashes($row['title']));
                $tpl->set('{descr}', stripslashes($row['descr']));
                if ($row['traf']) $tpl->set('{traf}', 'Игру ' . newGram($row['traf'], 'установил', 'установили', 'установили', true) . ' ' . newGram($row['traf'], 'человек', 'человека', 'человек'));
                else $tpl->set('{traf}', 'Пока никто не установил эту игру');
                $tpl->set('{app-id}', $row['id']);
                //Выводим скрины
                $sql_imgs = $db->super_query("SELECT file FROM `" . PREFIX . "_games_files` WHERE game_id = '{$row['id']}' AND type = 'scrin'", 1);
                if ($sql_imgs) {
                    $ci = 0;
                    $tpl->set('{m-poster-1}', '');
                    $tpl->set('{m-poster-2}', '');
                    $tpl->set('{m-poster-3}', '');
                    $tpl->set('{m-poster-4}', '');
                    $tpl->set('[poster]', '');
                    $tpl->set('[/poster]', '');
                    foreach ($sql_imgs as $row_img) {
                        $ci++;
                        $tpl->set("{poster-{$ci}}", "/uploads/apps/{$row['id']}/{$row_img['file']}");
                        if ($ci == 1) $opacity = 1;
                        else $opacity = 0.5;
                        $tpl->set("{m-poster-{$ci}}", "<img src=\"/uploads/apps/{$row['id']}/m{$row_img['file']}\" width=\"145\" height=\"90\" id=\"apmpos{$ci}\" onClick=\"apps.gallery({$ci}, this.id)\" style=\"opacity:{$opacity}\" />");
                    }
                } else {
                    $tpl->set_block("'\\[poster](.*?)\\[/poster]'si", "");
                    $tpl->set_block("'\\{m-poster-(.*?)\\}'si", "");
                }
                $tpl->set('[yes-game]', '');
                $tpl->set('[/yes-game]', '');
                $tpl->set_block("'\\[no-game](.*?)\\[/no-game]'si", "");
            } else {
                $tpl->set('[no-game]', '');
                $tpl->set('[/no-game]', '');
                $tpl->set_block("'\\[yes-game](.*?)\\[/yes-game]'si", "");
            }
            $tpl->compile('content');
            AjaxTpl();
            exit();
            break;
            //################### Запуск приложением ###################//
            
        case "start":
            $id = intval($_GET['id']);
            //Выводим данные о игре
            $row = $db->super_query("SELECT title, poster, flash, traf, width, height FROM `" . PREFIX . "_games` WHERE id = '{$id}'");
            if ($row) {
                $metatags['title'] = stripslashes($row['title']);
                //Проверка на существование игры у пользователя
                $checkMy = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_games_users` WHERE user_id = '{$user_id}' AND game_id = '{$id}'");
                //Автоматическая установка
                if (!$checkMy['cnt']) {
                    $db->query("INSERT INTO `" . PREFIX . "_games_users` SET user_id = '{$user_id}', game_id = '{$id}', setdate = '{$server_time}', lastdate = '{$server_time}'");
                    //Обновляем кол-во участников у игры
                    $db->query("UPDATE `" . PREFIX . "_games` SET traf = traf+1 WHERE id = '{$id}'");
                    //Всталвяем событие в ленту активности "установил"
                    $db->query("INSERT INTO `" . PREFIX . "_games_activity` SET user_id = '{$user_id}', game_id = '{$id}', date = '{$server_time}', action = '1'");
                } else {
                    $db->query("UPDATE `" . PREFIX . "_games_users` SET lastdate = '{$server_time}' WHERE user_id = '{$user_id}' AND game_id = '{$id}'");
                    //Всталвяем событие в ленту активности "запуск"
                    $rowActivity = $db->super_query("SELECT date FROM `" . PREFIX . "_games_activity` WHERE user_id = '{$user_id}' AND game_id = '{$id}'");
                    $lastUP = $rowActivity['date'] + 3600;
                    if ($lastUP <= $server_time) $db->query("INSERT INTO `" . PREFIX . "_games_activity` SET user_id = '{$user_id}', game_id = '{$id}', date = '{$server_time}', action = '2'");
                }
                $tpl->load_template('apps/start.tpl');
                if ($row['poster']) $tpl->set('{poster}', "/uploads/apps/{$id}/{$row['poster']}");
                else $tpl->set('{poster}', "/uploads/no_app.gif");
                $tpl->set('{title}', stripslashes($row['title']));
                $tpl->set('{swf}', "/uploads/apps/{$id}/{$row['flash']}");
                $tpl->set('{app-id}', $id);
                if ($row['traf']) $tpl->set('{traf}', newGram($row['traf'], 'участник', 'участника', 'участников'));
                else $tpl->set('{traf}', 'Нет участников');
                if (!$row['width']) $row['width'] = 795;
                if (!$row['height']) $row['height'] = 495;
                $tpl->set('{width}', $row['width']);
                $tpl->set('{height}', $row['height']);
                $tpl->compile('content');
            } else msgbox('', 'Игра не найдена.', 'info_2');
            break;
            //################### Удаление игры из своего списка ###################//
            
        case "mydel":
            NoAjaxQuery();
            $id = intval($_POST['id']);
            //Проверка на существование игры у пользователя
            $checkMy = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_games_users` WHERE user_id = '{$user_id}' AND game_id = '{$id}'");
            if ($checkMy['cnt']) {
                $db->query("DELETE FROM `" . PREFIX . "_games_users` WHERE user_id = '{$user_id}' AND game_id = '{$id}'");
                //Обновляем кол-во участников у игры
                $db->query("UPDATE `" . PREFIX . "_games` SET traf = traf-1 WHERE id = '{$id}'");
            }
            exit();
            break;
            //################### Результат поиска по играм ###################//
            
        case "search":
            NoAjaxQuery();
            $query_games = $db->safesql(strip_data($_POST['query_games']));
            $query_games = strtr($query_games, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
            if (isset($query_games) AND !empty($query_games)) {
                $lastid = intval($_POST['lastid']);
                if ($lastid) $sql_where = "AND id > '{$lastid}'";
                $sql_ = $db->super_query("SELECT id, title, poster, traf FROM `" . PREFIX . "_games` WHERE title LIKE '%{$query_games}%' {$sql_where} ORDER by `date` ASC LIMIT 0, 20", 1);
                if ($sql_) {
                    $tpl->load_template('apps/search.tpl');
                    foreach ($sql_ as $row) {
                        if ($row['poster']) $tpl->set('{poster}', "/uploads/apps/{$row['id']}/{$row['poster']}");
                        else $tpl->set('{poster}', "/uploads/no_app.gif");
                        $tpl->set('{title}', stripslashes($row['title']));
                        if ($row['traf']) $tpl->set('{traf}', newGram($row['traf'], 'участник', 'участника', 'участников'));
                        else $tpl->set('{traf}', 'Нет участников');
                        $tpl->set('{app-id}', $row['id']);
                        $tpl->compile('content');
                    }
                    if (!$lastid) {
                        $tpl->load_template('apps/search_but.tpl');
                        $cnt = count($sql_);
                        if ($cnt == 20) {
                            $tpl->set('[but]', '');
                            $tpl->set('[/but]', '');
                        } else $tpl->set_block("'\\[but](.*?)\\[/but]'si", "");
                        $tpl->compile('content');
                    }
                } else if (!$lastid) $tpl->result['content'] = '<div class="info_center" style="padding-top:125px;padding-bottom:125px">По запросу <b>' . $query_games . '</b> не найдено ни одной игры.</div>';
                AjaxTpl();
            }
            exit();
            break;
            //################### Главная страница игр ###################//
            
        default:
            $metatags['title'] = 'Игры';
            //Если вызвана подгрузка
            if ($_POST['doload']) NoAjaxQuery();
            $limit_news = 20;
            $limit_news_old = 5;
            if ($_POST['page_cnt'] > 0) $page_cnt = intval($_POST['page_cnt']) * $limit_news;
            else $page_cnt = 0;
            if ($_POST['page_cnt_old'] > 0) $page_cnt_old = intval($_POST['page_cnt_old']) * $limit_news_old;
            else $page_cnt_old = 0;
            //Выводим популярные
            if ($_POST['doload'] != 2) {
                $sql_popular = $db->super_query("SELECT id, title, poster, traf FROM `" . PREFIX . "_games` ORDER by `traf` DESC LIMIT {$page_cnt}, {$limit_news}", 1);
                if ($sql_popular) {
                    $tpl->load_template('apps/game.tpl');
                    foreach ($sql_popular as $row_popular) {
                        if ($row_popular['poster']) $tpl->set('{poster}', "/uploads/apps/{$row_popular['id']}/{$row_popular['poster']}");
                        else $tpl->set('{poster}', "/uploads/no_app.gif");
                        $tpl->set('{title}', stripslashes($row_popular['title']));
                        if ($row_popular['traf']) $tpl->set('{traf}', newGram($row_popular['traf'], 'участник', 'участника', 'участников'));
                        else $tpl->set('{traf}', 'Нет участников');
                        $tpl->set('{app-id}', $row_popular['id']);
                        $tpl->set('{type}', 'pop');
                        $tpl->compile('pop_games');
                    }
                }
            }
            //Выводим новые
            if ($_POST['doload'] != 2) {
                $sql_new = $db->super_query("SELECT id, title, poster, traf FROM `" . PREFIX . "_games` ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_news}", 1);
                if ($sql_new) {
                    $tpl->load_template('apps/game.tpl');
                    foreach ($sql_new as $row_new) {
                        if ($row_new['poster']) $tpl->set('{poster}', "/uploads/apps/{$row_new['id']}/{$row_new['poster']}");
                        else $tpl->set('{poster}', "/uploads/no_app.gif");
                        $tpl->set('{title}', stripslashes($row_new['title']));
                        if ($row_new['traf']) $tpl->set('{traf}', newGram($row_new['traf'], 'участник', 'участника', 'участников'));
                        else $tpl->set('{traf}', 'Нет участников');
                        $tpl->set('{app-id}', $row_new['id']);
                        $tpl->set('{type}', 'new');
                        $tpl->compile('new_games');
                    }
                }
            }
            //Выводим "Мои игры"
            if (!$_POST['doload'] OR $_POST['doload'] == 2) {
                $sql_my = $db->super_query("SELECT tb1.game_id, tb2.title, traf, poster FROM `" . PREFIX . "_games_users` tb1, `" . PREFIX . "_games` tb2 WHERE tb1.game_id = tb2.id AND tb1.user_id = '{$user_id}' ORDER by `lastdate` DESC LIMIT {$page_cnt_old}, {$limit_news_old}", 1);
                if ($sql_my) {
                    $tpl->load_template('apps/my.tpl');
                    foreach ($sql_my as $row_my) {
                        if ($row_my['poster']) $tpl->set('{poster}', "/uploads/apps/{$row_my['game_id']}/{$row_my['poster']}");
                        else $tpl->set('{poster}', "/uploads/no_app.gif");
                        $tpl->set('{title}', stripslashes($row_my['title']));
                        if ($row_my['traf']) $tpl->set('{traf}', newGram($row_my['traf'], 'участник', 'участника', 'участников'));
                        else $tpl->set('{traf}', 'Нет участников');
                        $tpl->set('{app-id}', $row_my['game_id']);
                        $tpl->set('{type}', 'new');
                        $tpl->compile('my_games');
                    }
                } else if (!$_POST['doload']) $tpl->result['my_games'] = '<div class="info_center" style="width:370px;padding-top:125px;padding-bottom:100px">Здесь будут отображаться последние игры,<br /> которые Вы посещали.</div>';
            }
            //Выводим "Активность друзей"
            if (!$_POST['doload'] OR $_POST['doload'] == 2) {
                $myFrList = mozg_cache("user_{$user_id}/friends");
                $expListFr = explode('|', $myFrList);
                $sqlFRlist = array();
                foreach ($expListFr as $frId) {
                    $frId = str_replace('u', '', $frId);
                    if ($frId) $sqlFRlist[] = $frId;
                }
                $sqlFRlist = implode(', ', $sqlFRlist);
                if ($sqlFRlist) {
                    $sql_acti = $db->super_query("
							SELECT
								tb1.user_id, game_id, tb1.date, action, tb2.user_search_pref, user_photo, user_sex, tb3.title, poster
							FROM
								`" . PREFIX . "_games_activity` tb1,
								`" . PREFIX . "_users` tb2,
								`" . PREFIX . "_games` tb3
							WHERE
								tb1.user_id IN ({$sqlFRlist}) 
							AND
								tb1.user_id = tb2.user_id
							AND
								tb1.game_id = tb3.id
							ORDER
								by `date` DESC
							LIMIT {$page_cnt_old}, {$limit_news_old}
						", 1);
                    if ($sql_acti) {
                        $tpl->load_template('apps/activity.tpl');
                        foreach ($sql_acti as $row_acti) {
                            if ($row_acti['poster']) $tpl->set('{poster}', "/uploads/apps/{$row_acti['game_id']}/{$row_acti['poster']}");
                            else $tpl->set('{poster}', "/uploads/no_app.gif");
                            if ($row_acti['user_photo']) $tpl->set('{ava}', "/uploads/users/{$row_acti['user_id']}/50_{$row_acti['user_photo']}");
                            else $tpl->set('{ava}', "{theme}/images/no_ava_50.png");
                            megaDate($row_acti['date']);
                            if ($row_acti['user_sex'] == 2) {
                                $sXtxt = 'запустила';
                                $sXtxt2 = 'установила';
                            } else {
                                $sXtxt = 'запустил';
                                $sXtxt2 = 'установил';
                            }
                            if ($row_acti['action'] == 1) $tpl->set('{type}', $sXtxt2);
                            else $tpl->set('{type}', $sXtxt);
                            $tpl->set('{rand}', $row_acti['date']);
                            $tpl->set('{title}', stripslashes($row_acti['title']));
                            $tpl->set('{name}', $row_acti['user_search_pref']);
                            $tpl->set('{user-id}', $row_acti['user_id']);
                            $tpl->set('{app-id}', $row_acti['game_id']);
                            $tpl->compile('activity');
                        }
                    } else if (!$_POST['doload']) $tpl->result['activity'] = '<div class="info_center" style="width:370px;padding-top:125px;padding-bottom:100px">Здесь будет отображаться активность<br /> Ваших друзей в разделе игр.</div>';
                } else if (!$_POST['doload']) $tpl->result['activity'] = '<div class="info_center" style="width:370px;padding-top:125px;padding-bottom:100px">Здесь будет отображаться активность<br /> Ваших друзей в разделе игр.</div>';
            }
            if (!$_POST['doload']) {
                $tpl->load_template('apps/main.tpl');
                $tpl->set('{pop_games}', $tpl->result['pop_games']);
                $tpl->set('{new_games}', $tpl->result['new_games']);
                $tpl->set('{my_games}', $tpl->result['my_games']);
                $tpl->set('{activity}', $tpl->result['activity']);
                $cntMy = count($sql_my);
                $cntActi = $sql_acti ? count($sql_acti) : 0;
                $cntPop = count($sql_popular);
                $cntNew = count($sql_new);
                if ($cntMy == 5 OR $cntActi == 5) {
                    $tpl->set('[but-preload]', '');
                    $tpl->set('[/but-preload]', '');
                } else $tpl->set_block("'\\[but-preload](.*?)\\[/but-preload]'si", "");
                if ($cntPop == 20 OR $cntNew == 20) {
                    $tpl->set('[but-preload-2]', '');
                    $tpl->set('[/but-preload-2]', '');
                } else $tpl->set_block("'\\[but-preload-2](.*?)\\[/but-preload-2]'si", "");
                $tpl->compile('content');
            }
            //Если вызвана подгрузка
            if ($_POST['doload']) {
                if ($_POST['doload'] == 2) $tpl->result['content'] = $tpl->result['my_games'] . '||' . $tpl->result['activity'];
                else $tpl->result['content'] = $tpl->result['pop_games'] . '||' . $tpl->result['new_games'];
                AjaxTpl();
                exit();
            }
        }
        $tpl->clear();
        $db->free();
    } else {
        $user_speedbar = $lang['no_infooo'];
        msgbox('', $lang['not_logged'], 'info');
    }
?>