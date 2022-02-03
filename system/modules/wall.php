<?php
/* 
	Appointment: Стена
	File: wall.php 
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
	
if($logged){
	$act = $_GET['act'];
	$user_id = $user_info['user_id'];
	$limit_select = 10;
	$limit_page = 0;
	
	switch($act){
	
		//################### Добвление новой записи на стену ###################//
		case "send":
			NoAjaxQuery();
			$wall_text = ajax_utf8(textFilter($_POST['wall_text']));
			$attach_files = ajax_utf8(textFilter($_POST['attach_files'], false, true));
			$for_user_id = intval($_POST['for_user_id']);
			$fast_comm_id = intval($_POST['rid']);
			$answer_comm_id = intval($_POST['answer_comm_id']);
			$str_date = time();
			
			if(!$fast_comm_id) AntiSpam('wall');
			else AntiSpam('comments');
			
			//Проверка на наличии юзера которум отправляется запись
			$check = $db->super_query("SELECT user_privacy, user_last_visit FROM `".PREFIX."_users` WHERE user_id = '{$for_user_id}'");
			
			if($check){

				if(isset($wall_text) AND !empty($wall_text) OR isset($attach_files) AND !empty($attach_files)){
					
					//Приватность
					$user_privacy = xfieldsdataload($check['user_privacy']);
					
					//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
					if($user_privacy['val_wall2'] == 2 OR $user_privacy['val_wall1'] == 2 OR $user_privacy['val_wall3'] == 2 AND $user_id != $for_user_id)
						$check_friend = CheckFriends($for_user_id);

					if(!$fast_comm_id){
						if($user_privacy['val_wall2'] == 1 OR $user_privacy['val_wall2'] == 2 AND $check_friend OR $user_id == $for_user_id)
							$xPrivasy = 1;
						else
							$xPrivasy = 0;
					} else {
						if($user_privacy['val_wall3'] == 1 OR $user_privacy['val_wall3'] == 2 AND $check_friend OR $user_id == $for_user_id)
							$xPrivasy = 1;
						else
							$xPrivasy = 0;
					}
					
					if($user_privacy['val_wall1'] == 1 OR $user_privacy['val_wall1'] == 2 AND $check_friend OR $user_id == $for_user_id)
						$xPrivasyX = 1;
					else
						$xPrivasyX = 0;

					//ЧС
					$CheckBlackList = CheckBlackList($for_user_id);
					if(!$CheckBlackList){
						if($xPrivasy){
							
							//Оприделение изображения к ссылке
							if(stripos($attach_files, 'link|') !== false){
								$attach_arr = explode('||', $attach_files);
								$cnt_attach_link = 1;
								foreach($attach_arr as $attach_file){
									$attach_type = explode('|', $attach_file);
									if($attach_type[0] == 'link' AND preg_match('/http:\/\/(.*?)+$/i', $attach_type[1]) AND $cnt_attach_link == 1){
										$domain_url_name = explode('/', $attach_type[1]);
										$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
										$rImgUrl = $attach_type[4];
										$rImgUrl = str_replace("\\", "/", $rImgUrl);
										$img_name_arr = explode(".", $rImgUrl);
										$img_format = totranslit(end($img_name_arr));
										$image_name = substr(md5($server_time.md5($rImgUrl)), 0, 15);
										
										//Разришенные форматы
										$allowed_files = array('jpg', 'jpeg', 'jpe', 'png');

										//Загружаем картинку на сайт
										if(in_array(strtolower($img_format), $allowed_files) AND preg_match("/http:\/\/(.*?)(.jpg|.png|.jpeg|.jpe)/i", $rImgUrl)){
													
											//Директория загрузки фото
											$upload_dir = ROOT_DIR.'/uploads/attach/'.$user_id;
													
											//Если нет папки юзера, то создаём её
											if(!is_dir($upload_dir)){ 
												@mkdir($upload_dir, 0777);
												@chmod($upload_dir, 0777);
											}
													
											//Подключаем класс для фотографий
											include ENGINE_DIR.'/classes/images.php';

											if(@copy($rImgUrl, $upload_dir.'/'.$image_name.'.'.$img_format)){
												$tmb = new thumbnail($upload_dir.'/'.$image_name.'.'.$img_format);
												$tmb->size_auto('100x80');
												$tmb->jpeg_quality(100);
												$tmb->save($upload_dir.'/'.$image_name.'.'.$img_format);
												
												$attach_files = str_replace($attach_type[4], '/uploads/attach/'.$user_id.'/'.$image_name.'.'.$img_format, $attach_files);
											}
										}
										$cnt_attach_link++;
									}
								}
							}
							
							$attach_files = str_replace('vote|', 'hack|', $attach_files);
							$attach_files = str_replace(array('&amp;#124;', '&amp;raquo;', '&amp;quot;'), array('&#124;', '&raquo;', '&quot;'), $attach_files);
							
							//Голосование
							$vote_title = ajax_utf8(textFilter($_POST['vote_title'], false, true));
							$vote_answer_1 = ajax_utf8(textFilter($_POST['vote_answer_1'], false, true));

							$ansers_list = array();
							
							if(isset($vote_title) AND !empty($vote_title) AND isset($vote_answer_1) AND !empty($vote_answer_1)){
								
								for($vote_i = 1; $vote_i <= 10; $vote_i++){
									
									$vote_answer = ajax_utf8(textFilter($_POST['vote_answer_'.$vote_i], false, true));
									$vote_answer = str_replace('|', '&#124;', $vote_answer);
									
									if($vote_answer)
										$ansers_list[] = $vote_answer;
									
								}
								
								$sql_answers_list = implode('|', $ansers_list);
								
								//Вставляем голосование в БД
								$db->query("INSERT INTO `".PREFIX."_votes` SET title = '{$vote_title}', answers = '{$sql_answers_list}'");
								
								$attach_files = $attach_files."vote|{$db->insert_id()}||";
								
							}
							
							//Если добавляется ответ на комментарий то вносим в ленту новостей "ответы"
							if($answer_comm_id){
								
								//Выводим ид владельца комменатрия
								$row_owner2 = $db->super_query("SELECT author_user_id FROM `".PREFIX."_wall` WHERE id = '{$answer_comm_id}' AND fast_comm_id != '0'");
								
								//Проверка на то, что юзер не отвечает сам себе
								if($user_id != $row_owner2['author_user_id'] AND $row_owner2){
									
									$check2 = $db->super_query("SELECT user_last_visit, user_name FROM `".PREFIX."_users` WHERE user_id = '{$row_owner2['author_user_id']}'");
									
									$wall_text = str_replace($check2['user_name'], "<a href=\"/u{$row_owner2['author_user_id']}\" onClick=\"Page.Go(this.href); return false\" class=\"newcolor000\">{$check2['user_name']}</a>", $wall_text);
									
									//Вставляем в ленту новостей
									$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 6, action_text = '{$wall_text}', obj_id = '{$answer_comm_id}', for_user_id = '{$row_owner2['author_user_id']}', action_time = '{$server_time}'");
									
									//Вставляем событие в моментальные оповещания
									$update_time = $server_time - 70;
	
									if($check2['user_last_visit'] >= $update_time){
									
										$db->query("INSERT INTO `".PREFIX."_updates` SET for_user_id = '{$row_owner2['author_user_id']}', from_user_id = '{$user_id}', type = '5', date = '{$server_time}', text = '{$wall_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/wall{$for_user_id}_{$fast_comm_id}'");
									
										mozg_create_cache("user_{$row_owner2['author_user_id']}/updates", 1);
									
									//ИНАЧЕ Добавляем +1 юзеру для оповещания
									} else {
										
										$cntCacheNews = mozg_cache("user_{$row_owner2['author_user_id']}/new_news");
										mozg_create_cache("user_{$row_owner2['author_user_id']}/new_news", ($cntCacheNews+1));
										
									}
									
								}
								
							}

							//Вставляем саму запись в БД
							$db->query("INSERT INTO `".PREFIX."_wall` SET author_user_id = '{$user_id}', for_user_id = '{$for_user_id}', text = '{$wall_text}', add_date = '{$str_date}', fast_comm_id = '{$fast_comm_id}', attach = '".$attach_files."'");
							$dbid = $db->insert_id();
							
							//Если пользователь пишет сам у себя на стене, то вносим это в "Мои Новости"
							if($user_id == $for_user_id AND !$fast_comm_id){
								$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$wall_text}', obj_id = '{$dbid}', action_time = '{$str_date}'");
							}
							
							//Если добавляется комментарий к записи то вносим в ленту новостей "ответы"
							if($fast_comm_id AND !$answer_comm_id){
								//Выводим ид владельца записи
								$row_owner = $db->super_query("SELECT author_user_id FROM `".PREFIX."_wall` WHERE id = '{$fast_comm_id}'");
								
								if($user_id != $row_owner['author_user_id'] AND $row_owner){
									$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 6, action_text = '{$wall_text}', obj_id = '{$fast_comm_id}', for_user_id = '{$row_owner['author_user_id']}', action_time = '{$str_date}'");

									//Вставляем событие в моментальные оповещания
									$update_time = $server_time - 70;
									
									if($check['user_last_visit'] >= $update_time){
									
										$db->query("INSERT INTO `".PREFIX."_updates` SET for_user_id = '{$row_owner['author_user_id']}', from_user_id = '{$user_id}', type = '1', date = '{$server_time}', text = '{$wall_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/wall{$for_user_id}_{$fast_comm_id}'");
									
										mozg_create_cache("user_{$row_owner['author_user_id']}/updates", 1);
									
									//ИНАЧЕ Добавляем +1 юзеру для оповещания
									} else {
										
										$cntCacheNews = mozg_cache('user_'.$row_owner['author_user_id'].'/new_news');
										mozg_create_cache('user_'.$row_owner['author_user_id'].'/new_news', ($cntCacheNews+1));
										
									}
									
									//Отправка уведомления на E-mail
									if($config['news_mail_2'] == 'yes'){
										$rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `".PREFIX."_users` WHERE user_id = '".$row_owner['author_user_id']."'");
										if($rowUserEmail['user_email']){
											include_once ENGINE_DIR.'/classes/mail.php';
											$mail = new dle_mail($config);
											$rowMyInfo = $db->super_query("SELECT user_search_pref FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
											$rowEmailTpl = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '2'");
											$rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
											$rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
											$rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'].'wall'.$row_owner['author_user_id'].'_'.$fast_comm_id, $rowEmailTpl['text']);
											$mail->send($rowUserEmail['user_email'], 'Ответ на запись', $rowEmailTpl['text']);
										}
									}
								}
							}

							if($fast_comm_id)
								$db->query("UPDATE `".PREFIX."_wall` SET fasts_num = fasts_num+1 WHERE id = '{$fast_comm_id}'");
							else
								$db->query("UPDATE `".PREFIX."_users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$for_user_id}'");

							//Подгружаем и объявляем класс для стены
							include ENGINE_DIR.'/classes/wall.php';
							$wall = new wall();
				
							//Если добавлена просто запись, то сразу обновляем все записи на стене
							AntiSpamLogInsert('wall');
							if(!$fast_comm_id){
									
								if($xPrivasyX){
									$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, type, tell_uid, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE for_user_id = '{$for_user_id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '0' ORDER by `add_date` DESC LIMIT 0, {$limit_select}");
									$wall->template('wall/record.tpl');
									$wall->compile('content');
									$wall->select();
								}
									
								mozg_clear_cache_file('user_'.$for_user_id.'/profile_'.$for_user_id);
								
								//Отправка уведомления на E-mail
								if($config['news_mail_7'] == 'yes' AND $user_id != $for_user_id){
									$rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `".PREFIX."_users` WHERE user_id = '".$for_user_id."'");
									if($rowUserEmail['user_email']){
										include_once ENGINE_DIR.'/classes/mail.php';
										$mail = new dle_mail($config);
										$rowMyInfo = $db->super_query("SELECT user_search_pref FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
										$rowEmailTpl = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '7'");
										$rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
										$rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
										$rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'].'wall'.$for_user_id.'_'.$dbid, $rowEmailTpl['text']);
										$mail->send($rowUserEmail['user_email'], 'Новая запись на стене', $rowEmailTpl['text']);
									}
								}
									
							//Если добавлен комментарий к записи то просто обновляем нужную часть, тоесть только часть комментариев, но не всю стену
							} else {
								
								AntiSpamLogInsert('comments');
								
								//Выводим кол-во комментов к записи
								$row = $db->super_query("SELECT fasts_num FROM `".PREFIX."_wall` WHERE id = '{$fast_comm_id}'");
								$record_fasts_num = $row['fasts_num'];
								if($record_fasts_num > 3)
									$limit_comm_num = $row['fasts_num']-3;
								else
									$limit_comm_num = 0;
									
								$wall->comm_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, tb2.user_photo, user_search_pref, user_last_visit FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '{$fast_comm_id}' ORDER by `add_date` ASC LIMIT {$limit_comm_num}, 3");
									
								if($_POST['type'] == 1)
									$wall->comm_template('news/news.tpl');
								else if($_POST['type'] == 2)
									$wall->comm_template('wall/one_record.tpl');
								else
									$wall->comm_template('wall/record.tpl');
									
								$wall->comm_compile('content');
								$wall->comm_select();
							}
							
							AjaxTpl();
							
						} else
							echo 'err_privacy';
					} else
						echo 'err_privacy';
				}
			}

			die();
		break;
		
		//################### Удаление записи со стены ###################//
		case "delet":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			//Проверка на существование записи и выводим ID владельца записи и кому предназначена запись
			$row = $db->super_query("SELECT author_user_id, for_user_id, fast_comm_id, add_date, attach FROM `".PREFIX."_wall` WHERE id = '{$rid}'");
			if($row['author_user_id'] == $user_id OR $row['for_user_id'] == $user_id){
				
				//удаляем саму запись
				$db->query("DELETE FROM `".PREFIX."_wall` WHERE id = '{$rid}'");

				//Если удаляется НЕ комментарий к записи
				if(!$row['fast_comm_id']){
					//удаляем комменты к записиы
					$db->query("DELETE FROM `".PREFIX."_wall` WHERE fast_comm_id = '{$rid}'");
					
					//удаляем "мне нравится"
					$db->query("DELETE FROM `".PREFIX."_wall_like` WHERE rec_id = '{$rid}'");
					
					//обновляем кол-во записей
					$db->query("UPDATE `".PREFIX."_users` SET user_wall_num = user_wall_num-1 WHERE user_id = '{$row['for_user_id']}'");
					
					//Чистим кеш
					mozg_clear_cache_file('user_'.$row['for_user_id'].'/profile_'.$row['for_user_id']);
					
					//удаляем из ленты новостей
					$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$rid}' AND action_type = 6");
					
					//Удаляем фотку из прикрипленой ссылке, если она есть
					if(stripos($row['attach'], 'link|') !== false){
						$attach_arr = explode('link|', $row['attach']);
						$attach_arr2 = explode('|/uploads/attach/'.$user_id.'/', $attach_arr[1]);
						$attach_arr3 = explode('||', $attach_arr2[1]);
						if($attach_arr3[0])
							@unlink(ROOT_DIR.'/uploads/attach/'.$user_id.'/'.$attach_arr3[0]);	
					}
				
					$action_type = 1;
				}

				//Если удаляется комментарий к записи
				if($row['fast_comm_id']){
					$db->query("UPDATE `".PREFIX."_wall` SET fasts_num = fasts_num-1 WHERE id = '{$row['fast_comm_id']}'");
					$rid = $row['fast_comm_id'];
					
					//удаляем из ленты новостей
					$db->query("DELETE FROM `".PREFIX."_news` WHERE action_time = '{$row['add_date']}' AND action_type = '6' AND ac_user_id = '{$row['author_user_id']}'");
					
					$action_type = 6;
				}
				
				//удаляем из ленты новостей
				$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$rid}' AND action_time = '{$row['add_date']}' AND action_type = {$action_type}");
			}
			
			die();
		break;
		
		//################### Ставим "Мне нравится" ###################//
		case "like_yes":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			//Проверка на существование записи
			$row = $db->super_query("SELECT text, for_user_id, likes_users, author_user_id FROM `".PREFIX."_wall` WHERE id = '{$rid}'");
			if($row){
				//Проверка на то что этот юзер ставил уже мне нрав или нет
				$likes_users = explode('|', str_replace('u', '', $row['likes_users']));
				if(!in_array($user_id, $likes_users)){
					$db->query("INSERT INTO `".PREFIX."_wall_like` SET rec_id = '{$rid}', user_id = '{$user_id}', date = '{$server_time}'");

					$db->query("UPDATE `".PREFIX."_wall` SET likes_num = likes_num+1, likes_users = '|u{$user_id}|{$row['likes_users']}' WHERE id = '{$rid}'");
					
					if($user_id != $row['author_user_id']){
					
						//Вставляем событие в моментальные оповещания
						$row_owner = $db->super_query("SELECT user_last_visit FROM `".PREFIX."_users` WHERE user_id = '{$row['author_user_id']}'");
						$update_time = $server_time - 70;
						
						if($row_owner['user_last_visit'] >= $update_time){
							
							$row['text'] = strip_tags($row['text']);
							if($row['text']) $wall_text = ' &laquo;'.iconv_substr($row['text'], 0, 70, 'utf-8').'&raquo;';
							else $wall_text = '.';
							
							$myRow = $db->super_query("SELECT user_sex FROM `".PREFIX."_users` WHERE user_id = '{$user_info['user_id']}'");
							if($myRow['user_sex'] == 2) $action_update_text = 'оценила Вашу запись'.$wall_text;
							else $action_update_text = 'оценил Вашу запись'.$wall_text;
							
							$db->query("INSERT INTO `".PREFIX."_updates` SET for_user_id = '{$row['author_user_id']}', from_user_id = '{$user_info['user_id']}', type = '10', date = '{$server_time}', text = '{$action_update_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/wall{$row['author_user_id']}_{$rid}'");
						
							mozg_create_cache("user_{$row['author_user_id']}/updates", 1);
						
						}

						//Добавляем в ленту новостей "ответы"

						$generateLastTime = $server_time-10800;
						$row_news = $db->super_query("SELECT ac_id, action_text, action_time FROM `".PREFIX."_news` WHERE action_time > '{$generateLastTime}' AND action_type = 7 AND obj_id = '{$rid}'");
						if($row_news)
							$db->query("UPDATE `".PREFIX."_news` SET action_text = '|u{$user_id}|{$row_news['action_text']}', action_time = '{$server_time}' WHERE obj_id = '{$rid}' AND action_type = 7 AND action_time = '{$row_news['action_time']}'");
						else
							$db->query("INSERT INTO `".PREFIX."_news` SET ac_user_id = '{$user_id}', action_type = 7, action_text = '|u{$user_id}|', obj_id = '{$rid}', for_user_id = '{$row['author_user_id']}', action_time = '{$server_time}'");
					}
				}
			}

			die();
		break;
		
		//################### Удаляем "Мне нравится" ###################//
		case "like_no":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			//Проверка на существование записи
			$row = $db->super_query("SELECT likes_users FROM `".PREFIX."_wall` WHERE id = '{$rid}'");
			if($row){
				//Проверка на то что этот юзер ставил уже мне нрав или нет
				$likes_users = explode('|', str_replace('u', '', $row['likes_users']));
				if(in_array($user_id, $likes_users)){
					$db->query("DELETE FROM `".PREFIX."_wall_like` WHERE rec_id = '{$rid}' AND user_id = '{$user_id}'");
					$newListLikesUsers = strtr($row['likes_users'], array('|u'.$user_id.'|' => ''));
					$db->query("UPDATE `".PREFIX."_wall` SET likes_num = likes_num-1, likes_users = '{$newListLikesUsers}' WHERE id = '{$rid}'");
					
					//удаляем из ленты новостей
					$row_news = $db->super_query("SELECT ac_id, action_text FROM `".PREFIX."_news` WHERE action_type = 7 AND obj_id = '{$rid}'");
					$row_news['action_text'] = strtr($row_news['action_text'], array('|u'.$user_id.'|' => ''));
					if($row_news['action_text'])
						$db->query("UPDATE `".PREFIX."_news` SET action_text = '{$row_news['action_text']}' WHERE obj_id = '{$rid}' AND action_type = 7");
					else
						$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$rid}' AND action_type = 7");
				}
			}

			die();
		break;
		
		//################### Выводим первых 7 юзеров которые поставили "мне нравится" ###################//
		case "liked_users":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			$sql_ = $db->super_query("SELECT tb1.user_id, tb2.user_photo FROM `".PREFIX."_wall_like` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.rec_id = '{$rid}' ORDER by `date` DESC LIMIT 0, 7", 1);
			if($sql_){
				foreach($sql_ as $row){
					if($row['user_photo']) $ava = '/uploads/users/'.$row['user_id'].'/50_'.$row['user_photo'];
					else $ava = '/templates/'.$config['temp'].'/images/no_ava_50.png';
					echo '<a href="/u'.$row['user_id'].'" id="Xlike_user'.$row['user_id'].'_'.$rid.'" onClick="Page.Go(this.href); return false"><img src="'.$ava.'" width="32" /></a>';
				}
			}
			die();
		break;
		
		//################### Выводим всех юзеров которые поставили "мне нравится" ###################//
		case "all_liked_users":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			$liked_num = intval($_POST['liked_num']);
			
			if($_POST['page'] > 0) $page = intval($_POST['page']); else $page = 1;
			$gcount = 24;
			$limit_page = ($page-1)*$gcount;
			
			if(!$liked_num)
				$liked_num = 24;
			
			if($rid AND $liked_num){
				$sql_ = $db->super_query("SELECT tb1.user_id, tb2.user_photo, user_search_pref FROM `".PREFIX."_wall_like` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.rec_id = '{$rid}' ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", 1);
				
				if($sql_){
					$tpl->load_template('profile_subscription_box_top.tpl');
					$tpl->set('[top]', '');
					$tpl->set('[/top]', '');
					$tpl->set('{subcr-num}', 'Понравилось '.$liked_num.' '.gram_record($liked_num, 'like'));
					$tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si","");
					$tpl->compile('content');
					
					$tpl->result['content'] = str_replace('Всего', '', $tpl->result['content']);
					
					$tpl->load_template('profile_friends.tpl');
					foreach($sql_ as $row){
						if($row['user_photo'])
							$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row['user_id'].'/50_'.$row['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						$friend_info_online = explode(' ', $row['user_search_pref']);
						$tpl->set('{user-id}', $row['user_id']);
						$tpl->set('{name}', $friend_info_online[0]);
						$tpl->set('{last-name}', $friend_info_online[1]);
						$tpl->compile('content');
					}
					box_navigation($gcount, $liked_num, $rid, 'wall.all_liked_users', $liked_num);
					
					AjaxTpl();
				}
			}
			die();
		break;
		
		//################### Показ всех комментариев к записи ###################//
		case "all_comm":
			NoAjaxQuery();
			$fast_comm_id = intval($_POST['fast_comm_id']);
			$for_user_id = intval($_POST['for_user_id']);
			if($fast_comm_id AND $for_user_id){
				//Подгружаем и объявляем класс для стены
				include ENGINE_DIR.'/classes/wall.php';
				$wall = new wall();
				
				//Проверка на существование получателя
				$row = $db->super_query("SELECT user_privacy FROM `".PREFIX."_users` WHERE user_id = '{$for_user_id}'");
				if($row){
					//Приватность
					$user_privacy = xfieldsdataload($row['user_privacy']);
					
					//Если приватность "Только друщья" то Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
					if($user_privacy['val_wall3'] == 2 AND $user_id != $for_user_id)
						$check_friend = $db->super_query("SELECT user_id FROM `".PREFIX."_friends` WHERE user_id = '{$user_id}' AND friend_id = '{$for_user_id}' AND subscriptions = 0");
						
					if($user_privacy['val_wall3'] == 1 OR $user_privacy['val_wall3'] == 2 AND $check_friend OR $user_id == $for_user_id){
						$wall->comm_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, tb2.user_photo, user_search_pref, user_last_visit FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '{$fast_comm_id}' ORDER by `add_date` ASC LIMIT 0, 200", '');

						if($_POST['type'] == 1)
							$wall->comm_template('news/news.tpl');
						else if($_POST['type'] == 2)
							$wall->comm_template('wall/one_record.tpl');
						else
							$wall->comm_template('wall/record.tpl');
						$wall->comm_compile('content');
						$wall->comm_select();
					
						AjaxTpl();
					} else
						echo 'err_privacy';
				}
			}
			die();
		break;
		
		//################### Показ предыдущих записей ###################//
		case "page":
			NoAjaxQuery();
			$last_id = intval($_POST['last_id']);
			$for_user_id = intval($_POST['for_user_id']);
			
			//ЧС
			$CheckBlackList = CheckBlackList($for_user_id);
				
			if(!$CheckBlackList AND $for_user_id AND $last_id){
				include ENGINE_DIR.'/classes/wall.php';
				$wall = new wall();
				
				//Проверка на существование получателя
				$row = $db->super_query("SELECT user_privacy FROM `".PREFIX."_users` WHERE user_id = '{$for_user_id}'");
				
				if($row){
					//Приватность
					$user_privacy = xfieldsdataload($row['user_privacy']);

					//Если приватность "Только друщья" то Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
					if($user_privacy['val_wall1'] == 2 AND $user_id != $for_user_id)
						$check_friend = $db->super_query("SELECT user_id FROM `".PREFIX."_friends` WHERE user_id = '{$user_id}' AND friend_id = '{$for_user_id}' AND subscriptions = 0");
							
					if($user_privacy['val_wall1'] == 1 OR $user_privacy['val_wall1'] == 2 AND $check_friend OR $user_id == $for_user_id)
						$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, type, tell_uid, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.id < '{$last_id}' AND for_user_id = '{$for_user_id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '0' ORDER by `add_date` DESC LIMIT 0, {$limit_select}");
					else
						$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, type, tell_uid, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.id < '{$last_id}' AND for_user_id = '{$for_user_id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '0' AND tb1.author_user_id = '{$for_user_id}' ORDER by `add_date` DESC LIMIT 0, {$limit_select}");
					
					$wall->template('wall/record.tpl');
					$wall->compile('content');
					$wall->select();
					AjaxTpl();
				}
			}
			die();
		break;
		
		//################### Рассказать друзьям "Мне нравитсяя" ###################//
		case "tell":
			NoAjaxQuery();
			$rid = intval($_POST['rid']);
			
			//Проверка на существование записи
			$row = $db->super_query("SELECT add_date, text, author_user_id, tell_uid, tell_date, public, attach FROM `".PREFIX."_wall` WHERE fast_comm_id = '0' AND id = '{$rid}'");

			if($row){
				if($row['author_user_id'] != $user_id){
					if($row['tell_uid']){
						$row['add_date'] = $row['tell_date'];
						$row['author_user_id'] = $row['tell_uid'];
					}
						
					//Проверяем на существование этой записи у себя на стене
					$myRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_wall` WHERE tell_uid = '{$row['author_user_id']}' AND tell_date = '{$row['add_date']}' AND author_user_id = '{$user_id}'");
					if(!$myRow['cnt']){
						$row['text'] = $db->safesql($row['text']);
						$row['attach'] = $db->safesql($row['attach']);
						
						//Всталвяем себе на стену
						$db->query("INSERT INTO `".PREFIX."_wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$row['text']}', add_date = '{$server_time}', fast_comm_id = 0, tell_uid = '{$row['author_user_id']}', tell_date = '{$row['add_date']}', public = '{$row['public']}', attach = '{$row['attach']}'");
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
		
		//################### Парсер информации о ссылке ###################//
		case "parse_link":
			$lnk = 'http://'.str_replace('http://', '', trim($_POST['lnk']));
			$check_url = @get_headers(stripslashes($lnk));

			if(strpos($check_url[0], '200')){
				$open_lnk = @file_get_contents($lnk);
				
				if(stripos(strtolower($open_lnk), 'charset=utf-8') OR stripos(strtolower($check_url[2]), 'charset=utf-8'))
					$open_lnk = ajax_utf8($open_lnk);
				else
					$open_lnk = iconv('windows-1251', 'utf-8', $open_lnk);
					
				if(stripos(strtolower($open_lnk), 'charset=KOI8-R'))
					$open_lnk = iconv('KOI8-R', 'utf-8', $open_lnk);

				preg_match("/<meta property=(\"|')og:title(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_title);
				if(!$parse_title[4])
					preg_match("/<meta name=(\"|')title(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_title);

				$res_title = $parse_title[4];
				
				if(!$res_title){
					preg_match_all('`(<title>[^\[]+\</title>)`si', $open_lnk, $parse);
					$res_title = str_replace(array('<title>', '</title>'), '', $parse[1][0]);
				}

				preg_match("/<meta property=(\"|')og:description(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_descr);
				if(!$parse_descr[4])
					preg_match("/<meta name=(\"|')description(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_descr);

				$res_descr = strip_tags($parse_descr[4]);
				$res_title = strip_tags($res_title);
				
				$open_lnk = preg_replace('`(<!--noindex-->|<noindex>).+?(<!--/noindex-->|</noindex>)`si', '', $open_lnk);

				preg_match("/<meta property=(\"|')og:image(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_img);
				if(!$parse_img[4])
					preg_match_all('/<img(.*?)src=\"(.*?)\"/', $open_lnk, $array);
				else
					$array[2][0] = $parse_img[4];

				$res_title = str_replace("|", "&#124;", $res_title);
				$res_descr = str_replace("|", "&#124;", $res_descr);

				$allowed_files = array('jpg', 'jpeg', 'jpe', 'png');
				
				$expImgs = explode('<img', $open_lnk);
				
				if($expImgs[1]){
					
					$i = 0;
					
					foreach($expImgs as $img){
						
						$exp1 = explode('src="', $img);

						$exp2 = explode('/>', $exp1[1]);
						
						$exp3 = explode('"', $exp2[0]);
						
						$expFormat = end(explode('.', $exp3[0]));
						
						if(in_array(strtolower($expFormat), $allowed_files)){
							
							$i++;
							
							$domain_url_name = explode('/', $lnk);
							$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
							
							if(stripos(strtolower($exp3[0]), 'http://') === false)
							
								$new_imgs .= 'http://'.$rdomain_url_name.'/'.$exp3[0].'|';
								
							else
							
								$new_imgs .= $exp3[0].'|';
								
							if($i == 1)
								$img_link = str_replace('|', '', $new_imgs);
						}

					}
					
				}
				
				preg_match("/<meta property=(\"|')og:image(\"|') content=(\"|')(.*?)(\"|')(.*?)>/is", $open_lnk, $parse_img);
				if($parse_img[4]){
					$rIMGx = explode('?', $parse_img[4]);
					$img_link = $rIMGx[0];
					if(!$new_imgs)
						$new_imgs = $img_link;
				}

				echo $res_title.'<f>'.$res_descr.'<f>'.$img_link.'<f>'.$new_imgs;
				
			} else
				echo 1;
			die();
		break;
		
			default:

				//################### Показ последних 10 записей ###################//

				//Если вызвана страница стены, не со страницы юзера
				if(!$id){
					$rid = intval($_GET['rid']);
					
					$id = intval($_GET['uid']);
					if(!$id)
						$id = $user_id;
						
					$walluid = $id;
					$metatags['title'] = $lang['wall_title'];
					$user_speedbar = 'На стене нет записей';

					if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
					$gcount = 10;
					$limit_page = ($page-1)*$gcount;
	
					//Выводим имя юзера и настройки приватности
					$row_user = $db->super_query("SELECT user_name, user_wall_num, user_privacy FROM `".PREFIX."_users` WHERE user_id = '{$id}'");
					$user_privacy = xfieldsdataload($row_user['user_privacy']);

					if($row_user){
						//ЧС
						$CheckBlackList = CheckBlackList($id);
						if(!$CheckBlackList){
							//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
							if($user_id != $id)
								$check_friend = CheckFriends($id);

							if($user_privacy['val_wall1'] == 1 OR $user_privacy['val_wall1'] == 2 AND $check_friend OR $user_id == $id)
								$cnt_rec['cnt'] = $row_user['user_wall_num'];
							else
								$cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
								
							if($_GET['type'] == 'own'){
								$cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
								$where_sql = "AND tb1.author_user_id = '{$id}'";
								$tpl->set_block("'\\[record-tab\\](.*?)\\[/record-tab\\]'si","");
								$page_type = '/wall'.$id.'_sec=own&page=';
							} else if($_GET['type'] == 'record'){
								$where_sql = "AND tb1.id = '{$rid}'";
								$tpl->set('[record-tab]', '');
								$tpl->set('[/record-tab]', '');
								$wallAuthorId = $db->super_query("SELECT author_user_id FROM `".PREFIX."_wall` WHERE id = '{$rid}'");
							} else {
								$_GET['type'] = '';
								$where_sql = '';
								$tpl->set_block("'\\[record-tab\\](.*?)\\[/record-tab\\]'si","");
								$page_type = '/wall'.$id.'/page/';
							}

							if($cnt_rec['cnt'] > 0)
								$user_speedbar = 'На стене '.$cnt_rec['cnt'].' '.gram_record($cnt_rec['cnt'], 'rec');

							$tpl->load_template('wall/head.tpl');
							$tpl->set('{name}', gramatikName($row_user['user_name']));
							$tpl->set('{uid}', $id);
							$tpl->set('{rec-id}', $rid);
							$tpl->set("{activetab-{$_GET['type']}}", 'activetab');
							$tpl->compile('info');
							
							if($cnt_rec['cnt'] < 1)
								msgbox('', $lang['wall_no_rec'], 'info_2');
						} else {
							$user_speedbar = $lang['error'];
							msgbox('', $lang['no_notes'], 'info');
						}
					} else
						msgbox('', $lang['wall_no_rec'], 'info_2');
				}

				if(!$CheckBlackList){
					include ENGINE_DIR.'/classes/wall.php';
					$wall = new wall();
					
						if($user_privacy['val_wall1'] == 1 OR $user_privacy['val_wall1'] == 2 AND $check_friend OR $user_id == $id)
							$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 {$where_sql} ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}");
						elseif($wallAuthorId['author_user_id'] == $id)
							$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 {$where_sql} ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}");
						else {
							$wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 AND tb1.author_user_id = '{$id}' ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}");
							if($wallAuthorId['author_user_id'])
								$Hacking = true;
						}
					//Если вызвана страница стены, не со страницы юзера
					if(!$Hacking){
						if($rid OR $walluid){
							$wall->template('wall/one_record.tpl');
							$wall->compile('content');
							$wall->select();

							if($cnt_rec['cnt'] > $gcount AND $_GET['type'] == '' OR $_GET['type'] == 'own')
								navigation($gcount, $cnt_rec['cnt'], $page_type);
						} else {
							$wall->template('wall/record.tpl');
							$wall->compile('wall');
							$wall->select();
						}
					}
				}
	}
	$tpl->clear();
	$db->free();
} else
	echo 'no_log';
?>