<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Registry};
use FluffyDollop\Http\Request;
use Mozg\classes\Cache;

$act = $_GET['act'];

switch ($act) {

    //################### Массовые действия с юзерами ###################//
    case "users":
        $db = Registry::get('db');
        $massaction_users = $_POST['massaction_users'];
        $mass_type = (new Request)->int('mass_type');
        $ban_date = (new Request)->int('ban_date');
        if ($massaction_users) {
            if ($mass_type <= 19 && $mass_type >= 1) {
                $inputUlist = '';
                foreach ($massaction_users as $user_id) {
                    $user_id = (int)$user_id;

                    if ($user_id === 1) {
                        if ($mass_type === 1 || $mass_type === 8 || $mass_type === 16 || $mass_type === 17) {
                            msgbox('Ошибка', 'Данное действие нельзя применить к администратору!', '?mod=users');//fixme
                            exit;
                        }
                    }
                    //Удаление пользователей
                    if ($mass_type === 1) {
                        $upload_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
                        $row = $db->super_query("SELECT user_photo, user_wall_id FROM `users` WHERE user_id = '" . $user_id . "'");
                        if ($row['user_photo']) {
                            $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE id = '" . $row['user_wall_id'] . "'");
                            if ($check_wall_rec['cnt']) {
                                $update_wall = ", user_wall_num = user_wall_num-1";
                                $db->query("DELETE FROM `wall` WHERE id = '" . $row['user_wall_id'] . "'");
                                $db->query("DELETE FROM `news` WHERE obj_id = '" . $row['user_wall_id'] . "'");
                            } else {
                                $update_wall = "";
                            }
                            $db->query("UPDATE `users` SET user_delet = 1, user_photo = '',  user_active = 1, user_wall_id = '' " . $update_wall . " WHERE user_id = '" . $user_id . "'");
                            Filesystem::delete($upload_dir . $row['user_photo']);
                            Filesystem::delete($upload_dir . '50_' . $row['user_photo']);
                            Filesystem::delete($upload_dir . '100_' . $row['user_photo']);
                            Filesystem::delete($upload_dir . 'o_' . $row['user_photo']);
                            Filesystem::delete($upload_dir . 'c_' . $row['user_photo']);
                        } else {
                            $db->query("UPDATE `users` SET user_delet = 1,  user_active = 1, user_photo = '' WHERE user_id = '" . $user_id . "'");
                        }

                        $db->query("UPDATE `users` SET user_search_pref = '' WHERE user_id = '" . $user_id . "'");

                        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    } else if ($mass_type === 7) {
                        //Восстановление пользователей
                        $db->query("UPDATE `users` SET user_delet = 0 WHERE user_id = '" . $user_id . "'");
                        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    } else if ($mass_type === 8) {
                        //Блокировка пользователей
                        $this_time = $ban_date ? time() + ($ban_date * 60 * 60 * 24) : 0;
                        $db->query("UPDATE `users` SET user_ban = 1, user_active = 1, user_ban_date = '" . $this_time . "' WHERE user_id = '" . $user_id . "'");
                        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    } else if ($mass_type === 9) {
                        //Разблокировка пользователей
                        $db->query("UPDATE `users` SET user_ban = 0, user_ban_date = '' WHERE user_id = '" . $user_id . "'");
                        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                    } else if ($mass_type === 3) {
                        //Удаление отправленных сообщений юзерам
                        $sql_msg = $db->super_query("SELECT SQL_CALC_FOUND_ROWS from_user_id FROM `messages` WHERE folder = 'outbox' AND for_user_id = '" . $user_id . "' GROUP by `from_user_id`", true);
                        foreach ($sql_msg as $row_msg) {
                            $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `messages` WHERE for_user_id = '" . $row_msg['from_user_id'] . "' AND pm_read = 'no' AND from_user_id = '" . $user_id . "' AND folder = 'inbox'");

                            if ($count['cnt']) {
                                $db->query("UPDATE `users` SET user_pm_num = user_pm_num-" . $count['cnt'] . " WHERE user_id = '" . $row_msg['from_user_id'] . "'");
                                $db->query("UPDATE `im` SET msg_num = msg_num-" . $count['cnt'] . " WHERE iuser_id = '" . $row_msg['from_user_id'] . "'");
                            }

                            $countAll = $db->super_query("SELECT COUNT(*) AS cnt FROM `messages` WHERE for_user_id = '" . $row_msg['from_user_id'] . "' AND from_user_id = '" . $user_id . "' AND folder = 'inbox'");

                            $db->query("UPDATE `im` SET all_msg_num = all_msg_num-" . $countAll['cnt'] . " WHERE iuser_id = '" . $user_id . "' AND im_user_id = '" . $row_msg['from_user_id'] . "'");

                            $db->query("UPDATE `im` SET all_msg_num = all_msg_num-" . $countAll['cnt'] . " WHERE iuser_id = '" . $row_msg['from_user_id'] . "' AND im_user_id = '" . $user_id . "'");
                        }

                        $db->query("DELETE FROM `messages` WHERE history_user_id = '" . $user_id . "'");

                    } else if ($mass_type === 4) {
                        //Удаление оставленных комментариев к фото
                        $sql_pc = $db->super_query("SELECT SQL_CALC_FOUND_ROWS pid, album_id FROM `photos_comments` WHERE user_id = '" . $user_id . "' GROUP by `pid`", true);
                        foreach ($sql_pc as $row_pc) {
                            $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos_comments` WHERE user_id = '" . $user_id . "' AND pid = '" . $row_pc['pid'] . "'");

                            $db->query("UPDATE `photos` SET comm_num = comm_num-" . $count['cnt'] . " WHERE id = '" . $row_pc['pid'] . "'");

                            $db->query("UPDATE `albums` SET comm_num = comm_num-" . $count['cnt'] . " WHERE aid = '" . $row_pc['album_id'] . "'");
                        }

                        $db->query("DELETE FROM `photos_comments` WHERE user_id = '" . $user_id . "'");

                    } else if ($mass_type === 5) {
                        //Удаление оставленных комментариев к видео
                        $sql_pc = $db->super_query("SELECT SQL_CALC_FOUND_ROWS video_id FROM `videos_comments` WHERE author_user_id = '" . $user_id . "' GROUP by `video_id`", true);
                        foreach ($sql_pc as $row_pc) {
                            $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos_comments` WHERE author_user_id = '" . $user_id . "' AND video_id = '" . $row_pc['video_id'] . "'");

                            $db->query("UPDATE `videos` SET comm_num = comm_num-" . $count['cnt'] . " WHERE id = '" . $row_pc['video_id'] . "'");

                            $rowOnwer = $db->super_query("SELECT owner_user_id FROM `videos` WHERE id = '" . $row_pc['video_id'] . "'");

                            //Чистим кеш
                            Cache::mozgMassClearCacheFile("user_{$rowOnwer['owner_user_id']}/page_videos_user|user_{$rowOnwer['owner_user_id']}/page_videos_user_friends|user_{$rowOnwer['owner_user_id']}/page_videos_user_all");
                        }

                        $db->query("DELETE FROM `videos_comments` WHERE author_user_id = '" . $user_id . "'");

                    } else if ($mass_type === 11) {
                        //Удаление оставленных комментариев к заметкам
                        $sql_pc = $db->super_query("SELECT SQL_CALC_FOUND_ROWS note_id FROM `notes_comments` WHERE from_user_id = '" . $user_id . "' GROUP by `note_id`", true);
                        foreach ($sql_pc as $row_pc) {
                            $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `notes_comments` WHERE from_user_id = '" . $user_id . "' AND note_id = '" . $row_pc['note_id'] . "'");

                            $db->query("UPDATE `notes` SET comm_num = comm_num-" . $count['cnt'] . " WHERE id = '" . $row_pc['note_id'] . "'");

                            $rowOnwer = $db->super_query("SELECT owner_user_id FROM `notes` WHERE id = '" . $row_pc['note_id'] . "'");

                            //Чистим кеш
                            Cache::mozgClearCacheFile('user_' . $rowOnwer['owner_user_id'] . '/notes_user_' . $row['owner_user_id']);
                        }

                        $db->query("DELETE FROM `notes_comments` WHERE from_user_id = '" . $user_id . "'");

                    } else if ($mass_type === 6) {
                        //Удаление оставленных записей на стенах
                        $sql_pc = $db->super_query("SELECT SQL_CALC_FOUND_ROWS for_user_id FROM `wall` WHERE author_user_id = '" . $user_id . "' AND for_user_id != '" . $user_id . "' AND fast_comm_id = '0' GROUP by `for_user_id`", 1);
                        foreach ($sql_pc as $row_pc) {
                            $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE author_user_id = '" . $user_id . "' AND for_user_id = '" . $row_pc['for_user_id'] . "' AND fast_comm_id = '0'");

                            $db->query("UPDATE `users` SET user_wall_num = user_wall_num-" . $count['cnt'] . " WHERE user_id = '" . $row_pc['for_user_id'] . "'");

                            //Чистим кеш
                            Cache::mozgClearCacheFile('user_' . $row_pc['for_user_id'] . '/profile_' . $row_pc['for_user_id']);
                        }

                        $db->query("DELETE FROM `wall` WHERE author_user_id = '" . $user_id . "' AND for_user_id != '" . $user_id . "' AND fast_comm_id = '0'");

                    } //Начисление голосов
                    else if ($mass_type === 14) {
                        $db->query("UPDATE `users` SET user_balance = user_balance+" . (int)$_POST['voices'] . " WHERE user_id = '" . $user_id . "'");
                    } //Отчисление голосов
                    else if ($mass_type === 15) {
                        $db->query("UPDATE `users` SET user_balance = user_balance-" . (int)$_POST['voices'] . " WHERE user_id = '" . $user_id . "'");
                    } //Перевод в группу поддержки
                    else if ($mass_type === 16) {
                        $db->query("UPDATE `users` SET user_group = '4' WHERE user_id = '" . $user_id . "'");
                    } //Перевод в группу пользователи
                    else if ($mass_type === 17) {
                        $db->query("UPDATE `users` SET user_group = '5' WHERE user_id = '" . $user_id . "'");
                    } //Перевод в группу проверенный пользователь
                    else if ($mass_type === 18) {
                        $db->query("UPDATE `users` SET user_real = '1' WHERE user_id = '" . $user_id . "'");
                    } //Перевод в группу не проверенный пользователь
                    else if ($mass_type === 19) {
                        $db->query("UPDATE `users` SET user_real = '0' WHERE user_id = '" . $user_id . "'");
                    }
                    //Опять составляем список выделеных юеров для блокировки
                    $inputUlist .= '<input type="hidden" name="massaction_users[]" value="' . $user_id . '" />';
                }

                //Удаление пользователей
                if ($mass_type === 1) {
//                    $response = array('info' => 'Пользователи успешно удалены');
//                    _e_json($response);
//                    die();

                    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
                    $tpl->load_template('info/info_red.tpl');
                    $tpl->set('{error}', 'Пользователи успешно удалены');
                    $tpl->set('{admin_link}', $admin_link ?? '');
                    $tpl->set('{title}', 'Информация');
                    $tpl->compile('content');
                    $tpl->render();

//                    msgbox('Информация', 'Пользователи успешно удалены', '?mod=users');
                } else if ($mass_type === 7) {
                    msgbox('Информация', 'Пользователи успешно восстановлены', '?mod=users');
                } //Подготовка блокировки пользователей
                else if ($mass_type === 2) {
                    $tpl = new TplCp(ADMIN_DIR . '/tpl/');
                    $tpl->load_template('info/info_red.tpl');
                    $tpl->set('{error}', '<form method="POST" action="?mod=massaction&act=users">Количество дней блокировки: <input type="text" value="0" class="inpu" name="ban_date" /> <input type="submit" value="Забанить" class="inp" /><br />Оставьте <b>0</b>, если срок блокировки неограничен по времени.<br /><input type="hidden" value="8" name="mass_type" />' . $inputUlist . '</form>');
                    $tpl->set('{admin_link}', $admin_link ?? '');
                    $tpl->set('{title}', 'Бан пользователей');
                    $tpl->compile('content');
                    $tpl->render();
//                    msgbox('Бан пользователей', '<form method="POST" action="?mod=massaction&act=users">Количество дней блокировки: <input type="text" value="0" class="inpu" name="ban_date" /> <input type="submit" value="Забанить" class="inp" /><br />Оставьте <b>0</b>, если срок блокировки неограничен по времени.<br /><input type="hidden" value="8" name="mass_type" />' . $inputUlist . '</form>', '?mod=users');
                    //Информация об усешной блокировки пользователей
                } else if ($mass_type === 8) {
                    msgbox('Бан пользователей', 'Пользователи успешно забанены', '?mod=users');
                } //Информация об усешной РАЗблокировка пользователей
                else if ($mass_type === 9) {
                    msgbox('Разблокировка пользователей', 'Пользователи успешно разблокированы', '?mod=users');
                } //Информация об усешной удалении сообщений
                else if ($mass_type === 3) {
                    msgbox('Сообщения удалены', 'Все отправленные сообщение пользователем были удалены', '?mod=users');
                } //Информация об усешной удалении комментов к фото
                else if ($mass_type === 4) {
                    msgbox('Комментарии удалены', 'Все оставленные комментарии к фото были удалены', '?mod=users');
                } //Информация об усешной удалении комментов к фото
                else if ($mass_type === 5) {
                    msgbox('Комментарии удалены', 'Все оставленные комментарии к видео были удалены', '?mod=users');
                } //Информация об усешной удалении комментов к заметкам
                else if ($mass_type === 11)
                    msgbox('Комментарии удалены', 'Все оставленные комментарии к заметкам были удалены', '?mod=users');
                else if ($mass_type === 6)
                    msgbox('Записи удалены', 'Все оставленные записи на стенах были удалены', '?mod=users');
                //Подготовка начисления голосов
                else if ($mass_type === 12)
                    msgbox('Начисление голосов', '<form method="POST" action="?mod=massaction&act=users">Введите количество: <input type="text" value="0" class="inpu" name="voices" style="width:80px" /> <input type="submit" value="Начислить" class="inp" /><input type="hidden" value="14" name="mass_type" />' . $inputUlist . '</form>', '?mod=users');
                //Информация о начисления голосов
                else if ($mass_type === 14)
                    msgbox('Начисление голосов', 'Голоса были успешно начислены', '?mod=users');
                //Подготовка отчисление голосов
                else if ($mass_type === 13)
                    msgbox('Отчисление голосов', '<form method="POST" action="?mod=massaction&act=users">Введите количество: <input type="text" value="0" class="inpu" name="voices" style="width:80px" /> <input type="submit" value="Забрать" class="inp" /><input type="hidden" value="15" name="mass_type" />' . $inputUlist . '</form>', '?mod=users');
                //Информация о отчисление голосов
                else if ($mass_type === 15)
                    msgbox('Отчисление голосов', 'Голоса были успешно отчислены', '?mod=users');
                //Информация о переведении в группу
                else if ($mass_type === 16)
                    msgbox('Перевод в группу', 'Пользователь был переведен в группу техподдержки', '?mod=users');
                //Информация о переведении в группу
                else if ($mass_type === 17)
                    msgbox('Перевод в группу', 'Пользователь был переведен в группу пользователи', '?mod=users');
                //Информация о переведении в проверенные пользователи
                else if ($mass_type === 18)
                    msgbox('Подтверждение аккаунта', 'Аккаунт пользователя успешно подтвержден', '?mod=users');
                //Информация о переведении в группу
                else if ($mass_type === 19)
                    msgbox('Подтверждение аккаунта', 'Подтверждение пользователя успешно снято', '?mod=users');
                //Otmetka off
                else if ($mass_type === 20)
                    msgbox('Модерация', 'Анкета активирована', '?mod=users');
                Cache::mozgClearCache();
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=users');
        } else
            msgbox('Ошибка', 'Выберите пользователей', '?mod=users');
        break;

    //################### Масовые действия с заметками ###################//
    case "notes":
        $db = Registry::get('db');
        $massaction_note = $_POST['massaction_note'];
        $mass_type = (new Request)->int('mass_type');
        if ($massaction_note) {
            if ($mass_type <= 2 && $mass_type >= 1) {
                //Если удаляем
                if ($mass_type === 1) {
                    foreach ($massaction_note as $note_id) {
                        $note_id = (int)$note_id;

                        //Проверка на существование заметки и выводим ИД владельца заметки
                        $row = $db->super_query("SELECT owner_user_id FROM `notes` WHERE id = '" . $note_id . "'");
                        if ($row) {
                            $db->query("DELETE FROM `notes` WHERE id = '" . $note_id . "'");
                            $db->query("DELETE FROM `notes_comments` WHERE note_id = '" . $note_id . "'");
                            $db->query("UPDATE `users` SET user_notes_num = user_notes_num-1 WHERE user_id = '" . $row['owner_user_id'] . "'");

                            //Чистим кеш владельцу заметки и заметок на его стр
                            Cache::mozgClearCacheFile('user_' . $row['owner_user_id'] . '/profile_' . $row['owner_user_id']);
                            Cache::mozgClearCacheFile('user_' . $row['owner_user_id'] . '/notes_user_' . $row['owner_user_id']);
                        }
                    }
                    msgbox('Информация', 'Выбранные заметки успешно удалены', '?mod=notes');
                }

                //Если чистим комментарии
                if ($mass_type === 2) {
                    foreach ($massaction_note as $note_id) {
                        $note_id = (int)$note_id;

                        //Проверка на существование заметки и выводим ИД владельца заметки
                        $row = $db->super_query("SELECT owner_user_id FROM `notes` WHERE id = '" . $note_id . "'");
                        if ($row) {
                            $db->query("UPDATE `notes` SET comm_num = '0' WHERE id = '" . $note_id . "'");
                            $db->query("DELETE FROM `notes_comments` WHERE note_id = '" . $note_id . "'");

                            //Чистим кеш владельцу заметки и заметок на его стр
                            Cache::mozgClearCacheFile('user_' . $row['owner_user_id'] . '/profile_' . $row['owner_user_id']);
                            Cache::mozgClearCacheFile('user_' . $row['owner_user_id'] . '/notes_user_' . $row['owner_user_id']);
                        }
                    }
                    msgbox('Информация', 'Комментарии к выбраным заметкам удалены', '?mod=notes');
                }
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=notes');
        } else
            msgbox('Ошибка', 'Выберите заметки', '?mod=notes');
        break;

    //################### Масовые действия с сообещствами ###################//
    case "groups":
        $db = Registry::get('db');
        $massaction_list = $_POST['massaction_list'];
        $mass_type = (new Request)->int('mass_type');
        if ($massaction_list) {
            if ($mass_type <= 10 && $mass_type >= 1) {
                //Если удаляем
                if ($mass_type === 1) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $row = $db->super_query("SELECT real_admin, photo FROM `communities` WHERE id = '" . $id . "'");
                        if ($row) {
                            $db->query("UPDATE `communities` SET del = '1', photo = '' WHERE id = '" . $id . "'");
                            if ($row['photo']) {
                                Filesystem::delete(ROOT_DIR . '/uploads/groups/' . $row['real_admin'] . '/' . $row['photo']);
                            }
                        }
                    }
                    msgbox('Информация', 'Выбранные сообщества успешно удалены', '?mod=groups');
                }

                //Если баним
                if ($mass_type === 2) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $row = $db->super_query("SELECT real_admin, photo FROM `communities` WHERE id = '" . $id . "'");
                        if ($row) {
                            $db->query("UPDATE `communities` SET ban = '1', photo = '' WHERE id = '" . $id . "'");
                            if ($row['photo']) {
                                Filesystem::delete(ROOT_DIR . '/uploads/groups/' . $row['real_admin'] . '/' . $row['photo']);
                            }
                        }
                    }
                    msgbox('Информация', 'Выбранные сообщества успешно заблокированы', '?mod=groups');
                }

                //Если воостанавливаем
                if ($mass_type === 3) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET del = '0' WHERE id = '" . $id . "'");
                    }
                    msgbox('Информация', 'Выбранные сообщества успешно воостановлены', '?mod=groups');
                }
                //Установка отметки
                if ($mass_type === 5) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET real_communities = '1' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Отметка установлена', '?mod=groups');
                }
                //Удаление отметки
                if ($mass_type === 6) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET real_communities = '0' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Отметка удалена', '?mod=groups');
                }
                //Занесение в реестр
                if ($mass_type === 7) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET badpublic = '1' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Страница занесена в реестр', '?mod=groups');
                }
                //Удаление с реестра
                if ($mass_type === 8) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET badpublic = '0' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Страница удалена из реестра', '?mod=groups');
                }
                //Добавление в рекомендуемые
                if ($mass_type === 9) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET recommendation = '1' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Страница добавлена в рекомендуемые', '?mod=groups');
                }
                //Удаление с рекомендуемых
                if ($mass_type === 10) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET recommendation = '0' WHERE id = '" . $id . "'");//fixme bug
                    }
                    msgbox('Информация', 'Страница удалена из рекомендуемых', '?mod=groups');
                }
                //Если разблокируем
                if ($mass_type === 4) {
                    foreach ($massaction_list as $id) {
                        $id = (int)$id;
                        $db->query("UPDATE `communities` SET ban = '0' WHERE id = '" . $id . "'");
                    }
                    msgbox('Информация', 'Выбранные сообщества успешно разблокированы', '?mod=groups');
                }
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=groups');
        } else
            msgbox('Ошибка', 'Выберите заметки', '?mod=groups');
        break;

    //################### Масовые действия с видеозаписям ###################//
    case "videos":
        $db = Registry::get('db');
        $massaction_list = $_POST['massaction_list'];
        $mass_type = (new Request)->int('mass_type');
        if ($massaction_list) {
            if ($mass_type <= 3 and $mass_type >= 1) {
                //Если удаляем
                if ($mass_type === 1) {
                    foreach ($massaction_list as $id) {
                        $vid = (int)$id;
                        $row = $db->super_query("SELECT owner_user_id, photo FROM `videos` WHERE id = '" . $vid . "'");
                        if ($row) {
                            $db->query("DELETE FROM `videos` WHERE id = '" . $vid . "'");
                            $db->query("DELETE FROM `videos_comments` WHERE video_id = '" . $vid . "'");
                            $db->query("UPDATE `users` SET user_videos_num = user_videos_num-1 WHERE user_id = '" . $row['owner_user_id'] . "'");

                            //Удаляем фотку
                            $exp_photo = explode('/', $row['photo']);
                            $photo_name = end($exp_photo);
                            Filesystem::delete(ROOT_DIR . '/uploads/videos/' . $row['owner_user_id'] . '/' . $photo_name);

                            //Чистим кеш
                            Cache::mozgMassClearCacheFile("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all|user_{$row['owner_user_id']}/profile_{$row['owner_user_id']}|user_{$row['owner_user_id']}/videos_num_all|user_{$row['owner_user_id']}/videos_num_friends");
                        }
                    }
                    msgbox('Информация', 'Выбранные видеозаписи успешно удалены', '?mod=videos');
                }

                //Если чистим комменты
                if ($mass_type === 2) {
                    foreach ($massaction_list as $id) {
                        $vid = (int)$id;
                        $row = $db->super_query("SELECT owner_user_id FROM `videos` WHERE id = '" . $vid . "'");
                        if ($row) {
                            $db->query("DELETE FROM `videos_comments` WHERE video_id = '" . $vid . "'");
                            //fixme bug $photo undefined
                            $db->query("DELETE FROM `news` WHERE action_text LIKE '%" . $photo . "|" . $vid . "%' AND action_type = '9' AND for_user_id = '" . $row['owner_user_id'] . "'");
                            $db->query("UPDATE `videos` SET comm_num = '0' WHERE id = '" . $vid . "'");

                            //Чистим кеш
                            Cache::mozgMassClearCacheFile("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all");
                        }
                    }
                    msgbox('Информация', 'Комментарии к выбраным видео удалены', '?mod=videos');
                }

                //Если чистим просмотры
                if ($mass_type === 3) {
                    foreach ($massaction_list as $id) {
                        $vid = (int)$id;
                        $db->query("UPDATE `videos` SET views = '0' WHERE id = '" . $vid . "'");
                    }
                    msgbox('Информация', 'Просмотры к выбраным видео очищены', '?mod=videos');
                }
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=videos');
        } else
            msgbox('Ошибка', 'Выберите видеозаписи', '?mod=videos');
        break;

    //################### Масовые действия с аудиозаписями ###################//
    case "musics":
        $db = Registry::get('db');
        $massaction_list = $_POST['massaction_list'];
        $mass_type = (new Request)->int('mass_type');
        if ($massaction_list) {
            if ($mass_type === 1) {
                foreach ($massaction_list as $id) {
                    $aid = (int)$id;
                    $check = $db->super_query("SELECT auser_id FROM `audio` WHERE aid = '" . $aid . "'");
                    if ($check) {
                        $db->query("DELETE FROM `audio` WHERE aid = '" . $aid . "'");
                        $db->query("UPDATE `users` SET user_audio = user_audio-1 WHERE user_id = '" . $check['auser_id'] . "'");
                        Cache::mozgMassClearCacheFile('user_' . $check['auser_id'] . '/audios_profile|user_' . $check['auser_id'] . '/profile_' . $check['auser_id']);
                    }
                }
                msgbox('Информация', 'Выбранные аудиозаписи успешно удалены', '?mod=musics');
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=musics');
        } else
            msgbox('Ошибка', 'Выберите аудиозаписи', '?mod=musics');
        break;

    //################### Масовые действия с альбомами ###################//
    case "albums":
        $db = Registry::get('db');
        $massaction_list = $_POST['massaction_list'];
        $mass_type = (new Request)->int('mass_type');
        if ($massaction_list) {
            //Удаление
            if ($mass_type === 1) {
                foreach ($massaction_list as $id) {
                    $aid = (int)$id;
                    $row = $db->super_query("SELECT user_id, photo_num FROM `albums` WHERE aid = '" . $aid . "'");
                    if ($row) {
                        //Удаляем альбом
                        $db->query("DELETE FROM `albums` WHERE aid = '" . $aid . "'");

                        //Проверяем еслить ли фотки в альбоме
                        if ($row['photo_num']) {
                            //Удаляем фотки
                            $db->query("DELETE FROM `photos` WHERE album_id = '" . $aid . "'");

                            //Удаляем комментарии к альбому
                            $db->query("DELETE FROM `photos_comments` WHERE album_id = '" . $aid . "'");

                            //Удаляем фотки из папки на сервере
                            $fdir = opendir(ROOT_DIR . '/uploads/users/' . $row['user_id'] . '/albums/' . $aid);
                            while ($file = readdir($fdir)) {
                                Filesystem::delete(ROOT_DIR . '/uploads/users/' . $row['user_id'] . '/albums/' . $aid . '/' . $file);
                            }

                            Filesystem::delete(ROOT_DIR . '/uploads/users/' . $row['user_id'] . '/albums/' . $aid);
                        }

                        //Обновлям кол-во альбом в юзера
                        $db->query("UPDATE `users` SET user_albums_num = user_albums_num-1 WHERE user_id = '" . $row['user_id'] . "'");

                        //Удаляем кеш позиций фотографий и кеш профиля
                        Cache::mozgClearCacheFile('user_' . $row['user_id'] . '/position_photos_album_' . $aid);
                        Cache::mozgClearCacheFile("user_{$row['user_id']}/profile_{$row['user_id']}");
                        Cache::mozgMassClearCacheFile("user_{$row['user_id']}/albums|user_{$row['user_id']}/albums_all|user_{$row['user_id']}/albums_friends|user_{$row['user_id']}/albums_cnt_friends|user_{$row['user_id']}/albums_cnt_all");
                    }
                }
                msgbox('Информация', 'Выбранные альбомы успешно удалены', '?mod=albums');
            } else
                msgbox('Ошибка', 'Выберите действие', '?mod=albums');
        } else
            msgbox('Ошибка', 'Выберите альбомы', '?mod=albums');
        break;

    default:

        header("Location: ?mod");
}