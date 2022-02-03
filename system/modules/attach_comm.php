<?php
/* 
	Appointment: Комментарии к прикприпленным фото
	File: attach_comm.php 
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
		
		//################### Удаление комментария ###################//
		case "delcomm":
			
			$id = intval($_POST['id']);
			$purl = $db->safesql(totranslit($_POST['purl']));
			
			//Выводим данные о комментариии
			$row = $db->super_query("SELECT tb1.forphoto, auser_id, tb2.ouser_id FROM `".PREFIX."_attach_comm` tb1, `".PREFIX."_attach` tb2 WHERE tb1.id = '{$id}' AND tb1.forphoto = '{$purl}'");
			$tab_photos = false;

			//Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
			if(!$row){
				
				//Проверка в таблице PREFIX_photos
				$row_photos = $db->super_query("SELECT tb1.pid, owner_id, tb2.user_id FROM `".PREFIX."_photos_comments` tb1, `".PREFIX."_photos` tb2 WHERE tb1.id = '{$id}' AND tb1.photo_name = '{$purl}'");
				$tab_photos = true;
				
				$row['auser_id'] = $row_photos['owner_id'];
				$row['ouser_id'] = $row_photos['user_id'];
				$row['pid'] = $row_photos['pid'];
				
			}

			if($row['auser_id'] == $user_id OR $row['ouser_id'] == $user_id){
				
				//Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
				if($tab_photos){
					
					$db->query("DELETE FROM `".PREFIX."_photos_comments` WHERE id = '{$id}'");
					$db->query("UPDATE `".PREFIX."_photos` SET comm_num = comm_num-1 WHERE id = '{$row['pid']}'");
					
					$row2 = $db->super_query("SELECT album_id FROM `".PREFIX."_photos` WHERE id = '{$row['pid']}'");
					
					$db->query("UPDATE `".PREFIX."_albums` SET comm_num = comm_num-1 WHERE aid = '{$row2['album_id']}'");
				
				} else {
				
					//Обновляем кол-во комментов
					$db->query("UPDATE `".PREFIX."_attach` SET acomm_num = acomm_num-1 WHERE photo = '{$row['forphoto']}'");
					
					//Удаляем комментарий
					$db->query("DELETE FROM `".PREFIX."_attach_comm` WHERE forphoto = '{$row['forphoto']}' AND id = '{$id}'");
				
				}
				
			}
			
		break;
		
		//################### Добавления комментария ###################//
		case "addcomm":
			
			$text = ajax_utf8(textFilter($_POST['text']));
			$purl = $db->safesql(totranslit($_POST['purl']));
			
			//Проверка на существования фотки в таблице PREFIX_attach
			$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_attach` WHERE photo = '{$purl}'");
			$tab_photos = false;
			
			//Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
			if(!$row['cnt']){
			
				$row = $db->super_query("SELECT album_id, user_id, photo_name, id FROM `".PREFIX."_photos` WHERE photo_name = '{$purl}'");
				$tab_photos = true;
				
				if($row['album_id'])
					$row['cnt'] = 1;
				
			}

			//Если фотка есть
			if(isset($text) AND !empty($text) AND $row['cnt']){
				
				if($tab_photos){
					
					$hash = md5($user_id.$server_time.$_IP.$user_info['user_email'].rand(0, 1000000000)).$text.$purl;
					
					$db->query("INSERT INTO `".PREFIX."_photos_comments` (pid, user_id, text, date, hash, album_id, owner_id, photo_name) VALUES ('{$row['id']}', '{$user_id}', '{$text}', NOW(), '{$hash}', '{$row['album_id']}', '{$row['user_id']}', '{$row['photo_name']}')");
					$id = $db->insert_id();
					
					$db->query("UPDATE `".PREFIX."_photos` SET comm_num = comm_num+1 WHERE id = '{$row['id']}'");
					
					$db->query("UPDATE `".PREFIX."_albums` SET comm_num = comm_num+1 WHERE aid = '{$row['album_id']}'");
				
				} else {
				
					//Вставляем сам комментарий
					$db->query("INSERT INTO `".PREFIX."_attach_comm` SET forphoto = '{$purl}', auser_id = '{$user_id}', text = '{$text}', adate = '{$server_time}'");
					$id = $db->insert_id();
					
					//Обновляем кол-во комментов
					$db->query("UPDATE `".PREFIX."_attach` SET acomm_num = acomm_num+1 WHERE photo = '{$purl}'");
					
				}
				
				$tpl->load_template('attach/comment.tpl');
				$tpl->set('{id}', $id);
				$tpl->set('{uid}', $user_id);
				$tpl->set('{comment}', stripslashes($text));
				$tpl->set('{purl}', $purl);
				$tpl->set('{author}', $user_info['user_search_pref']);
				$tpl->set('{online}', $lang['online']);
				$tpl->set('{date}', langdate('сегодня в H:i', $server_time));
				if($user_info['user_photo']) $tpl->set('{ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
				else $tpl->set('{ava}', '{theme}/images/no_ava_50.png');
				$tpl->set('[owner]', '');
				$tpl->set('[/owner]', '');
				$tpl->compile('content');
				
				AjaxTpl();
				
			}
		
		break;
		
		//################### Показ пред.комментариев ###################//
		case "prevcomm":
			
			$foSQLurl = $db->safesql(totranslit($_POST['purl']));
			
			//Выводим данные о владельце фото
			$row = $db->super_query("SELECT ouser_id, acomm_num FROM `".PREFIX."_attach` WHERE photo = '{$foSQLurl}'");
			$tab_photos = false;
			
			//Если нету то проверяем в таблице PREFIX_photos
			if(!$row){
				
				$row = $db->super_query("SELECT user_id, comm_num FROM `".PREFIX."_photos` WHERE photo_name = '{$foSQLurl}'");
				$row['acomm_num'] = $row['comm_num'];
				$row['ouser_id'] = $row['user_id'];
				$tab_photos = true;
				
			}
			
			$limit = 10;
			$first_id = intval($_POST['first_id']);
			$page_post = intval($_POST['page']);
			if($page_post <= 0) $page_post = 1;

			$start_limit = $row['acomm_num']-($page_post*$limit)-3;
			if($start_limit < 0) $start_limit = 0;
			
			if($tab_photos)
			
				$sql_comm = $db->super_query("SELECT tb1.user_id, text, date, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_photos_comments` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}' AND id < '{$first_id}' ORDER by `date` ASC LIMIT {$start_limit}, {$limit}", 1);
				
			else
			
				$sql_comm = $db->super_query("SELECT tb1.auser_id, text, adate, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_attach_comm` tb1, `".PREFIX."_users` tb2 WHERE tb1.auser_id = tb2.user_id AND tb1.forphoto = '{$foSQLurl}' AND id < '{$first_id}' ORDER by `adate` ASC LIMIT {$start_limit}, {$limit}", 1);
			
			$tpl->load_template('attach/comment.tpl');
					
			foreach($sql_comm as $row_comm){
						
				if($tab_photos){
							
					$row_comm['adate'] = strtotime($row_comm['date']);
					$row_comm['auser_id'] = $row_comm['user_id'];
							
				}
						
				$tpl->set('{comment}', stripslashes($row_comm['text']));
				$tpl->set('{uid}', $row_comm['auser_id']);
				$tpl->set('{id}', $row_comm['id']);
				$tpl->set('{purl}', $foSQLurl);
				$tpl->set('{author}', $row_comm['user_search_pref']);
										
				if($row_comm['user_photo']) $tpl->set('{ava}', '/uploads/users/'.$row_comm['auser_id'].'/50_'.$row_comm['user_photo']);
				else $tpl->set('{ava}', '{theme}/images/no_ava_50.png');
											
				OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
				megaDate($row_comm['adate']);
										
				if($row_comm['auser_id'] == $user_id OR $row['ouser_id'] == $user_id){
					$tpl->set('[owner]', '');
					$tpl->set('[/owner]', '');
				} else 
					$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
								
				$tpl->compile('content');
					
			}
				
			AjaxTpl();
			
		break;

		default:
			
			$photo_url = $_POST['photo'];
			$resIMGurl = explode('/', $photo_url);
			$foSQLurl = end($resIMGurl);
			$foSQLurl = $db->safesql(totranslit($foSQLurl));
			
			//Выводим данные о владельце фото
			$row = $db->super_query("SELECT tb1.ouser_id, acomm_num, add_date, tb2.user_search_pref, user_country_city_name FROM `".PREFIX."_attach` tb1, `".PREFIX."_users` tb2 WHERE tb1.ouser_id = tb2.user_id AND tb1.photo = '{$foSQLurl}'");
			$tab_photos = false;
			
			//Если нету то проверяем в таблице PREFIX_photos
			if(!$row){
				
				$row = $db->super_query("SELECT tb1.user_id, comm_num, date, tb2.user_search_pref, user_country_city_name FROM `".PREFIX."_photos` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}'");
				$row['acomm_num'] = $row['comm_num'];
				$row['ouser_id'] = $row['user_id'];
				$row['add_date'] = strtotime($row['date']);
				$tab_photos = true;
				
			}
			
			if($row){
			
				//Выводим комментарии если они есть
				if($row['acomm_num']){
			
					if($row['acomm_num'] > 7)
						$limit_comm = $row['acomm_num']-3;
					else
						$limit_comm = 0;
					
					if($tab_photos)
					
						$sql_comm = $db->super_query("SELECT tb1.user_id, text, date, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_photos_comments` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}' ORDER by `date` ASC LIMIT {$limit_comm}, {$row['acomm_num']}", 1);

					else
					
						$sql_comm = $db->super_query("SELECT tb1.auser_id, text, adate, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_attach_comm` tb1, `".PREFIX."_users` tb2 WHERE tb1.auser_id = tb2.user_id AND tb1.forphoto = '{$foSQLurl}' ORDER by `adate` ASC LIMIT {$limit_comm}, {$row['acomm_num']}", 1);
					
					$tpl->load_template('attach/comment.tpl');
					
					foreach($sql_comm as $row_comm){

						if($tab_photos){
							
							$row_comm['adate'] = strtotime($row_comm['date']);
							$row_comm['auser_id'] = $row_comm['user_id'];
							
						}
						
						$tpl->set('{comment}', stripslashes($row_comm['text']));
						$tpl->set('{uid}', $row_comm['auser_id']);
						$tpl->set('{id}', $row_comm['id']);
						$tpl->set('{purl}', $foSQLurl);
						$tpl->set('{author}', $row_comm['user_search_pref']);
										
						if($row_comm['user_photo']) $tpl->set('{ava}', '/uploads/users/'.$row_comm['auser_id'].'/50_'.$row_comm['user_photo']);
						else $tpl->set('{ava}', '{theme}/images/no_ava_50.png');
											
						OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
						megaDate($row_comm['adate']);
										
						if($row_comm['auser_id'] == $user_id OR $row['ouser_id'] == $user_id){
							$tpl->set('[owner]', '');
							$tpl->set('[/owner]', '');
						} else 
							$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
								
						$tpl->compile('comments');
					}
					
				}
				
				$tpl->load_template('attach/addcomm.tpl');
				
				//Кнопка показ пред сообщений
				if($row['acomm_num'] > 7){
					
					$tpl->set('[comm]', '');
					$tpl->set('[/comm]', '');
					
				} else
					$tpl->set_block("'\\[comm\\](.*?)\\[/comm\\]'si","");
					
				$tpl->set('{author}', $row['user_search_pref']);
				$tpl->set('{uid}', $row['ouser_id']);
				$tpl->set('{purl}', $foSQLurl);
				$tpl->set('{purl-js}', substr($foSQLurl, 0, 20));
				
				if($row['add_date'])
					megaDate($row['add_date']);
				else
					$tpl->set('{date}', '');
				
				$author_info = explode('|', $row['user_country_city_name']);
				if($author_info[0]) $tpl->set('{author-info}', $author_info[0]); 
				else $tpl->set('{author-info}', '');
				if($author_info[1]) $tpl->set('{author-info}', $author_info[0].', '.$author_info[1].'<br />');
				
				$tpl->set('{comments}', $tpl->result['comments']);
				$tpl->compile('content');
				
				AjaxTpl();
			
			}
	}
	
	$tpl->clear();
	$db->free();
	
}

exit();
?>