<?php
/* 
	Appointment: Заметки
	File: notes.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

if($ajax == 'yes')
	NoAjaxQuery();

if($logged){
	$act = $_GET['act'];
	$user_id = intval($user_info['user_id']);
	$yesterday_date = time();
	
	$page = (intval($_GET['page']) > 0) ? intval($_GET['page']) : 1;
	$gcount = 20;
	$limit_page = ($page-1)*$gcount;
	
	switch($act){
		
		//################### Страница добавления заметки ###################//
		case "add":
			$metatags['title'] = $lang['add_new_note'];
			$user_speedbar = $lang['add_new_note'];
			
			//Загруажем head заметок
			$tpl->load_template('notes/head.tpl');
			$tpl->set('[add]', '');
			$tpl->set('[/add]', '');
			$tpl->set_block("'\\[all\\](.*?)\\[/all\\]'si","");
			$tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si","");
			$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");
			$tpl->compile('info');
			
			//Загружаем сам шаблон добавления
			$tpl->load_template('notes/add.tpl');
			$tpl->compile('content');
		break;
		
		//################### Добавление заметки в БД ###################//
		case "save":
			NoAjaxQuery();
			
			//Подключаем парсер
			include ENGINE_DIR.'/classes/parse.php';
			$parse = new parse();

			$title = ajax_utf8(textFilter($_POST['title'], false, true));
			$text = $parse->BBparse(ajax_utf8(textFilter($_POST['text'])));

			if(strlen($title) > 0 && strlen($text) > 0){
				$db->query("INSERT INTO `".PREFIX."_notes` SET owner_user_id = '{$user_id}', title = '{$title}', full_text = '{$text}', date = NOW()");
				$db_id = $db->insert_id();
				$db->query("UPDATE `".PREFIX."_users` SET user_notes_num = user_notes_num+1 WHERE user_id = '{$user_id}'");
				
				echo $db_id;
				
				//Добавляем действия в ленту новостей
				$generateLastTime = $server_time-10800;
				$row = $db->super_query("SELECT ac_id, action_text FROM `".PREFIX."_news` WHERE action_time > '{$generateLastTime}' AND action_type = 5 AND ac_user_id = '{$user_id}'");
				if($row)
					$db->query("UPDATE `".PREFIX."_news` SET action_text = '{$db_id}||{$row['action_text']}', action_time = '{$server_time}' WHERE ac_id = '{$row['ac_id']}'");
				else
					$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 5, action_text = '{$db_id}', action_time = '{$server_time}'");
				
				//Чистим кеш владельцу заметки и заметок на его стр
				mozg_clear_cache_file('user_'.$user_id.'/profile_'.$user_id);
				mozg_clear_cache_file('user_'.$user_id.'/notes_user_'.$user_id);
			}
			
			die();
		break;
		
		//################### Предварительный просмотр заметки ###################//
		case "preview":
			NoAjaxQuery();
			
			//Подключаем парсер
			include ENGINE_DIR.'/classes/parse.php';
			$parse = new parse();

			$title = ajax_utf8(textFilter($_POST['title']));
			$text = $parse->BBparse(ajax_utf8(textFilter($_POST['text'])), 1);
			
			if($text && $title){
				//Загружаем шаблон вывода полного просомтра заметки
				$tpl->load_template('notes/preview.tpl');
				$tpl->set('{title}', stripslashes(stripslashes($title)));
				$tpl->set('{full-text}', stripslashes(stripslashes($text)));
				$tpl->set('{name}', 'Вы');
				$tpl->set('{user-id}', $user_id);
				$tpl->set('{date}', langdate('j F Y в H:i', time()));
				$tpl->set('{comm-num}', $lang['note_no_comments']);
				$tpl->compile('content');
				AjaxTpl();
			}
			
			die();
		break;
		
		//################### Страница редактирования заметки ###################//
		case "edit":
			$note_id = intval($_GET['note_id']);
			$metatags['title'] = $lang['note_edit'];
			$user_speedbar = $lang['note_edit'];
			
			//SQL Запрос на вывод информации о заметке
			$row = $db->super_query("SELECT title, full_text FROM `".PREFIX."_notes` WHERE owner_user_id = '{$user_id}' AND id = '{$note_id}'");
			
			if($row){
				//Подключаем парсер
				include ENGINE_DIR.'/classes/parse.php';
				$parse = new parse();
			
				//Загруажем head заметок
				$tpl->load_template('notes/head.tpl');
				$tpl->set('{note-id}', $note_id);
				$tpl->set('[edit]', '');
				$tpl->set('[/edit]', '');
				$tpl->set_block("'\\[all\\](.*?)\\[/all\\]'si","");
				$tpl->set_block("'\\[add\\](.*?)\\[/add\\]'si","");
				$tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si","");
				$tpl->compile('info');

				//Загружаем шаблон редактирования
				$tpl->load_template('notes/edit.tpl');
				$tpl->set('{note-id}', $note_id);
				$tpl->set('{title}', stripslashes($row['title']));
				$tpl->set('{text}', $parse->BBdecode(stripslashes(myBrRn($row['full_text']))));
				$tpl->compile('content');
			} else {
				$user_speedbar = $lang['error'];
				msgbox('', $lang['no_notes'], 'info');
			}
		break;
		
		//################### Сохранение изменений ###################//
		case "editsave":
			NoAjaxQuery();
			
			//Подключаем парсер
			include ENGINE_DIR.'/classes/parse.php';
			$parse = new parse();
				
			$note_id = intval($_POST['note_id']);
			
			$title = textFilter(ajax_utf8($_POST['title']), false, true);
			$text = $parse->BBparse(textFilter(ajax_utf8($_POST['text'])));

			if(strlen($title) > 0 && strlen($text) > 0){
				//Проверка на существование заметки
				$row = $db->super_query("SELECT owner_user_id FROM `".PREFIX."_notes` WHERE id = '{$note_id}'");
				if($row['owner_user_id'] == $user_id)
					$db->query("UPDATE `".PREFIX."_notes` SET title = '{$title}', full_text = '{$text}' WHERE id = '{$note_id}'");
			}
			
			die();
		break;
		
		//################### Удаление заметки ###################//
		case "delet":
			NoAjaxQuery();
			$note_id = intval($_POST['note_id']);
			//Проверка на существование заметки
			$row = $db->super_query("SELECT owner_user_id FROM `".PREFIX."_notes` WHERE id = '{$note_id}'");
			if($row['owner_user_id'] == $user_id){
				$db->query("DELETE FROM `".PREFIX."_notes` WHERE id = '{$note_id}'");
				$db->query("DELETE FROM `".PREFIX."_notes_comments` WHERE note_id = '{$note_id}'");
				$db->query("UPDATE `".PREFIX."_users` SET user_notes_num = user_notes_num-1 WHERE user_id = '{$user_id}'");
				
				//Чистим кеш владельцу заметки и заметок на его стр
				mozg_clear_cache_file('user_'.$user_id.'/profile_'.$user_id);
				mozg_clear_cache_file('user_'.$user_id.'/notes_user_'.$user_id);
			}
			die();
		break;
		
		//################### Добавления комментария ###################//
		case "addcomment":
			NoAjaxQuery();
			$note_id = intval($_POST['note_id']);
			$textcom = ajax_utf8(textFilter($_POST['textcom']));
			
			//Проверка на существование заметки
			$check = $db->super_query("SELECT owner_user_id FROM `".PREFIX."_notes` WHERE id = '{$note_id}'");
				
			$CheckBlackList = CheckBlackList($check['owner_user_id']);
			
			if(!$CheckBlackList AND $check AND isset($textcom) AND !empty($textcom)){
				if($check){
					$db->query("INSERT INTO `".PREFIX."_notes_comments` SET note_id = '{$note_id}', from_user_id = '{$user_id}', text = '{$textcom}', add_date = NOW()");
					$db_id = $db->insert_id();
					$db->query("UPDATE `".PREFIX."_notes` SET comm_num = comm_num+1 WHERE id = '{$note_id}'");

					$tpl->load_template('notes/comment.tpl');
					$tpl->set('{author}', $user_info['user_search_pref']);
					if($user_info['user_photo'])
						$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$user_id.'/50_'.$user_info['user_photo']);
					else
						$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
					$tpl->set('{id}', $db_id);
					$tpl->set('{date}', langdate('сегодня в H:i', time()));
					$tpl->set('[owner]', '');
					$tpl->set('[/owner]', '');
					$tpl->set('{online}', $lang['online']);
					$tpl->set('{comment}', stripslashes($textcom));
					$tpl->compile('content');
					
					//Добавляем действие в ленту новостей "ответы" владельцу заметки
					if($user_id != $check['owner_user_id']){
						$comment = str_replace("|", "&#124;", $textcom);
						$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 10, action_text = '{$comment}|{$note_id}', obj_id = '{$db_id}', for_user_id = '{$check['owner_user_id']}', action_time = '{$server_time}'");

						//Вставляем событие в моментальные оповещания
						$row_userOW = $db->super_query("SELECT user_last_visit FROM `".PREFIX."_users` WHERE user_id = '{$check['owner_user_id']}'");
						$update_time = $server_time - 70;
											
						if($row_userOW['user_last_visit'] >= $update_time){
											
							$db->query("INSERT INTO `".PREFIX."_updates` SET for_user_id = '{$check['owner_user_id']}', from_user_id = '{$user_id}', type = '4', date = '{$server_time}', text = '{$comment}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/notes/view/{$note_id}'");
											
							mozg_create_cache("user_{$check['owner_user_id']}/updates", 1);
							
						//ИНАЧЕ Добавляем +1 юзеру для оповещания							
						} else {

							$cntCacheNews = mozg_cache('user_'.$check['owner_user_id'].'/new_news');
							mozg_create_cache('user_'.$check['owner_user_id'].'/new_news', ($cntCacheNews+1));
						
						}
							
						//Отправка уведомления на E-mail
						if($config['news_mail_5'] == 'yes'){
							$rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `".PREFIX."_users` WHERE user_id = '".$check['owner_user_id']."'");
							if($rowUserEmail['user_email']){
								include_once ENGINE_DIR.'/classes/mail.php';
								$mail = new dle_mail($config);
								$rowMyInfo = $db->super_query("SELECT user_search_pref FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
								$rowEmailTpl = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '5'");
								$rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
								$rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
								$rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'].'notes/view/'.$note_id, $rowEmailTpl['text']);
								$mail->send($rowUserEmail['user_email'], 'Новый комментарий к Вашей заметке', $rowEmailTpl['text']);
							}
						}
					}

					//Чистим кеш владельцу заметки и заметок на его стр
					mozg_clear_cache_file('user_'.$check['owner_user_id'].'/notes_user_'.$check['owner_user_id']);
				
					AjaxTpl();
				}
			}
			die();
		break;
		
		//################### Удаление комментария ###################//
		case "delcomment":
			NoAjaxQuery();
			$comm_id = intval($_POST['comm_id']);
			//Проверка на существование коммента и выводим ИД создателя заметки
			$row = $db->super_query("SELECT tb1.note_id, from_user_id, tb2.owner_user_id FROM `".PREFIX."_notes_comments` tb1, `".PREFIX."_notes` tb2  WHERE tb1.id = '{$comm_id}' AND tb1.note_id = tb2.id");
			if($row['from_user_id'] == $user_id || $row['owner_user_id'] == $user_id){
				$db->query("DELETE FROM `".PREFIX."_notes_comments` WHERE id = '{$comm_id}'");
				$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$comm_id}' AND action_type = 10");
				$db->query("UPDATE `".PREFIX."_notes` SET comm_num = comm_num-1 WHERE id = '{$row['note_id']}'");
				
				//Чистим кеш владельцу заметки и заметок на его стр
				mozg_clear_cache_file('user_'.$row['owner_user_id'].'/notes_user_'.$row['owner_user_id']);
			}
			die();
		break;
		
		//################### Показ всех комментариев ###################//
		case "allcomment":
			NoAjaxQuery();
			$note_id = intval($_POST['note_id']);
			$comm_num = intval($_POST['comm_num']);
			if($comm_num > 10 && $note_id){
				$limit = $comm_num-10;
				
				$sql_ = $db->super_query("SELECT tb1.id, from_user_id, text, date, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile tb3.owner_user_id FROM `".PREFIX."_notes_comments` tb1, `".PREFIX."_users` tb2, `".PREFIX."_notes` tb3 WHERE tb1.note_id = '{$note_id}' AND tb1.from_user_id = tb2.user_id AND tb1.note_id = tb3.id ORDER by `add_date` ASC LIMIT 0, {$limit}", 1);
					
				$tpl->load_template('notes/comment.tpl');
				foreach($sql_ as $row_comm){
					if($row_comm['user_photo'])
						$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comm['from_user_id'].'/50_'.$row_comm['user_photo']);
					else
						$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						
					OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
					
					megaDate(strtotime($row_comm['date']));
						
					if($row_comm['from_user_id'] == $user_id || $row_comm['owner_user_id'] == $user_id){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
					} else
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
						
					$tpl->set('{author}', $row_comm['user_search_pref']);
					$tpl->set('{uid}', $row_comm['from_user_id']);
					$tpl->set('{id}', $row_comm['id']);
					$tpl->set('{comment}', stripslashes($row_comm['text']));
					$tpl->compile('content');
				}
				AjaxTpl();
			}
			die();
		break;
		
		//################### Просмотр полной заметки ###################//
		case "view":
			$note_id = intval($_GET['note_id']);
			
			//SQL Запрос
			$row = $db->super_query("SELECT tb1.title, owner_user_id, full_text, comm_num, date, tb2.user_search_pref FROM `".PREFIX."_notes` tb1, `".PREFIX."_users` tb2 WHERE id = '{$note_id}' AND tb1.owner_user_id = tb2.user_id");

			//ЧС
			$CheckBlackList = CheckBlackList($row['owner_user_id']);
			if(!$CheckBlackList){
				if($row){
					//Формирование мета титле и спидбара
					$author_info = explode(' ', $row['user_search_pref']);
					$user_speedbar = $lang['notes_view'];
					$metatags['title'] = $lang['notes_view'];
					
					//Загруажем head заметок
					$tpl->load_template('notes/head.tpl');
					$tpl->set('[view]', '');
					$tpl->set('[/view]', '');
					$tpl->set('{user-id}', $row['owner_user_id']);
					$tpl->set('{note-id}', $note_id);
					if($row['owner_user_id'] == $user_id){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
						$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
					} else {
						$tpl->set('[not-owner]', '');
						$tpl->set('[/not-owner]', '');
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					}
					$tpl->set('{name}', gramatikName($author_info[0]));
					$tpl->set_block("'\\[add\\](.*?)\\[/add\\]'si","");
					$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");
					$tpl->set_block("'\\[all\\](.*?)\\[/all\\]'si","");
					$tpl->compile('info');
					
					//Загружаем шаблон вывода полного просомтра заметки
					$tpl->load_template('notes/full.tpl');
					$tpl->set('{note-id}', $note_id);
					$tpl->set('{title}', stripslashes($row['title']));
					$tpl->set('{full-text}', stripslashes($row['full_text']));
					$tpl->set('{name}', $row['user_search_pref']);
					$tpl->set('{user-id}', $row['owner_user_id']);
					
					$date_comm = strtotime($row['date']);
					if(date('Y-m-d', $date_comm) == date('Y-m-d', $yesterday_date))
						$tpl->set('{date}', langdate('сегодня в H:i', $date_comm));
					elseif(date('Y-m-d', $date_comm) == date('Y-m-d', ($yesterday_date-84600)))
						$tpl->set('{date}', langdate('вчера в H:i', $date_comm));
					else
						$tpl->set('{date}', langdate('j F Y в H:i', $date_comm));
									
					if($row['owner_user_id'] == $user_id){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
					} else
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
									
					if($row['comm_num'])
						$tpl->set('{comm-num}', $row['comm_num'].' '.gram_record($row['comm_num'], 'comments'));
					else
						$tpl->set('{comm-num}', $lang['note_no_comments']);
					
					if($row['comm_num'] > 10){
						$tpl->set('[all-comm]', '');
						$tpl->set('[/all-comm]', '');
					} else
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
						
					$tpl->set('{prev-text-comm}', gram_record(($row['comm_num']-10), 'prev').' '.($row['comm_num']-10).' '.gram_record(($row['comm_num']-10), 'comments'));
					$tpl->set('{num}', $row['comm_num']);

					$tpl->compile('content');
					
					$tpl->result['content'] = str_replace('{note-id}', $note_id, $tpl->result['content']);
					
					//Выводи комменты, если они есть
					if($row['comm_num']){
						
						if($row['comm_num'] >= 10)
							$start_limit = $row['comm_num']-10;
						else
							$start_limit = 0;
						
						$sql_ = $db->super_query("SELECT tb1.id, from_user_id, text, add_date, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_notes_comments` tb1, `".PREFIX."_users` tb2 WHERE tb1.note_id = '{$note_id}' AND tb1.from_user_id = tb2.user_id ORDER by `add_date` ASC LIMIT {$start_limit}, {$row['comm_num']}", 1);
						
						$tpl->load_template('notes/comment.tpl');
						foreach($sql_ as $row_comm){
							if($row_comm['user_photo'])
								$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comm['from_user_id'].'/50_'.$row_comm['user_photo']);
							else
								$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
								
							OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);

							megaDate(strtotime($row_comm['add_date']));
							
							if($row_comm['from_user_id'] == $user_id || $row['owner_user_id'] == $user_id){
								$tpl->set('[owner]', '');
								$tpl->set('[/owner]', '');
							} else
								$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
							
							$tpl->set('{author}', $row_comm['user_search_pref']);
							$tpl->set('{uid}', $row_comm['from_user_id']);
							$tpl->set('{id}', $row_comm['id']);
							$tpl->set('{comment}', stripslashes($row_comm['text']));
							$tpl->compile('content');
						}
					}
					
					//Загружаем форму добавления комментов
					$tpl->load_template('notes/addcomment.tpl');
					$tpl->set('{note-id}', $note_id);
					$tpl->compile('content');
					
				} else {
					$user_speedbar = $lang['error'];
					$metatags['title'] = $lang['error'];
					msgbox('', $lang['no_notes'], 'info');
				}
			} else {
				$user_speedbar = $lang['error'];
				msgbox('', $lang['no_notes'], 'info');
			}
		break;
		
		default:
		
			$get_user_id = intval($_GET['get_user_id']);
			if(!$get_user_id)
				$get_user_id = $user_id;
			
			//ЧС
			$CheckBlackList = CheckBlackList($get_user_id);
			if(!$CheckBlackList){
				//Выводи информация о юзере у которого заметки
				$owner = $db->super_query("SELECT user_search_pref, user_photo, user_notes_num FROM `".PREFIX."_users` WHERE user_id = '{$get_user_id}'");
				if($owner){
					
					//SQL Запрос на вывод заметок из БД
					$sql_ = $db->super_query("SELECT id, title, full_text, date, comm_num FROM `".PREFIX."_notes` WHERE owner_user_id = '{$get_user_id}' ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", 1);
					
					if(!$owner['user_notes_num'])
						$owner['user_notes_num'] = '';
					
					//Формирование мета титле и спидбара
					$author_info = explode(' ', $owner['user_search_pref']);
					$metatags['title'] = $lang['title_notes'].' '.gramatikName($author_info[0]).' '.gramatikName($author_info[1]);
					$user_speedbar = 'У '.gramatikName($author_info[0]).' <span id="notes_num">'.$owner['user_notes_num'].'</span> '.gram_record($owner['user_notes_num'], 'notes');;
					
					//Загруажем head заметок
					$tpl->load_template('notes/head.tpl');
					$tpl->set('[all]', '');
					$tpl->set('[/all]', '');
					$tpl->set('{user-id}', $get_user_id);
					if($get_user_id == $user_id){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
						$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
						$user_speedbar = 'У Вас <span id="notes_num">'.$owner['user_notes_num'].'</span> '.gram_record($owner['user_notes_num'], 'notes');
					} else {
						$tpl->set('[not-owner]', '');
						$tpl->set('[/not-owner]', '');
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					}
					$tpl->set('{name}', gramatikName($author_info[0]));
					$tpl->set_block("'\\[add\\](.*?)\\[/add\\]'si","");
					$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");
					$tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si","");
					$tpl->compile('info');
					
					//Выводим
					if($sql_){
						$tpl->load_template('notes/short.tpl');
						foreach($sql_ as $row){
							if($owner['user_photo'])
								$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$get_user_id.'/50_'.$owner['user_photo']);
							else
								$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
							
							$tpl->set('{user-id}', $get_user_id);
							$tpl->set('{short-text}', stripslashes($row['full_text']));
							$tpl->set('{title}', stripslashes($row['title']));
							$tpl->set('{name}', $owner['user_search_pref']);
							$tpl->set('{note-id}', $row['id']);
							
							$date_comm = strtotime($row['date']);
							if(date('Y-m-d', $date_comm) == date('Y-m-d', $yesterday_date))
								$tpl->set('{date}', langdate('сегодня в H:i', $date_comm));
							elseif(date('Y-m-d', $date_comm) == date('Y-m-d', ($yesterday_date-84600)))
								$tpl->set('{date}', langdate('вчера в H:i', $date_comm));
							else
								$tpl->set('{date}', langdate('j F Y в H:i', $date_comm));
								
							if($get_user_id == $user_id){
								$tpl->set('[owner]', '');
								$tpl->set('[/owner]', '');
							} else
								$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
								
							if($row['comm_num'])
								$tpl->set('{comm-num}', $row['comm_num'].' '.gram_record($row['comm_num'], 'comments'));
							else
								$tpl->set('{comm-num}', $lang['note_no_comments']);
							
							$tpl->compile('content');
						}
						navigation($gcount, $owner['user_notes_num'], $config['home_url'].'notes/'.$get_user_id.'/page/');
					} else
						if($get_user_id == $user_id)
							msgbox('', $lang['note_no_user'], 'info_2');
						else
							msgbox('', $lang['note_no'], 'info_2');
				
				} else {
					$user_speedbar = $lang['error'];
					$metatags['title'] = $lang['error'];
					msgbox('', $lang['no_notes'], 'info');
				}
			} else {
				$user_speedbar = $lang['error'];
				msgbox('', $lang['no_notes'], 'info');
			}
	}
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>