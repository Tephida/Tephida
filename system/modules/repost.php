<?php
/* 
	Appointment: Отправка записи в сообщество или другу
	File: repost.php 
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

NoAjaxQuery();

if($logged){

	$act = $_GET['act'];
	$user_id = $user_info['user_id'];
	
	switch($act){
	
		//################## Если выбрано "Друзья и подписчики" ##################//
		case "for_wall":
			NoAjaxQuery();
			
			$rid = intval($_POST['rec_id']);
			$comm = ajax_utf8(textFilter($_POST['comm']));
			
			//Проверка на существование записи
			if($_POST['g_tell'] == 1){
				$row = $db->super_query("SELECT add_date, text, public_id, attach, tell_uid, tell_date, public FROM `".PREFIX."_communities_wall` WHERE fast_comm_id = 0 AND id = '{$rid}'");
				if($row['tell_uid'])
					$row['author_user_id'] = $row['tell_uid'];
			} else
				$row = $db->super_query("SELECT add_date, text, author_user_id, tell_uid, tell_date, public, attach FROM `".PREFIX."_wall` WHERE fast_comm_id = '0' AND id = '{$rid}'");

			if($row){
				if($row['author_user_id'] != $user_id){
					if($row['tell_uid']){
						$row['add_date'] = $row['tell_date'];
						$row['author_user_id'] = $row['tell_uid'];
					} elseif($_POST['g_tell'] == 1){
						$row['public'] = 1;
						$row['author_user_id'] = $row['public_id'];
					}
						
					//Проверяем на существование этой записи у себя на стене
					$myRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_wall` WHERE tell_uid = '{$row['author_user_id']}' AND tell_date = '{$row['add_date']}' AND author_user_id = '{$user_id}'");
					if($myRow['cnt'] == false){
						$row['text'] = $db->safesql($row['text']);
						$row['attach'] = $db->safesql($row['attach']);
						
						//Всталвяем себе на стену
						$db->query("INSERT INTO `".PREFIX."_wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$row['text']}', add_date = '{$server_time}', fast_comm_id = 0, tell_uid = '{$row['author_user_id']}', tell_date = '{$row['add_date']}', public = '{$row['public']}', attach = '{$row['attach']}', tell_comm = '{$comm}'");
						$dbid = $db->insert_id();
						$db->query("UPDATE `".PREFIX."_users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$user_id}'");
						
						//Вставляем в ленту новостей
						$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$row['text']}', obj_id = '{$dbid}', action_time = '{$server_time}'");
						
						//Чистим кеш
						mozg_clear_cache_file("user_{$user_id}/profile_{$user_id}");
					} else
						echo 1;
				} else
					echo 1;
			}
			die();
		break;
		
		//################## Если выбрано "Подписчики сообщества" ##################//
		case "groups":
			NoAjaxQuery();
			
			$rid = intval($_POST['rec_id']);
			$sel_group = intval($_POST['sel_group']);
			$comm = ajax_utf8(textFilter($_POST['comm']));
			
			//Проверка на существование записи
			$row = $db->super_query("SELECT add_date, text, author_user_id, tell_uid, tell_date, public, attach FROM `".PREFIX."_wall` WHERE fast_comm_id = '0' AND id = '{$rid}'");
			
			if($row['tell_uid']){
				$row['add_date'] = $row['tell_date'];
				$row['author_user_id'] = $row['tell_uid'];
			}
			
			//ДЛя проверки что записи нет в сообществе
			if($row['public'])
				$check_IdGR = $row['tell_uid'];
			else
				$check_IdGR = '';
				
			//Проверка на админа
			$rowGroup = $db->super_query("SELECT admin, del, ban FROM `".PREFIX."_communities` WHERE id = '{$sel_group}'");
			
			//Проверяем на существование этой записи В сообществе
			$myRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_communities_wall` WHERE tell_uid = '{$row['author_user_id']}' AND public_id = '{$sel_group}' AND tell_date = '{$row['add_date']}'");

			if($sel_group != $check_IdGR AND $myRow['cnt'] == false AND stripos($rowGroup['admin'], "u{$user_id}|") !== false AND $rowGroup['del'] == 0 AND $rowGroup['ban'] == 0){
				$row['text'] = $db->safesql($row['text']);
				$row['attach'] = $db->safesql($row['attach']);
				
				//Вставляем саму запись в БД
				$db->query("INSERT INTO `".PREFIX."_communities_wall` SET public_id = '{$sel_group}', text = '{$row['text']}', attach = '{$row['attach']}', add_date = '{$server_time}', tell_uid = '{$row['author_user_id']}', tell_date = '{$row['add_date']}', public = '{$row['public']}', tell_comm = '{$comm}'");
				$dbid = $db->insert_id();
				$db->query("UPDATE `".PREFIX."_communities` SET rec_num = rec_num+1 WHERE id = '{$sel_group}'");
				
				//Вставляем в ленту новотсей
				$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$sel_group}', action_type = 11, action_text = '{$row['text']}', obj_id = '{$dbid}', action_time = '{$server_time}'");
			} else
				echo 1;
			
			die();
		break;
		
		//################## Если выбрано "Подписчики сообщества" из СООБЩЕСТВ ##################//
		case "groups_2":
			NoAjaxQuery();
			
			$rid = intval($_POST['rec_id']);
			$sel_group = intval($_POST['sel_group']);
			$comm = ajax_utf8(textFilter($_POST['comm']));
			
			//Проверка на существование записи
			$row = $db->super_query("SELECT add_date, text, public_id, attach, tell_uid, tell_date, public FROM `".PREFIX."_communities_wall` WHERE fast_comm_id = 0 AND id = '{$rid}'");
			
			if($row['tell_uid']){
				$tell_uid = $row['tell_uid'];
				$tell_date = $row['tell_date'];
				if($row['public'])
					$row['public_id'] = $tell_uid;
			} else {
				$tell_uid = $row['public_id'];
				$tell_date = $row['add_date'];
				$row['public'] = 1;
			}

			//Проверка на админа
			$rowGroup = $db->super_query("SELECT admin, del, ban FROM `".PREFIX."_communities` WHERE id = '{$sel_group}'");
			
			//Проверяем на существование этой записи В сообществе
			$myRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_communities_wall` WHERE tell_uid = '{$tell_uid}' AND public_id = '{$sel_group}' AND tell_date = '{$tell_date}'");
			
			if($sel_group != $row['public_id'] AND $myRow['cnt'] == false AND stripos($rowGroup['admin'], "u{$user_id}|") !== false AND $rowGroup['del'] == 0 AND $rowGroup['ban'] == 0){
			
				$row['text'] = $db->safesql($row['text']);
				$row['attach'] = $db->safesql($row['attach']);
				
				//Вставляем саму запись в БД
				$db->query("INSERT INTO `".PREFIX."_communities_wall` SET public_id = '{$sel_group}', text = '{$row['text']}', attach = '{$row['attach']}', add_date = '{$server_time}', tell_uid = '{$tell_uid}', tell_date = '{$tell_date}', public = '{$row['public']}', tell_comm = '{$comm}'");
				$dbid = $db->insert_id();
				$db->query("UPDATE `".PREFIX."_communities` SET rec_num = rec_num+1 WHERE id = '{$sel_group}'");
				
				//Вставляем в ленту новотсей
				$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$sel_group}', action_type = 11, action_text = '{$row['text']}', obj_id = '{$dbid}', action_time = '{$server_time}'");
				
			} else
				echo 1;
			
			die();
		break;
		
		//################### Если выбрано " Отправить личным сообщением" ###################//
		case "message":
			NoAjaxQuery();
			
			$for_user_id = intval($_POST['for_user_id']);
			$tell_comm = ajax_utf8(textFilter($_POST['comm']));
			$rid = intval($_POST['rec_id']);

			if($user_id != $for_user_id){
				
				//Проверка на существование получателя
				$row = $db->super_query("SELECT user_privacy FROM `".PREFIX."_users` WHERE user_id = '{$for_user_id}'");

				if($row){
					//Приватность
					$user_privacy = xfieldsdataload($row['user_privacy']);
					
					//ЧС
					$CheckBlackList = CheckBlackList($for_user_id);
				
					//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
					if($user_privacy['val_msg'] == 2)
						$check_friend = CheckFriends($for_user_id);
	
					if(!$CheckBlackList AND $user_privacy['val_msg'] == 1 OR $user_privacy['val_msg'] == 2 AND $check_friend)
						$xPrivasy = 1;
					else
						$xPrivasy = 0;
				
					if($xPrivasy){
						
						//Выводим данные о записи
						if($_POST['g_tell'] == 1)
						
							$row_rec = $db->super_query("SELECT add_date, text, public_id, attach, tell_uid, tell_date, public FROM `".PREFIX."_communities_wall` WHERE fast_comm_id = 0 AND id = '{$rid}'");
							
						else
						
							$row_rec = $db->super_query("SELECT add_date, text, author_user_id, tell_uid, tell_date, public, attach FROM `".PREFIX."_wall` WHERE fast_comm_id = '0' AND id = '{$rid}'");
						
						if($row_rec){
							$msg = $db->safesql($row_rec['text']);
							$attach_files = $db->safesql($row_rec['attach']);
							$theme = 'Запись на стене';
							
							if($row_rec['tell_uid']){
							
								$tell_uid = $row_rec['tell_uid'];
								$tell_date = $row_rec['tell_date'];
								
							} else {
								
								if($_POST['g_tell'] == 1){
								
									$row_rec['author_user_id'] = $row_rec['public_id'];
									$row_rec['public'] = 1;
									
								}
								
								$tell_uid = $row_rec['author_user_id'];
								$tell_date = $row_rec['add_date'];
								
							}
							
							//Отправляем сообщение получателю
							$db->query("INSERT INTO `".PREFIX."_messages` SET theme = '{$theme}', text = '{$msg}', for_user_id = '{$for_user_id}', from_user_id = '{$user_id}', date = '{$server_time}', pm_read = 'no', folder = 'inbox', history_user_id = '{$user_id}', attach = '".$attach_files."', tell_uid = '{$tell_uid}', tell_date = '{$tell_date}', public = '{$row_rec['public']}', tell_comm = '{$tell_comm}'");
							$dbid = $db->insert_id();

							//Сохраняем сообщение в папку отправленные
							$db->query("INSERT INTO `".PREFIX."_messages` SET theme = '{$theme}', text = '{$msg}', for_user_id = '{$user_id}', from_user_id = '{$for_user_id}', date = '{$server_time}', pm_read = 'no', folder = 'outbox', history_user_id = '{$user_id}', attach = '".$attach_files."', tell_uid = '{$tell_uid}', tell_date = '{$tell_date}', public = '{$row_rec['public']}', tell_comm = '{$tell_comm}'");

							//Обновляем кол-во новых сообщения у получателя
							$db->query("UPDATE `".PREFIX."_users` SET user_pm_num = user_pm_num+1 WHERE user_id = '{$for_user_id}'");
							
							//Проверка на наличии созданого диалога у себя
							$check_im = $db->super_query("SELECT iuser_id FROM `".PREFIX."_im` WHERE iuser_id = '".$user_id."' AND im_user_id = '".$for_user_id."'");
							if(!$check_im)
								$db->query("INSERT INTO ".PREFIX."_im SET iuser_id = '".$user_id."', im_user_id = '".$for_user_id."', idate = '".$server_time."', all_msg_num = 1");
							else
								$db->query("UPDATE ".PREFIX."_im  SET idate = '".$server_time."', all_msg_num = all_msg_num+1 WHERE iuser_id = '".$user_id."' AND im_user_id = '".$for_user_id."'");
								
							//Проверка на наличии созданого диалога у получателя, а если есть то просто обновляем кол-во новых сообщений в диалоге
							$check_im_2 = $db->super_query("SELECT iuser_id FROM ".PREFIX."_im WHERE iuser_id = '".$for_user_id."' AND im_user_id = '".$user_id."'");
							if(!$check_im_2)
								$db->query("INSERT INTO ".PREFIX."_im SET iuser_id = '".$for_user_id."', im_user_id = '".$user_id."', msg_num = 1, idate = '".$server_time."', all_msg_num = 1");
							else
								$db->query("UPDATE ".PREFIX."_im  SET idate = '".$server_time."', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE iuser_id = '".$for_user_id."' AND im_user_id = '".$user_id."'");
							
							//Читисм кеш обновлений
							mozg_clear_cache_file('user_'.$for_user_id.'/im');
							mozg_create_cache('user_'.$for_user_id.'/im_update', '1');
							
							//Отправка уведомления на E-mail
							if($config['news_mail_8'] == 'yes' AND $user_id != $for_user_id){
								$rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `".PREFIX."_users` WHERE user_id = '".$for_user_id."'");
								if($rowUserEmail['user_email']){
									include_once ENGINE_DIR.'/classes/mail.php';
									$mail = new dle_mail($config);
									$rowMyInfo = $db->super_query("SELECT user_search_pref FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
									$rowEmailTpl = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '8'");
									$rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
									$rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
									$rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'].'messages/show/'.$dbid, $rowEmailTpl['text']);
									$mail->send($rowUserEmail['user_email'], 'Новое персональное сообщение', $rowEmailTpl['text']);
								}
							}
							
						}
								
					} else
						echo 'err_privacy';
				} else
					echo 'no_user';
			} else
				echo 'max_strlen';
				
			die();
		break;
		
		//################## Страница отправки ##################//
		default:
			
			//Выводим сообщества
			$sql_ = $db->super_query("SELECT id, title FROM `".PREFIX."_communities` WHERE admin regexp '[[:<:]](u{$user_id})[[:>:]]' ORDER by `traf` DESC LIMIT 0, 50", 1);
			
			//Выводим список друзей
			$sql_fr = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 ORDER by `views` DESC LIMIT 0, 50", 1);
			
			$tpl->load_template('repost/send.tpl');
			
			if($sql_){
			
				foreach($sql_ as $row)
				
					$groups_list .= '<option value="'.$row['id'].'">'.stripslashes($row['title']).'</option>';
				
			}
			$tpl->set('{groups-list}', $groups_list);
			
			if($sql_fr){
			
				foreach($sql_fr as $row_fr)
				
					$friends_list .= '<option value="'.$row_fr['friend_id'].'">'.$row_fr['user_search_pref'].'</option>';
				
			}
			$tpl->set('{friends-list}', $friends_list);
			
			if(!$friends_list)
				$tpl->set('{disabled-friends}', 'disabled');
			else
				$tpl->set('{disabled-friends}', '');

			if(!$groups_list)
				$tpl->set('{groups-friends}', 'disabled');
			else
				$tpl->set('{groups-friends}', '');
			
			$tpl->compile('content');
			
			AjaxTpl();
		
	}
	
	$tpl->clear();
	$db->free();
	
}

die();
?>