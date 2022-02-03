<?php
/* 
	Appointment: Новости
	File: news.php 
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
	$user_id = $user_info['user_id'];
	$limit_news = 20;
		
			//################### Вывод новостей ###################//
			$type = $_GET['type']; #тип сортировки
			
			//Если вызвана страница обновлений
			if($type == 'updates' OR $type == 'photos' OR $type == 'videos')
				$for_new_sql = "AND subscriptions IN (0,1)";
			
			$sql_where = "tb1.ac_user_id IN (SELECT tb2.friend_id FROM `".PREFIX."_friends` tb2 WHERE user_id = '{$user_id}' {$for_new_sql}) AND";
			
			//Если вызвана страница обновлений
			if($type == 'updates'){
				$metatags['title'] = $lang['news_updates'];
				$user_speedbar = $lang['news_speedbar'];
				$sql_sort = '4,5';
				$no_news = '<br /><br />'.$lang['news_none_updates'].'<br /><br /><br />';
			} 
			//Если вызвана страница фотографий
			else if($type == 'photos'){
				$metatags['title'] = $lang['news_photos'];
				$user_speedbar = $lang['news_speedbar_photos'];
				$sql_sort = '3';
				$no_news = '<br /><br />'.$lang['news_none_photos'].'<br /><br /><br />';
			} 
			//Если вызвана страница видео
			else if($type == 'videos'){
				$metatags['title'] = $lang['news_videos'];
				$user_speedbar = $lang['news_speedbar_videos'];
				$sql_sort = '2';
				$no_news = '<br /><br />'.$lang['news_none_videos'].'<br /><br /><br />';
			} 
			//Если вызвана страница ответов
			else if($type == 'notifications'){
				$metatags['title'] = $lang['news_notifications'];
				$user_speedbar = $lang['news_speedbar_notifications'];
				$mobile_speedbar = 'Последние ответы';
				$sql_sort = '6,7,8,9,10,12';
				$dop_sort = "AND tb1.for_user_id = '{$user_id}'";
				$no_news = '<br /><br />'.$lang['news_none_notifica'].'<br /><br /><br />';
				$sql_where = '';
				
				//Обновляем счетчик новых новостей, ставим 0
				$CacheNews = mozg_cache('user_'.$_SESSION['user_id'].'/new_news');
				if($CacheNews)
					mozg_create_cache('user_'.$user_id.'/new_news', '');
			} else {
				$metatags['title'] = $lang['news_title'];
				$user_speedbar = $lang['news_speedbar'];
				$mobile_speedbar = 'Последние новости';
				$sql_sort = '1,2,3,11';
				$no_news = '<br /><br />'.$lang['news_none'].'<br /><br /><br />';
				$type = '';
				
				$sql_where = "
					tb1.ac_user_id IN (SELECT tb2.friend_id FROM `".PREFIX."_friends` tb2 WHERE user_id = '{$user_id}' AND tb1.action_type IN (1,2,3) AND subscriptions != 2) 
				OR 
					tb1.ac_user_id IN (SELECT tb2.friend_id FROM `".PREFIX."_friends` tb2 WHERE user_id = '{$user_id}' AND tb1.action_type = 11 AND subscriptions = 2) 
				AND";
			}
			
			if($_POST['page_cnt'] > 0)
				$page_cnt = intval($_POST['page_cnt'])*$limit_news;
			else
				$page_cnt = 0;
				
			//Если вызваны предыдущие новости
			if($_POST['page_cnt'])
				$where = "AND ac_id < '{$last_id}'";
			
			//Head
			if(!$_POST['page_cnt']){
				$tpl->load_template('news/head.tpl');
				$tpl->set('[news]', '');
				$tpl->set('[/news]', '');
				$tpl->set("{activetab-{$type}}", 'activetab');
				$tpl->set('{type}', $type);
				$tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si","");
				$tpl->compile('info');
			}
			
			//Запрос на вывод из БД tb1.action_type regexp '[[:<:]]({$sql_sort})[[:>:]]' 
			$sql_ = $db->super_query("
				SELECT 
					tb1.ac_id, ac_user_id, action_text, action_time, action_type, obj_id, answer_text, link
				FROM 
					`".PREFIX."_news` tb1
				WHERE 
					{$sql_where}
					tb1.action_type IN ({$sql_sort})
					{$dop_sort}
				ORDER BY tb1.action_time DESC
				LIMIT {$page_cnt}, {$limit_news}
			", 1);

			if($sql_){
				$c = 0;
				
				//Если страница "ответов" то загружаем шаблон notifications.tpl
				if($type == 'notifications')
					$tpl->load_template('news/notifications.tpl');
				else
					$tpl->load_template('news/news.tpl');
				
				foreach($sql_ as $row){
				
					if($row['action_type'] != 11){
						$rowInfoUser = $db->super_query("SELECT user_search_pref, user_last_visit, user_logged_mobile, user_photo, user_sex, user_privacy FROM `".PREFIX."_users` WHERE user_id = '{$row['ac_user_id']}'");
						$row['user_search_pref'] = $rowInfoUser['user_search_pref'];
						$row['user_last_visit'] = $rowInfoUser['user_last_visit'];
						$row['user_logged_mobile'] = $rowInfoUser['user_logged_mobile'];
						$row['user_photo'] = $rowInfoUser['user_photo'];
						$row['user_sex'] = $rowInfoUser['user_sex'];
						$row['user_privacy'] = $rowInfoUser['user_privacy'];
						$tpl->set('{link}', 'u');
					} else {
						$rowInfoUser = $db->super_query("SELECT title, photo, comments FROM `".PREFIX."_communities` WHERE id = '{$row['ac_user_id']}'");
						$row['user_search_pref'] = $rowInfoUser['title'];
						$tpl->set('{link}', 'public');
					}

					//Выводим данные о том кто инсцинировал действие
					if($row['user_sex'] == 2){
						$sex_text = 'добавила';
						$sex_text_2 = 'ответила';
						$sex_text_3 = 'оценила';
						$sex_text_4 = 'прокомментировала';
					} else {
						$sex_text = 'добавил';
						$sex_text_2 = 'ответил';
						$sex_text_3 = 'оценил';
						$sex_text_4 = 'прокомментировал';
					}
					
					$tpl->set('{author}', $row['user_search_pref']);
					$tpl->set('{author-id}', $row['ac_user_id']);
					OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);

					if($row['action_type'] != 11)
						if($row['user_photo'])
							$tpl->set('{ava}', '/uploads/users/'.$row['ac_user_id'].'/50_'.$row['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
					else
						if($rowInfoUser['photo'])
							$tpl->set('{ava}', '/uploads/groups/'.$row['ac_user_id'].'/50_'.$rowInfoUser['photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');

					//Выводим данные о действии
					megaDate($row['action_time'], 1, 1);
					$tpl->set('{comment}', stripslashes($row['action_text']));
					$tpl->set('{news-id}', $row['ac_id']);

					$tpl->set('{action-type-updates}', '');
					$tpl->set('{action-type}', '');
					
					$expFriensList = explode('||', $row['action_text']);
					$action_cnt = 0;
					
					//Если видео
					if($row['action_type'] == 2){
						if($expFriensList){
							foreach($expFriensList as $ac_id){
								$row_action = explode('|', $ac_id);
								if(file_exists(ROOT_DIR.$row_action[1])){
									$comment .= "<a href=\"/video{$row['ac_user_id']}_{$row_action[0]}_sec=news\" onClick=\"videos.show({$row_action[0]}, this.href, '/news/videos'); return false\"><img src=\"{$row_action[1]}\" style=\"margin-right:5px\" /></a>";
									$action_cnt++;
								}
							}
							$tpl->set('{action-type}', $action_cnt.' '.gram_record($action_cnt, 'videos').', ');
							$tpl->set('{comment}', $comment);
							$comment = '';
						}
					//Если фотография
					} else if($row['action_type'] == 3){
						if($expFriensList){
							foreach($expFriensList as $ac_id){
								$row_action = explode('|', $ac_id);
								if(file_exists(ROOT_DIR.$row_action[1])){
									$comment .= "<a href=\"/photo{$row['ac_user_id']}_{$row_action[0]}_sec=news\" onClick=\"Photo.Show(this.href); return false\"><img src=\"{$row_action[1]}\" style=\"margin-right:5px\" /></a>";
									$action_cnt++;
								}
							}
							$tpl->set('{action-type}', $action_cnt.' '.gram_record($action_cnt, 'photos').', ');
							$tpl->set('{comment}', $comment);
							$comment = '';
						}
					//Если новый друг(ья)
					} else if($row['action_type'] == 4){
						$newfriends = '';
						if($expFriensList){
							foreach($expFriensList as $fr_id){
								$fr_info = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$fr_id}'");
								if($fr_info){
									if($fr_info['user_photo'])
										$ava = "/uploads/users/{$fr_id}/100_{$fr_info['user_photo']}";
									else
										$ava = '{theme}/images/100_no_ava.png';
										
									$newfriends .= "<div class=\"newsnewfriend\"><a href=\"/u{$fr_id}\" onClick=\"Page.Go(this.href); return false\"><img src=\"{$ava}\" alt=\"\" />{$fr_info['user_search_pref']}</a></div>";
									
									$action_cnt++;
								}
							}
							$newfriends .= '<div class="clear"></div>';
							
							$tpl->set('{action-type-updates}', $sex_text.' в друзья '.$action_cnt.' '.gram_record($action_cnt, 'updates').'.');
							$tpl->set('{action-type}', '');
							$tpl->set('{comment}', $newfriends);
						}
					}
					//Если новая заметка(и)
					else if($row['action_type'] == 5){
						if($expFriensList){
							foreach($expFriensList as $nt_id){
								$note_info = $db->super_query("SELECT title FROM `".PREFIX."_notes` WHERE id = '{$nt_id}'");
								if($note_info){
									$newnotes .= '<a href="/notes/view/'.$nt_id.'" onClick="Page.Go(this.href); return false" class="news_ic_note">'.stripslashes($note_info['title']).'</a>';

									$action_cnt++;
								}
							}
							
							$type_updates = $action_cnt == 1 ? $type_updates = 'новую заметку' : $action_cnt.' '.gram_record($action_cnt, 'notes');

							$tpl->set('{action-type-updates}', $sex_text.' '.$type_updates.'.');
							$tpl->set('{action-type}', '');
							$tpl->set('{comment}', $newnotes);
							$newnotes = '';
						}
					}
					
					//Если страница ответов "стена"
					else if($row['action_type'] == 6){
						
						//Выводим текст на который ответил юзер
						if(!$row['answer_text'])
							$row_info = $db->super_query("SELECT id, author_user_id, for_user_id, text, add_date, tell_uid, tell_date, type, public, attach, tell_comm, fast_comm_id FROM `".PREFIX."_wall` WHERE id = '{$row['obj_id']}'");
							
						if($row_info OR $row['answer_text']){
							
							if($row['answer_text'])
								$row_info['text'] = $row['answer_text'];
								
							$str_text = substr(strip_tags($row_info['text']), 0, 70);

							//Прикрипленные файлы
							if($row_info['attach']){
								$attach_arr = explode('||', $row_info['attach']);
								$cnt_attach = 1;
								$cnt_attach_link = 1;
								$jid = 0;
								$attach_result = '';
								$attach_result .= '<div class="clear"></div>';
								foreach($attach_arr as $attach_file){
									$attach_type = explode('|', $attach_file);
									
									//Фото со стены сообщества
									if($attach_type[0] == 'photo' AND file_exists(ROOT_DIR."/uploads/groups/{$row_info['tell_uid']}/photos/c_{$attach_type[1]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info['id']}\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '{$row_info['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\"><img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row_info['tell_uid']}/photos/{$attach_type[1]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row_info['tell_uid']}/photos/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '{$row_info['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info['id']}\" />";
										
										$cnt_attach++;
										
										$resLinkTitle = '';
									//Фото со стены юзера
									} elseif($attach_type[0] == 'photo_u'){
										if($row_info['tell_uid']) $attauthor_user_id = $row_info['tell_uid'];
										else $attauthor_user_id = $row_info['author_user_id'];

										if($attach_type[1] == 'attach' AND file_exists(ROOT_DIR."/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")){
											if($cnt_attach < 2)
												$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info['id']}\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/{$attach_type[2]}\" align=\"left\" /></div>";
											else
												$attach_result .= "<img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info['id']}\" />";
												
											$cnt_attach++;
										} elseif(file_exists(ROOT_DIR."/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")){
											if($cnt_attach < 2)
												$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info['id']}\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/{$attach_type[1]}\" align=\"left\" /></div>";
											else
												$attach_result .= "<img id=\"photo_wall_{$row_info['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info['id']}', '{$row_info['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info['id']}\" />";
												
											$cnt_attach++;
										}
										
										$resLinkTitle = '';
									//Видео
									} elseif($attach_type[0] == 'video' AND file_exists(ROOT_DIR."/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")){
									
										$for_cnt_attach_video = explode('video|', $row_info['attach']);
										$cnt_attach_video = count($for_cnt_attach_video)-1;
								
										if($cnt_attach_video == 1 AND preg_match('/(photo|photo_u)/i', $row_info['attach']) == false){
											
											$video_id = intval($attach_type[2]);
											
											$row_video = $db->super_query("SELECT video, title FROM `".PREFIX."_videos` WHERE id = '{$video_id}'", false, "wall/video{$video_id}");
											$row_video['title'] = stripslashes($row_video['title']);
											$row_video['video'] = stripslashes($row_video['video']);
											$row_video['video'] = strtr($row_video['video'], array('width="770"' => 'width="390"', 'height="420"' => 'height="310"'));
											
											$attach_result .= "<div class=\"cursor_pointer clear\" id=\"no_video_frame{$video_id}\" onClick=\"$('#'+this.id).hide();$('#video_frame{$video_id}').show();\">
											<div class=\"video_inline_icon\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px\" width=\"390\" height=\"310\" /></div><div id=\"video_frame{$video_id}\" class=\"no_display\" style=\"padding-top:3px\">{$row_video['video']}</div><div class=\"video_inline_vititle\"></div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><b>{$row_video['title']}</b></a>";
										
										} else {
											
											$attach_result .= "<div class=\"fl_l\"><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><div class=\"video_inline_icon video_inline_icon2\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" /></a></div>";
											
										}

										$resLinkTitle = '';
										
									//Музыка
									} elseif($attach_type[0] == 'audio'){
										$audioId = intval($attach_type[1]);
										$audioInfo = $db->super_query("SELECT artist, name, url FROM `".PREFIX."_audio` WHERE aid = '".$audioId."'");
										if($audioInfo){
											if($_GET['uid']) $appClassWidth = 'player_mini_mbar_wall_all';
											$jid++;
											$attach_result .= '<div class="audioForSize'.$row_info['id'].' '.$appClassWidth.'" id="audioForSize"><div class="audio_onetrack audio_wall_onemus"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\''.$jid.'\', '.$row_info['id'].')" id="icPlay_'.$row_info['id'].$jid.'"></div><div id="music_'.$row_info['id'].$jid.'" data="'.$audioInfo['url'].'" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query='.$audioInfo['artist'].'&n=1" onClick="Page.Go(this.href); return false"><b>'.stripslashes($audioInfo['artist']).'</b></a> &ndash; '.stripslashes($audioInfo['name']).'</div><div id="play_time'.$row_info['id'].$jid.'" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall '.$appClassWidth.'" id="ppbarPro'.$row_info['id'].$jid.'"></div></div></div>';
										}
										
										$resLinkTitle = '';
									//Смайлик
									} elseif($attach_type[0] == 'smile' AND file_exists(ROOT_DIR."/uploads/smiles/{$attach_type[1]}")){
										$attach_result .= '<img src=\"/uploads/smiles/'.$attach_type[1].'\" style="margin-right:5px" />';

										$resLinkTitle = '';
										
									//Если ссылка
									} elseif($attach_type[0] == 'link' AND preg_match('/http:\/\/(.*?)+$/i', $attach_type[1]) AND $cnt_attach_link == 1 AND stripos(str_replace('http://www.', 'http://', $attach_type[1]), $config['home_url']) === false){
										$count_num = count($attach_type);
										$domain_url_name = explode('/', $attach_type[1]);
										$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
										
										$attach_type[3] = stripslashes($attach_type[3]);
										$attach_type[3] = substr($attach_type[3], 0, 200);
											
										$attach_type[2] = stripslashes($attach_type[2]);
										$str_title = substr($attach_type[2], 0, 55);
										
										if(stripos($attach_type[4], '/uploads/attach/') === false){
											$attach_type[4] = '{theme}/images/no_ava_groups_100.gif';
											$no_img = false;
										} else
											$no_img = true;
										
										if(!$attach_type[3]) $attach_type[3] = '';
											
										if($no_img AND $attach_type[2]){
											if($row_info['tell_comm']) $no_border_link = 'border:0px';
											
											$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="'.$no_border_link.'"><a href="/away.php?url='.$attach_type[1].'" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="'.$attach_type[4].'" /></div></a><div class="attatch_link_title"><a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$str_title.'</a></div><div style="max-height:50px;overflow:hidden">'.$attach_type[3].'</div></div></div>';

											$resLinkTitle = $attach_type[2];
											$resLinkUrl = $attach_type[1];
										} else if($attach_type[1] AND $attach_type[2]){
											$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div></div><div class="clear"></div>';
											
											$resLinkTitle = $attach_type[2];
											$resLinkUrl = $attach_type[1];
										}
										
										$cnt_attach_link++;
										
									//Если документ
									} elseif($attach_type[0] == 'doc'){
									
										$doc_id = intval($attach_type[1]);
										
										$row_doc = $db->super_query("SELECT dname, dsize FROM `".PREFIX."_doc` WHERE did = '{$doc_id}'");
										
										if($row_doc){
											
											$attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did='.$doc_id.'" target="_blank" onMouseOver="myhtml.title(\''.$doc_id.$cnt_attach.$row_info['id'].'\', \'<b>Размер файла: '.$row_doc['dsize'].'</b>\', \'doc_\')" id="doc_'.$doc_id.$cnt_attach.$row_info['id'].'">'.$row_doc['dname'].'</a></div></div></div><div class="clear"></div>';
												
											$cnt_attach++;
										}
										
									//Если опрос
									} elseif($attach_type[0] == 'vote'){
									
										$vote_id = intval($attach_type[1]);
										
										$row_vote = $db->super_query("SELECT title, answers, answer_num FROM `".PREFIX."_votes` WHERE id = '{$vote_id}'", false, "votes/vote_{$vote_id}");
										
										if($vote_id){

											$checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false, "votes/check{$user_id}_{$vote_id}");
											
											$row_vote['title'] = stripslashes($row_vote['title']);
											
											if(!$row_info['text'])
												$row_info['text'] = $row_vote['title'];

											$arr_answe_list = explode('|', stripslashes($row_vote['answers']));
											$max = $row_vote['answer_num'];
											
											$sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", 1, "votes/vote_answer_cnt_{$vote_id}");
											$answer = array();
											foreach($sql_answer as $row_answer){
											
												$answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
												
											}
											
											$attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
											
											for($ai = 0; $ai < sizeof($arr_answe_list); $ai++){

												if(!$checkMyVote['cnt']){
												
													$attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
												
												} else {

													$num = $answer[$ai]['cnt'];

													if(!$num ) $num = 0;
													if($max != 0) $proc = (100 * $num) / $max;
													else $proc = 0;
													$proc = round($proc, 2);
													
													$attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
													{$arr_answe_list[$ai]}<br />
													<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:".intval($proc)."%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
													<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
													</div><div class=\"clear\"></div>";
							
												}
											
											}
											
											if($row_vote['answer_num']) $answer_num_text = gram_record($row_vote['answer_num'], 'fave');
											else $answer_num_text = 'человек';
											
											if($row_vote['answer_num'] <= 1) $answer_text2 = 'Проголосовал';
											else $answer_text2 = 'Проголосовало';
												
											$attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
											
										}
										
									} else
									
										$attach_result .= '';
										
								}

								if($resLinkTitle AND $row_info['text'] == $resLinkUrl OR !$row_info['text'])
									$row_info['text'] = $resLinkTitle.$attach_result;
								else if($attach_result)
									$row_info['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info['text']).$attach_result;
								else
									$row_info['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info['text']);
							} else
								$row_info['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info['text']);
								
							$resLinkTitle = '';
							
							//Если это запись с "рассказать друзьям"
							if($row_info['tell_uid']){
								if($row_info['public'])
									$rowUserTell = $db->super_query("SELECT title, photo FROM `".PREFIX."_communities` WHERE id = '{$row_info['tell_uid']}'", false, "wall/group{$row_info['tell_uid']}");
								else
									$rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$row_info['tell_uid']}'");

								if(date('Y-m-d', $row_info['tell_date']) == date('Y-m-d', $server_time))
									$dateTell = langdate('сегодня в H:i', $row_info['tell_date']);
								elseif(date('Y-m-d', $row_info['tell_date']) == date('Y-m-d', ($server_time-84600)))
									$dateTell = langdate('вчера в H:i', $row_info['tell_date']);
								else
									$dateTell = langdate('j F Y в H:i', $row_info['tell_date']);
								
								if($row_info['public']){
									$rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
									$tell_link = 'public';
									if($rowUserTell['photo'])
										$avaTell = '/uploads/groups/'.$row_info['tell_uid'].'/50_'.$rowUserTell['photo'];
									else
										$avaTell = '{theme}/images/no_ava_50.png';
								} else {
									$tell_link = 'u';
									if($rowUserTell['user_photo'])
										$avaTell = '/uploads/users/'.$row_info['tell_uid'].'/50_'.$rowUserTell['user_photo'];
									else
										$avaTell = '{theme}/images/no_ava_50.png';
								}

								if($row_info['tell_comm']) $border_tell_class = 'wall_repost_border'; else $border_tell_class = 'wall_repost_border2';

								$row_info['text'] = <<<HTML
{$row_info['tell_comm']}
<div class="{$border_tell_class}" style="margin-top:-5px">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$row_info['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$row_info['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row_info['text']}<div class="clear"></div>
</div>
HTML;
							}
							
							$tpl->set('{wall-text}', stripslashes($row_info['text']));

							if(!$str_text){
								if(date('Y-m-d', $row_info['add_date']) == date('Y-m-d', $server_time))
									$nDate = langdate('сегодня в H:i', $row_info['add_date']);
								elseif(date('Y-m-d', $row_info['add_date']) == date('Y-m-d', ($server_time-84600)))
									$nDate = langdate('вчера в H:i', $row_info['add_date']);
								else
									$nDate = langdate('j F Y в H:i', $row_info['add_date']);
									
								$str_text = 'от '.$nDate;
								
							}
							
							if(strlen($str_text) == 70)
								$tocheks = '...';
							
							//Если это ответ на комментарий
							if($row_info['fast_comm_id'] OR $row['answer_text']){
							
								$act_type_wall_txt = 'Ваш комментарий';
								$row['obj_id'] = $row_info['fast_comm_id'];
								
							} else
								$act_type_wall_txt = 'Вашу запись';
							
							if($row['answer_text'] AND $row['link'])
								$tpl->set('{action-type}', $sex_text_2.' на '.$act_type_wall_txt.' <a href="'.$row['link'].'" onClick="Page.Go(this.href); return false" onMouseOver="news.showWallText('.$row['ac_id'].')" onMouseOut="news.hideWallText('.$row['ac_id'].')"><span id="2href_text_'.$row['ac_id'].'">'.$str_text.'</span></a>'.$tocheks);
							else
								$tpl->set('{action-type}', $sex_text_2.' на '.$act_type_wall_txt.' <a href="/wall'.$row_info['for_user_id'].'_'.$row['obj_id'].'" onMouseOver="news.showWallText('.$row['ac_id'].')" onMouseOut="news.hideWallText('.$row['ac_id'].')" onClick="Page.Go(this.href); return false"><span id="2href_text_'.$row['ac_id'].'">'.$str_text.'</span></a>'.$tocheks);
								
							$tocheks = '';

							$tpl->set('[like]', '');
							$tpl->set('[/like]', '');
							$tpl->set_block("'\\[no-like\\](.*?)\\[/no-like\\]'si","");
							$tpl->set_block("'\\[action\\](.*?)\\[/action\\]'si","");
							$action_cnt = 1;
						}
					}
					
					//Если страница ответов "мне нравится"
					else if($row['action_type'] == 7){
						
						//Выводим текст на который ответил юзер
						$row_info_likes = $db->super_query("SELECT id, author_user_id, for_user_id, text, add_date, tell_uid, tell_date, type, public, attach, tell_comm FROM `".PREFIX."_wall` WHERE id = '{$row['obj_id']}'");
						if($row_info_likes){
							$str_text_likes = strip_tags(substr($row_info_likes['text'], 0, 70));

							//Прикрипленные файлы
							if($row_info_likes['attach']){
								$attach_arr = explode('||', $row_info_likes['attach']);
								$cnt_attach = 1;
								$cnt_attach_link = 1;
								$jid = 0;
								$attach_result = '';
								$attach_result .= '<div class="clear"></div>';
								foreach($attach_arr as $attach_file){
									$attach_type = explode('|', $attach_file);
									
									//Фото со стены сообщества
									if($attach_type[0] == 'photo' AND file_exists(ROOT_DIR."/uploads/groups/{$row_info_likes['tell_uid']}/photos/c_{$attach_type[1]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info_likes['id']}\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '{$row_info_likes['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\"><img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row_info_likes['tell_uid']}/photos/{$attach_type[1]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row_info_likes['tell_uid']}/photos/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '{$row_info_likes['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info_likes['id']}\" />";
										
										$cnt_attach++;
										
										$resLinkTitle = '';
									//Фото со стены юзера
									} elseif($attach_type[0] == 'photo_u'){
										if($row_info_likes['tell_uid']) $attauthor_user_id = $row_info_likes['tell_uid'];
										else $attauthor_user_id = $row_info_likes['author_user_id'];

										if($attach_type[1] == 'attach' AND file_exists(ROOT_DIR."/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")){
											if($cnt_attach < 2)
												$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info_likes['id']}\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/{$attach_type[2]}\" align=\"left\" /></div>";
											else
												$attach_result .= "<img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info_likes['id']}\" />";
												
											$cnt_attach++;
										} elseif(file_exists(ROOT_DIR."/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")){
											if($cnt_attach < 2)
												$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_info_likes['id']}\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/{$attach_type[1]}\" align=\"left\" /></div>";
											else
												$attach_result .= "<img id=\"photo_wall_{$row_info_likes['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_info_likes['id']}', '{$row_info_likes['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_info_likes['id']}\" />";
												
											$cnt_attach++;
										}
										
										$resLinkTitle = '';
									//Видео
									} elseif($attach_type[0] == 'video' AND file_exists(ROOT_DIR."/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")){
									
										$for_cnt_attach_video = explode('video|', $row_info_likes['attach']);
										$cnt_attach_video = count($for_cnt_attach_video)-1;
								
										if($cnt_attach_video == 1 AND preg_match('/(photo|photo_u)/i', $row_info_likes['attach']) == false){
											
											$video_id = intval($attach_type[2]);
											
											$row_video = $db->super_query("SELECT video, title FROM `".PREFIX."_videos` WHERE id = '{$video_id}'", false, "wall/video{$video_id}");
											$row_video['title'] = stripslashes($row_video['title']);
											$row_video['video'] = stripslashes($row_video['video']);
											$row_video['video'] = strtr($row_video['video'], array('width="770"' => 'width="390"', 'height="420"' => 'height="310"'));
											
											$attach_result .= "<div class=\"cursor_pointer clear\" id=\"no_video_frame{$video_id}\" onClick=\"$('#'+this.id).hide();$('#video_frame{$video_id}').show();\">
											<div class=\"video_inline_icon\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px\" width=\"390\" height=\"310\" /></div><div id=\"video_frame{$video_id}\" class=\"no_display\" style=\"padding-top:3px\">{$row_video['video']}</div><div class=\"video_inline_vititle\"></div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><b>{$row_video['title']}</b></a>";
										
										} else {
											
											$attach_result .= "<div class=\"fl_l\"><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><div class=\"video_inline_icon video_inline_icon2\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" /></a></div>";
											
										}

										$resLinkTitle = '';
										
									//Музыка
									} elseif($attach_type[0] == 'audio'){
										$audioId = intval($attach_type[1]);
										$audioInfo = $db->super_query("SELECT artist, name, url FROM `".PREFIX."_audio` WHERE aid = '".$audioId."'");
										if($audioInfo){
											if($_GET['uid']) $appClassWidth = 'player_mini_mbar_wall_all';
											$jid++;
											$attach_result .= '<div class="audioForSize'.$row_info_likes['id'].' '.$appClassWidth.'" id="audioForSize"><div class="audio_onetrack audio_wall_onemus"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\''.$jid.'\', '.$row_info_likes['id'].')" id="icPlay_'.$row_info_likes['id'].$jid.'"></div><div id="music_'.$row_info_likes['id'].$jid.'" data="'.$audioInfo['url'].'" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query='.$audioInfo['artist'].'&n=1" onClick="Page.Go(this.href); return false"><b>'.stripslashes($audioInfo['artist']).'</b></a> &ndash; '.stripslashes($audioInfo['name']).'</div><div id="play_time'.$row_info_likes['id'].$jid.'" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall '.$appClassWidth.'" id="ppbarPro'.$row_info_likes['id'].$jid.'"></div></div></div>';
										}
										
										$resLinkTitle = '';
									//Смайлик
									} elseif($attach_type[0] == 'smile' AND file_exists(ROOT_DIR."/uploads/smiles/{$attach_type[1]}")){
										$attach_result .= '<img src=\"/uploads/smiles/'.$attach_type[1].'\" style="margin-right:5px" />';

										$resLinkTitle = '';
										
									//Если ссылка
									} elseif($attach_type[0] == 'link' AND preg_match('/http:\/\/(.*?)+$/i', $attach_type[1]) AND $cnt_attach_link == 1 AND stripos(str_replace('http://www.', 'http://', $attach_type[1]), $config['home_url']) === false){
										$count_num = count($attach_type);
										$domain_url_name = explode('/', $attach_type[1]);
										$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
										
										$attach_type[3] = stripslashes($attach_type[3]);
										$attach_type[3] = substr($attach_type[3], 0, 200);
											
										$attach_type[2] = stripslashes($attach_type[2]);
										$str_title = substr($attach_type[2], 0, 55);
										
										if(stripos($attach_type[4], '/uploads/attach/') === false){
											$attach_type[4] = '{theme}/images/no_ava_groups_100.gif';
											$no_img = false;
										} else
											$no_img = true;
										
										if(!$attach_type[3]) $attach_type[3] = '';
											
										if($no_img AND $attach_type[2]){
											if($row_info_likes['tell_comm']) $no_border_link = 'border:0px';
											
											$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="'.$no_border_link.'"><a href="/away.php?url='.$attach_type[1].'" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="'.$attach_type[4].'" /></div></a><div class="attatch_link_title"><a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$str_title.'</a></div><div style="max-height:50px;overflow:hidden">'.$attach_type[3].'</div></div></div>';

											$resLinkTitle = $attach_type[2];
											$resLinkUrl = $attach_type[1];
										} else if($attach_type[1] AND $attach_type[2]){
											$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div></div><div class="clear"></div>';
											
											$resLinkTitle = $attach_type[2];
											$resLinkUrl = $attach_type[1];
										}
										
										$cnt_attach_link++;
										
									//Если документ
									} elseif($attach_type[0] == 'doc'){
									
										$doc_id = intval($attach_type[1]);
										
										$row_doc = $db->super_query("SELECT dname, dsize FROM `".PREFIX."_doc` WHERE did = '{$doc_id}'");
										
										if($row_doc){
											
											$attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did='.$doc_id.'" target="_blank" onMouseOver="myhtml.title(\''.$doc_id.$cnt_attach.$row_info_likes['id'].'\', \'<b>Размер файла: '.$row_doc['dsize'].'</b>\', \'doc_\')" id="doc_'.$doc_id.$cnt_attach.$row_info_likes['id'].'">'.$row_doc['dname'].'</a></div></div></div><div class="clear"></div>';
												
											$cnt_attach++;
										}
										
									//Если опрос
									} elseif($attach_type[0] == 'vote'){
									
										$vote_id = intval($attach_type[1]);
										
										$row_vote = $db->super_query("SELECT title, answers, answer_num FROM `".PREFIX."_votes` WHERE id = '{$vote_id}'", false, "votes/vote_{$vote_id}");
										
										if($vote_id){

											$checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false, "votes/check{$user_id}_{$vote_id}");
											
											$row_vote['title'] = stripslashes($row_vote['title']);
											
											if(!$row_info_likes['text'])
												$row_info_likes['text'] = $row_vote['title'];

											$arr_answe_list = explode('|', stripslashes($row_vote['answers']));
											$max = $row_vote['answer_num'];
											
											$sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", 1, "votes/vote_answer_cnt_{$vote_id}");
											$answer = array();
											foreach($sql_answer as $row_answer){
											
												$answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
												
											}
											
											$attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
											
											for($ai = 0; $ai < sizeof($arr_answe_list); $ai++){

												if(!$checkMyVote['cnt']){
												
													$attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
												
												} else {

													$num = $answer[$ai]['cnt'];

													if(!$num ) $num = 0;
													if($max != 0) $proc = (100 * $num) / $max;
													else $proc = 0;
													$proc = round($proc, 2);
													
													$attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
													{$arr_answe_list[$ai]}<br />
													<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:".intval($proc)."%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
													<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
													</div><div class=\"clear\"></div>";
							
												}
											
											}
											
											if($row_vote['answer_num']) $answer_num_text = gram_record($row_vote['answer_num'], 'fave');
											else $answer_num_text = 'человек';
											
											if($row_vote['answer_num'] <= 1) $answer_text2 = 'Проголосовал';
											else $answer_text2 = 'Проголосовало';
												
											$attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
											
										}
										
									} else
									
										$attach_result .= '';
										
								}

								if($resLinkTitle AND $row_info_likes['text'] == $resLinkUrl OR !$row_info_likes['text'])
									$row_info_likes['text'] = $resLinkTitle.$attach_result;
								else if($attach_result)
									$row_info_likes['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info_likes['text']).$attach_result;
								else
									$row_info_likes['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info_likes['text']);
							} else
								$row_info_likes['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_info_likes['text']);
								
							$resLinkTitle = '';
							
							//Если это запись с "рассказать друзьям"
							if($row_info_likes['tell_uid']){
								if($row_info_likes['public'])
									$rowUserTell = $db->super_query("SELECT title, photo FROM `".PREFIX."_communities` WHERE id = '{$row_info_likes['tell_uid']}'", false, "wall/group{$row_info_likes['tell_uid']}");
								else
									$rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$row_info_likes['tell_uid']}'");

								if(date('Y-m-d', $row_info_likes['tell_date']) == date('Y-m-d', $server_time))
									$dateTell = langdate('сегодня в H:i', $row_info_likes['tell_date']);
								elseif(date('Y-m-d', $row_info_likes['tell_date']) == date('Y-m-d', ($server_time-84600)))
									$dateTell = langdate('вчера в H:i', $row_info_likes['tell_date']);
								else
									$dateTell = langdate('j F Y в H:i', $row_info_likes['tell_date']);
								
								if($row_info_likes['public']){
									$rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
									$tell_link = 'public';
									if($rowUserTell['photo'])
										$avaTell = '/uploads/groups/'.$row_info_likes['tell_uid'].'/50_'.$rowUserTell['photo'];
									else
										$avaTell = '{theme}/images/no_ava_50.png';
								} else {
									$tell_link = 'u';
									if($rowUserTell['user_photo'])
										$avaTell = '/uploads/users/'.$row_info_likes['tell_uid'].'/50_'.$rowUserTell['user_photo'];
									else
										$avaTell = '{theme}/images/no_ava_50.png';
								}

								if($row_info_likes['tell_comm']) $border_tell_class = 'wall_repost_border'; else $border_tell_class = 'wall_repost_border2';

								$row_info_likes['text'] = <<<HTML
{$row_info_likes['tell_comm']}
<div class="{$border_tell_class}" style="margin-top:-5px">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$row_info_likes['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$row_info_likes['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row_info_likes['text']}<div class="clear"></div>
</div>
HTML;
							}
							
							$tpl->set('{wall-text}', stripslashes($row_info_likes['text']));
							
							if(!$str_text_likes){
								if(date('Y-m-d', $row_info_likes['add_date']) == date('Y-m-d', $server_time))
									$nDate = langdate('сегодня в H:i', $row_info_likes['add_date']);
								elseif(date('Y-m-d', $row_info_likes['add_date']) == date('Y-m-d', ($server_time-84600)))
									$nDate = langdate('вчера в H:i', $row_info_likes['add_date']);
								else
									$nDate = langdate('j F Y в H:i', $row_info_likes['add_date']);
									
								$str_text_likes = 'от '.$nDate;
							}
							
							$likesUseList = explode('|', str_replace('u', '', $row['action_text']));
							$rList = '';
							$uNames = '';
							$cntUse = 0;
							foreach($likesUseList as $likeUser){
								if($likeUser){
									$rowUser = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$likeUser}'");
									if($rowUser['user_photo'])
										$luAva = '/uploads/users/'.$likeUser.'/50_'.$rowUser['user_photo'];
									else
										$luAva = '{theme}/images/no_ava_50.png';
									$rList .= '<a href="/u'.$likeUser.'" onClick="Page.Go(this.href); return false"><img src="'.$luAva.'" width="32" style="margin-right:5px;margin-top:3px" /></a>';
									$uNames .= '<a href="/u'.$likeUser.'" onClick="Page.Go(this.href); return false">'.$rowUser['user_search_pref'].'</a>, ';
									$cntUse++;
								}
							}
							$uNames = substr($uNames, 0, (strlen($uNames)-2));
							$tpl->set('{comment}', $rList);
							$tpl->set('{author}', $uNames);

							if($cntUse == 1) 
								$sex_text = $sex_text_3;
							else 
								$sex_text = '<b>'.$cntUse.'</b> '.gram_record($cntUse, 'fave').' оценили';
								
							if(strlen($str_text_likes) == 70)
								$tocheks = '...';
							$tpl->set('{action-type}', $sex_text.' Вашу запись <a href="/wall'.$row_info_likes['for_user_id'].'_'.$row['obj_id'].'" onMouseOver="news.showWallText('.$row['ac_id'].')" onMouseOut="news.hideWallText('.$row['ac_id'].')" onClick="Page.Go(this.href); return false"><span id="2href_text_'.$row['ac_id'].'">'.$str_text_likes.'</span></a>'.$tocheks);
							$tocheks = '';

							$tpl->set('[no-like]', '');
							$tpl->set('[/no-like]', '');
							$tpl->set_block("'\\[like\\](.*?)\\[/like\\]'si","");
							$tpl->set_block("'\\[action\\](.*?)\\[/action\\]'si","");
							$action_cnt = 1;
						} else
							$db->query("DELETE FROM `".PREFIX."_news` WHERE ac_id = '{$row['ac_id']}'");
					}
					
					//Если страница ответов "комменатрий к фотографии"
					else if($row['action_type'] == 8 OR $row['action_type'] == 12){
						$photo_info = explode('|', $row['action_text']);
						if(file_exists(ROOT_DIR.'/uploads/users/'.$user_id.'/albums/'.$photo_info[3].'/c_'.$photo_info[1])){
							$tpl->set('{comment}', stripslashes($photo_info[0])); 
							if($row['action_type'] == 12) $sex_text_5 = $sex_text_3;
							else $sex_text_5 = $sex_text_4;
							$tpl->set('{action-type}', $sex_text_5.' Вашу <a href="/photo'.$user_id.'_'.$photo_info[2].'_sec=news" onClick="Photo.Show(this.href); return false">фотографию</a>');
							$tpl->set('{act-photo}', '/uploads/users/'.$user_id.'/albums/'.$photo_info[3].'/c_'.$photo_info[1]);
							$tpl->set('{user-id}', $user_id);
							$tpl->set('{ac-id}', $photo_info[2]);
							$tpl->set('{type-name}', 'photo');
							$tpl->set('{function}', 'Photo.Show(this.href)');
							$tpl->set('[like]', '');
							$tpl->set('[/like]', '');
							$tpl->set('[action]', '');
							$tpl->set('[/action]', '');
							$tpl->set_block("'\\[no-like\\](.*?)\\[/no-like\\]'si","");
							$action_cnt = 1;
						} else
							$db->query("DELETE FROM `".PREFIX."_news` WHERE ac_id = '{$row['ac_id']}'");
					}
					
					//Если страница ответов "комменатрий к видеозаписи"
					else if($row['action_type'] == 9){
						$photo_info = explode('|', $row['action_text']);
						if(file_exists(ROOT_DIR.$photo_info[1])){
							$tpl->set('{comment}', stripslashes($photo_info[0])); 
							$tpl->set('{action-type}', $sex_text_4.' Вашу <a href="/video'.$user_id.'_'.$photo_info[2].'_sec=news" onClick="videos.show('.$photo_info[2].', this.href); return false">видеозапись</a>');
							$tpl->set('{act-photo}', $photo_info[1]);
							$tpl->set('{user-id}', $user_id);
							$tpl->set('{ac-id}', $photo_info[2]);
							$tpl->set('{type-name}', 'video');
							$tpl->set('{function}', "videos.show({$photo_info[2]}, this.href, '/news/notifications')");
							$tpl->set('[like]', '');
							$tpl->set('[/like]', '');
							$tpl->set('[action]', '');
							$tpl->set('[/action]', '');
							$tpl->set_block("'\\[no-like\\](.*?)\\[/no-like\\]'si","");
							$action_cnt = 1;
						} else
							$db->query("DELETE FROM `".PREFIX."_news` WHERE ac_id = '{$row['ac_id']}'");
					}
					
					//Если страница ответов "комменатрий к заметке"
					else if($row['action_type'] == 10){
						$note_info = explode('|', $row['action_text']);
						$row_note = $db->super_query("SELECT title FROM `".PREFIX."_notes` WHERE id = '{$note_info[1]}'");
						if($row_note){
							$tpl->set('{comment}', stripslashes($note_info[0])); 
							$tpl->set('{action-type}', $sex_text_4.' Вашу заметку <a href="/notes/view/'.$note_info[1].'" onClick="Page.Go(this.href); return false">'.$row_note['title'].'</a>');
							$tpl->set('[like]', '');
							$tpl->set('[/like]', '');
							$tpl->set_block("'\\[no-like\\](.*?)\\[/no-like\\]'si","");
							$tpl->set_block("'\\[action\\](.*?)\\[/action\\]'si","");
							$action_cnt = 1;
						} else
							$db->query("DELETE FROM `".PREFIX."_news` WHERE ac_id = '{$row['ac_id']}'");
					} else {
						//пустой ответ
						echo '';
					}
					
					$c++;

					//Если запись со стены
					if($row['action_type'] == 1){
						
						//Приватность
						$user_privacy = xfieldsdataload($row['user_privacy']);
						$check_friend = CheckFriends($row['ac_user_id']);
						
						//Выводим кол-во комментов, мне нравится, и список юзеров кто поставил лайки к записи если это не страница "ответов"
						$rec_info = $db->super_query("SELECT fasts_num, likes_num, likes_users, tell_uid, tell_date, type, public, attach, tell_comm FROM `".PREFIX."_wall` WHERE id = '{$row['obj_id']}'");
						
						//КНопка Показать полностью..
						$expBR = explode('<br />', $row['action_text']);
						$textLength = count($expBR);
						$strTXT = strlen($row['action_text']);
						if($textLength > 9 OR $strTXT > 600)
							$row['action_text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row['obj_id'].'">'.$row['action_text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row['obj_id'].', this.id)" id="hide_wall_rec_lnk'.$row['obj_id'].'">Показать полностью..</div>';
						
						//Прикрипленные файлы
						if($rec_info['attach']){
							$attach_arr = explode('||', $rec_info['attach']);
							$cnt_attach = 1;
							$cnt_attach_link = 1;
							$jid = 0;
							$attach_result = '';
							$attach_result .= '<div class="clear"></div>';
							foreach($attach_arr as $attach_file){
								$attach_type = explode('|', $attach_file);
								
								//Фото со стены сообщества
								if($attach_type[0] == 'photo' AND file_exists(ROOT_DIR."/uploads/groups/{$rec_info['tell_uid']}/photos/c_{$attach_type[1]}")){
									if($cnt_attach < 2)
										$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$rec_info['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/groups/{$rec_info['tell_uid']}/photos/{$attach_type[1]}\" align=\"left\" /></div>";
									else
										$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/groups/{$rec_info['tell_uid']}/photos/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$rec_info['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
									
									$cnt_attach++;
									
									$resLinkTitle = '';
									
								//Фото со стены юзера
								} elseif($attach_type[0] == 'photo_u'){
									if($rec_info['tell_uid']) $attauthor_user_id = $rec_info['tell_uid'];
									else $attauthor_user_id = $row['ac_user_id'];
									if($attach_type[1] == 'attach' AND file_exists(ROOT_DIR."/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/{$attach_type[2]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$row_wall['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
											
										$cnt_attach++;
									} elseif(file_exists(ROOT_DIR."/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/{$attach_type[1]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$row_wall['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
											
										$cnt_attach++;
									}
									
									$resLinkTitle = '';

								//Видео
								} elseif($attach_type[0] == 'video' AND file_exists(ROOT_DIR."/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")){
								
									$for_cnt_attach_video = explode('video|', $rec_info['attach']);
									$cnt_attach_video = count($for_cnt_attach_video)-1;
							
									if($cnt_attach_video == 1 AND preg_match('/(photo|photo_u)/i', $rec_info['attach']) == false){
										
										$video_id = intval($attach_type[2]);
										
										$row_video = $db->super_query("SELECT video, title FROM `".PREFIX."_videos` WHERE id = '{$video_id}'", false, "wall/video{$video_id}");
										$row_video['title'] = stripslashes($row_video['title']);
										$row_video['video'] = stripslashes($row_video['video']);
										$row_video['video'] = strtr($row_video['video'], array('width="770"' => 'width="390"', 'height="420"' => 'height="310"'));
										
										$attach_result .= "<div class=\"cursor_pointer clear\" id=\"no_video_frame{$video_id}\" onClick=\"$('#'+this.id).hide();$('#video_frame{$video_id}').show();\">
										<div class=\"video_inline_icon\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px\" width=\"390\" height=\"310\" /></div><div id=\"video_frame{$video_id}\" class=\"no_display\" style=\"padding-top:3px\">{$row_video['video']}</div><div class=\"video_inline_vititle\"></div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><b>{$row_video['title']}</b></a>";
									
									} else {
										
										$attach_result .= "<div class=\"fl_l\"><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><div class=\"video_inline_icon video_inline_icon2\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" /></a></div>";
										
									}

									$resLinkTitle = '';
									
								//Музыка
								} elseif($attach_type[0] == 'audio'){
									$audioId = intval($attach_type[1]);
									$audioInfo = $db->super_query("SELECT artist, name, url FROM `".PREFIX."_audio` WHERE aid = '".$audioId."'");
									if($audioInfo){
										$jid++;
										$attach_result .= '<div class="audioForSize'.$row['obj_id'].' player_mini_mbar_wall_all" id="audioForSize"><div class="audio_onetrack audio_wall_onemus"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\''.$jid.'\', '.$row['obj_id'].')" id="icPlay_'.$row['obj_id'].$jid.'"></div><div id="music_'.$row['obj_id'].$jid.'" data="'.$audioInfo['url'].'" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query='.$audioInfo['artist'].'&n=1" onClick="Page.Go(this.href); return false"><b>'.stripslashes($audioInfo['artist']).'</b></a> &ndash; '.stripslashes($audioInfo['name']).'</div><div id="play_time'.$row['obj_id'].$jid.'" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall player_mini_mbar_wall_all" id="ppbarPro'.$row['obj_id'].$jid.'"></div></div></div>';
									}
									
									$resLinkTitle = '';
									
								//Смайлик
								} elseif($attach_type[0] == 'smile' AND file_exists(ROOT_DIR."/uploads/smiles/{$attach_type[1]}")){
									$attach_result .= '<img src=\"/uploads/smiles/'.$attach_type[1].'\" />';
									
									$resLinkTitle = '';
								//Если ссылка
								} elseif($attach_type[0] == 'link' AND preg_match('/http:\/\/(.*?)+$/i', $attach_type[1]) AND $cnt_attach_link == 1 AND stripos(str_replace('http://www.', 'http://', $attach_type[1]), $config['home_url']) === false){
									$count_num = count($attach_type);
									$domain_url_name = explode('/', $attach_type[1]);
									$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
									
									$attach_type[3] = stripslashes($attach_type[3]);
									$attach_type[3] = substr($attach_type[3], 0, 200);
										
									$attach_type[2] = stripslashes($attach_type[2]);
									$str_title = substr($attach_type[2], 0, 55);
									
									if(stripos($attach_type[4], '/uploads/attach/') === false){
										$attach_type[4] = '{theme}/images/no_ava_groups_100.gif';
										$no_img = false;
									} else
										$no_img = true;
									
									if(!$attach_type[3]) $attach_type[3] = '';
										
									if($no_img AND $attach_type[2]){
										if($rec_info['tell_comm']) $no_border_link = 'border:0px';
										
										$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="'.$no_border_link.'"><a href="/away.php?url='.$attach_type[1].'" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="'.$attach_type[4].'" /></div></a><div class="attatch_link_title"><a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$str_title.'</a></div><div style="max-height:50px;overflow:hidden">'.$attach_type[3].'</div></div></div>';

										$resLinkTitle = $attach_type[2];
										$resLinkUrl = $attach_type[1];
									} else if($attach_type[1] AND $attach_type[2]){
										$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div></div><div class="clear"></div>';
										
										$resLinkTitle = $attach_type[2];
										$resLinkUrl = $attach_type[1];
									}
									
									$cnt_attach_link++;
									
								//Если документ
								} elseif($attach_type[0] == 'doc'){
								
									$doc_id = intval($attach_type[1]);
									
									$row_doc = $db->super_query("SELECT dname, dsize FROM `".PREFIX."_doc` WHERE did = '{$doc_id}'");
									
									if($row_doc){
										
										$attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did='.$doc_id.'" target="_blank" onMouseOver="myhtml.title(\''.$doc_id.$cnt_attach.$row['obj_id'].'\', \'<b>Размер файла: '.$row_doc['dsize'].'</b>\', \'doc_\')" id="doc_'.$doc_id.$cnt_attach.$row['obj_id'].'">'.$row_doc['dname'].'</a></div></div></div><div class="clear"></div>';
											
										$cnt_attach++;
									}
									
									//Если опрос
									} elseif($attach_type[0] == 'vote'){
									
										$vote_id = intval($attach_type[1]);
										
										$row_vote = $db->super_query("SELECT title, answers, answer_num FROM `".PREFIX."_votes` WHERE id = '{$vote_id}'", false, "votes/vote_{$vote_id}");
										
										if($vote_id){

											$checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false, "votes/check{$user_id}_{$vote_id}");
											
											$row_vote['title'] = stripslashes($row_vote['title']);
											
											if(!$row_wall['text'])
												$row_wall['text'] = $row_vote['title'];

											$arr_answe_list = explode('|', stripslashes($row_vote['answers']));
											$max = $row_vote['answer_num'];
											
											$sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", 1, "votes/vote_answer_cnt_{$vote_id}");
											$answer = array();
											foreach($sql_answer as $row_answer){
											
												$answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
												
											}
											
											$attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
											
											for($ai = 0; $ai < sizeof($arr_answe_list); $ai++){

												if(!$checkMyVote['cnt']){
												
													$attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
												
												} else {

													$num = $answer[$ai]['cnt'];

													if(!$num ) $num = 0;
													if($max != 0) $proc = (100 * $num) / $max;
													else $proc = 0;
													$proc = round($proc, 2);
													
													$attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
													{$arr_answe_list[$ai]}<br />
													<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:".intval($proc)."%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
													<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
													</div><div class=\"clear\"></div>";
							
												}
											
											}
											
											if($row_vote['answer_num']) $answer_num_text = gram_record($row_vote['answer_num'], 'fave');
											else $answer_num_text = 'человек';
											
											if($row_vote['answer_num'] <= 1) $answer_text2 = 'Проголосовал';
											else $answer_text2 = 'Проголосовало';
												
											$attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
											
										}
										
									} else
					
										$attach_result .= '';
									
							}

							if($resLinkTitle AND $row['action_text'] == $resLinkUrl OR !$row['action_text'])
								$row['action_text'] = $resLinkTitle.$attach_result;
							else if($attach_result)
								$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']).$attach_result;
							else
								$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']);
						} else
							$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']);
						
						
						$resLinkTitle = '';
						
						//Если это запись с "рассказать друзьям"
						if($rec_info['tell_uid']){
							if($rec_info['public'])
								$rowUserTell = $db->super_query("SELECT title, photo FROM `".PREFIX."_communities` WHERE id = '{$rec_info['tell_uid']}'", false, "wall/group{$rec_info['tell_uid']}");
							else
								$rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$rec_info['tell_uid']}'");

							if(date('Y-m-d', $rec_info['tell_date']) == date('Y-m-d', $server_time))
								$dateTell = langdate('сегодня в H:i', $rec_info['tell_date']);
							elseif(date('Y-m-d', $rec_info['tell_date']) == date('Y-m-d', ($server_time-84600)))
								$dateTell = langdate('вчера в H:i', $rec_info['tell_date']);
							else
								$dateTell = langdate('j F Y в H:i', $rec_info['tell_date']);

							if($rec_info['public']){
								$rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
								$tell_link = 'public';
								if($rowUserTell['photo'])
									$avaTell = '/uploads/groups/'.$rec_info['tell_uid'].'/50_'.$rowUserTell['photo'];
								else
									$avaTell = '{theme}/images/no_ava_50.png';
							} else {
								$tell_link = 'u';
								if($rowUserTell['user_photo'])
									$avaTell = '/uploads/users/'.$rec_info['tell_uid'].'/50_'.$rowUserTell['user_photo'];
								else
									$avaTell = '{theme}/images/no_ava_50.png';
							}
							
							if($rec_info['tell_comm']) $border_tell_class = 'wall_repost_border'; else $border_tell_class = '';
				
							$row['action_text'] = <<<HTML
{$rec_info['tell_comm']}
<div class="{$border_tell_class}">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$rec_info['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$rec_info['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row['action_text']}
<div class="clear"></div>
</div>
HTML;
						}
						
						$tpl->set('{comment}', stripslashes($row['action_text']));

						//Если есть комменты к записи, то выполняем след. действия
						if($rec_info['fasts_num'])
							$tpl->set_block("'\\[comments-link\\](.*?)\\[/comments-link\\]'si","");
						else {
							$tpl->set('[comments-link]', '');
							$tpl->set('[/comments-link]', '');
						}

						if($user_privacy['val_wall3'] == 1 OR $user_privacy['val_wall3'] == 2 AND $check_friend OR $user_id == $row['ac_user_id']){
							$tpl->set('[comments-link]', '');
							$tpl->set('[/comments-link]', '');
						} else
							$tpl->set_block("'\\[comments-link\\](.*?)\\[/comments-link\\]'si","");

						if($rec_info['type'])
							$tpl->set('{action-type-updates}', $rec_info['type']);
						else
							$tpl->set('{action-type-updates}', '');

						//Мне нравится
						if(stripos($rec_info['likes_users'], "u{$user_id}|") !== false){
							$tpl->set('{yes-like}', 'public_wall_like_yes');
							$tpl->set('{yes-like-color}', 'public_wall_like_yes_color');
							$tpl->set('{like-js-function}', 'groups.wall_remove_like('.$row['obj_id'].', '.$user_id.', \'uPages\')');
						} else {
							$tpl->set('{yes-like}', '');
							$tpl->set('{yes-like-color}', '');
							$tpl->set('{like-js-function}', 'groups.wall_add_like('.$row['obj_id'].', '.$user_id.', \'uPages\')');
						}
						
						if($rec_info['likes_num']){
							$tpl->set('{likes}', $rec_info['likes_num']);
							$tpl->set('{likes-text}', '<span id="like_text_num'.$row['obj_id'].'">'.$rec_info['likes_num'].'</span> '.gram_record($rec_info['likes_num'], 'like'));
						} else {
							$tpl->set('{likes}', '');
							$tpl->set('{likes-text}', '<span id="like_text_num'.$row['obj_id'].'">0</span> человеку');
						}
						
						//Выводим информцию о том кто смотрит страницу для себя
						$tpl->set('{viewer-id}', $user_id);
						if($user_info['user_photo'])
							$tpl->set('{viewer-ava}', '/uploads/users/'.$user_id.'/50_'.$user_info['user_photo']);
						else
							$tpl->set('{viewer-ava}', '{theme}/images/no_ava_50.png');
				
						$tpl->set('{rec-id}', $row['obj_id']);
						$tpl->set('[record]', '');
						$tpl->set('[/record]', '');
						$tpl->set('[wall]', '');
						$tpl->set('[/wall]', '');
						$tpl->set('[wall-func]', '');
						$tpl->set('[/wall-func]', '');
						$tpl->set_block("'\\[groups\\](.*?)\\[/groups\\]'si","");
						$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
						$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
						$tpl->compile('content');

						//Если есть комменты, то выводим и страница не "ответы"
						if($user_privacy['val_wall3'] == 1 OR $user_privacy['val_wall3'] == 2 AND $check_friend OR $user_id == $row['ac_user_id']){
						
							//Помещаем все комменты в id wall_fast_block_{id} это для JS
							$tpl->result['content'] .= '<div id="wall_fast_block_'.$row['obj_id'].'">';
							if($rec_info['fasts_num']){
								if($rec_info['fasts_num'] > 3)
									$comments_limit = $rec_info['fasts_num']-3;
								else
									$comments_limit = 0;
								
								$sql_comments = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, tb2.user_photo, user_search_pref FROM `".PREFIX."_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '{$row['obj_id']}' ORDER by `add_date` ASC LIMIT {$comments_limit}, 3", 1);

								//Загружаем кнопку "Показать N запсии"
								$tpl->set('{gram-record-all-comm}', gram_record(($rec_info['fasts_num']-3), 'prev').' '.($rec_info['fasts_num']-3).' '.gram_record(($rec_info['fasts_num']-3), 'comments'));
								if($rec_info['fasts_num'] < 4)
									$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
								else {
									$tpl->set('{rec-id}', $row['obj_id']);
									$tpl->set('[all-comm]', '');
									$tpl->set('[/all-comm]', '');
								}
								$tpl->set('{author-id}', $row['ac_user_id']);
								$tpl->set('[wall-func]', '');
								$tpl->set('[/wall-func]', '');
								$tpl->set_block("'\\[groups\\](.*?)\\[/groups\\]'si","");
								$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
								$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
								$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
								$tpl->compile('content');
							
								//Сообственно выводим комменты
								foreach($sql_comments as $row_comments){
									$tpl->set('{name}', $row_comments['user_search_pref']);
									if($row_comments['user_photo'])
										$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comments['author_user_id'].'/50_'.$row_comments['user_photo']);
									else
										$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
										
									$tpl->set('{rec-id}', $row['obj_id']);
									$tpl->set('{comm-id}', $row_comments['id']);
									$tpl->set('{user-id}', $row_comments['author_user_id']);
									
									$expBR2 = explode('<br />', $row_comments['text']);
									$textLength2 = count($expBR2);
									$strTXT2 = strlen($row_comments['text']);
									if($textLength2 > 6 OR $strTXT2 > 470)
										$row_comments['text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row_comments['id'].'" style="max-height:102px"">'.$row_comments['text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row_comments['id'].', this.id)" id="hide_wall_rec_lnk'.$row_comments['id'].'">Показать полностью..</div>';
					
									//Обрабатываем ссылки
									$row_comments['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_comments['text']);
									
									$tpl->set('{text}', stripslashes($row_comments['text']));
									megaDate($row_comments['add_date']);
									if($user_id == $row_comments['author_user_id']){
										$tpl->set('[owner]', '');
										$tpl->set('[/owner]', '');
									} else
										$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
								
									if($user_id == $row_comments['author_user_id'])
						
										$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
										
									else {

										$tpl->set('[not-owner]', '');
										$tpl->set('[/not-owner]', '');
										
									}
									
									$tpl->set('[comment]', '');
									$tpl->set('[/comment]', '');
									$tpl->set('[wall-func]', '');
									$tpl->set('[/wall-func]', '');
									$tpl->set_block("'\\[groups\\](.*?)\\[/groups\\]'si","");
									$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
									$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
									$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
									$tpl->compile('content');
								}

								//Загружаем форму ответа
								$tpl->set('{rec-id}', $row['obj_id']);
								$tpl->set('{author-id}', $row['ac_user_id']);
								$tpl->set('[comment-form]', '');
								$tpl->set('[/comment-form]', '');
								$tpl->set('[wall-func]', '');
								$tpl->set('[/wall-func]', '');
								$tpl->set_block("'\\[groups\\](.*?)\\[/groups\\]'si","");
								$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
								$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
								$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
								$tpl->compile('content');
							}
							$tpl->result['content'] .= '</div>';
						}
						
					//====================================//
					//Если запись со стены сообщества
					} else if($row['action_type'] == 11){

						//Выводим кол-во комментов, мне нравится, и список юзеров кто поставил лайки к записи если это не страница "ответов"
						$rec_info_groups = $db->super_query("SELECT fasts_num, likes_num, likes_users, attach, tell_uid, tell_date, tell_comm, public FROM `".PREFIX."_communities_wall` WHERE id = '{$row['obj_id']}'");
						
						//КНопка Показать полностью..
						$expBR = explode('<br />', $row['action_text']);
						$textLength = count($expBR);
						$strTXT = strlen($row['action_text']);
						if($textLength > 9 OR $strTXT > 600)
							$row['action_text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row['obj_id'].'">'.$row['action_text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row['obj_id'].', this.id)" id="hide_wall_rec_lnk'.$row['obj_id'].'">Показать полностью..</div>';
							
						//Прикрипленные файлы
						if($rec_info_groups['attach']){
							$attach_arr = explode('||', $rec_info_groups['attach']);
							$cnt_attach = 1;
							$cnt_attach_link = 1;
							$jid = 0;
							$attach_result = '';
							$attach_result .= '<div class="clear"></div>';
							foreach($attach_arr as $attach_file){
								$attach_type = explode('|', $attach_file);
								
								if($rec_info_groups['public'])
									$row['ac_user_id'] = $rec_info_groups['tell_uid'];
								
								//Фото со стены сообщества
								if($attach_type[0] == 'photo' AND file_exists(ROOT_DIR."/uploads/groups/{$row['ac_user_id']}/photos/c_{$attach_type[1]}")){
									if($cnt_attach < 2)
										$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$row['ac_user_id']}', '{$attach_type[1]}', '{$cnt_attach}')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row['ac_user_id']}/photos/{$attach_type[1]}\" align=\"left\" /></div>";
									else
										$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row['ac_user_id']}/photos/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$row['ac_user_id']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
									
									$cnt_attach++;
									
								//Фото со стены юзера
								} elseif($attach_type[0] == 'photo_u'){
									if($rec_info_groups['tell_uid']) $attauthor_user_id = $rec_info_groups['tell_uid'];
									else $attauthor_user_id = $row['ac_user_id'];

									if($attach_type[1] == 'attach' AND file_exists(ROOT_DIR."/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/{$attach_type[2]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
											
										$cnt_attach++;
									} elseif(file_exists(ROOT_DIR."/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")){
										if($cnt_attach < 2)
											$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row['obj_id']}\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/{$attach_type[1]}\" align=\"left\" /></div>";
										else
											$attach_result .= "<img id=\"photo_wall_{$row['obj_id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['obj_id']}', '{$row['obj_id']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['obj_id']}\" />";
											
										$cnt_attach++;
									}
									
									$resLinkTitle = '';
						
								//Видео
								} elseif($attach_type[0] == 'video' AND file_exists(ROOT_DIR."/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")){
								
									$for_cnt_attach_video = explode('video|', $rec_info_groups['attach']);
									$cnt_attach_video = count($for_cnt_attach_video)-1;
							
									if($cnt_attach_video == 1 AND preg_match('/(photo|photo_u)/i', $rec_info_groups['attach']) == false){
										
										$video_id = intval($attach_type[2]);
										
										$row_video = $db->super_query("SELECT video, title FROM `".PREFIX."_videos` WHERE id = '{$video_id}'", false, "wall/video{$video_id}");
										$row_video['title'] = stripslashes($row_video['title']);
										$row_video['video'] = stripslashes($row_video['video']);
										$row_video['video'] = strtr($row_video['video'], array('width="770"' => 'width="390"', 'height="420"' => 'height="310"'));
										
										$attach_result .= "<div class=\"cursor_pointer clear\" id=\"no_video_frame{$video_id}\" onClick=\"$('#'+this.id).hide();$('#video_frame{$video_id}').show();\">
										<div class=\"video_inline_icon\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px\" width=\"390\" height=\"310\" /></div><div id=\"video_frame{$video_id}\" class=\"no_display\" style=\"padding-top:3px\">{$row_video['video']}</div><div class=\"video_inline_vititle\"></div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><b>{$row_video['title']}</b></a>";
									
									} else {
										
										$attach_result .= "<div class=\"fl_l\"><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><div class=\"video_inline_icon video_inline_icon2\"></div><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" /></a></div>";
										
									}

									$resLinkTitle = '';
									
								//Музыка
								} elseif($attach_type[0] == 'audio'){
									$audioId = intval($attach_type[1]);
									$audioInfo = $db->super_query("SELECT artist, name, url FROM `".PREFIX."_audio` WHERE aid = '".$audioId."'");
									if($audioInfo){
										$jid++;
										$attach_result .= '<div class="audioForSize'.$row['obj_id'].' player_mini_mbar_wall_all" id="audioForSize"><div class="audio_onetrack audio_wall_onemus"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\''.$jid.'\', '.$row['obj_id'].')" id="icPlay_'.$row['obj_id'].$jid.'"></div><div id="music_'.$row['obj_id'].$jid.'" data="'.$audioInfo['url'].'" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query='.$audioInfo['artist'].'&n=1" onClick="Page.Go(this.href); return false"><b>'.stripslashes($audioInfo['artist']).'</b></a> &ndash; '.stripslashes($audioInfo['name']).'</div><div id="play_time'.$row['obj_id'].$jid.'" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall_all" id="ppbarPro'.$row['obj_id'].$jid.'"></div></div></div>';
									}
									
									$resLinkTitle = '';
									
								//Смайлик
								} elseif($attach_type[0] == 'smile' AND file_exists(ROOT_DIR."/uploads/smiles/{$attach_type[1]}")){
									$attach_result .= '<img src=\"/uploads/smiles/'.$attach_type[1].'\" style="margin-right:5px" />';
									
									$resLinkTitle = '';
									
								//Если ссылка
								} elseif($attach_type[0] == 'link' AND preg_match('/http:\/\/(.*?)+$/i', $attach_type[1]) AND $cnt_attach_link == 1 AND stripos(str_replace('http://www.', 'http://', $attach_type[1]), $config['home_url']) === false){
									$count_num = count($attach_type);
									$domain_url_name = explode('/', $attach_type[1]);
									$rdomain_url_name = str_replace('http://', '', $domain_url_name[2]);
									
									$attach_type[3] = stripslashes($attach_type[3]);
									$attach_type[3] = substr($attach_type[3], 0, 200);
										
									$attach_type[2] = stripslashes($attach_type[2]);
									$str_title = substr($attach_type[2], 0, 55);
									
									if(stripos($attach_type[4], '/uploads/attach/') === false){
										$attach_type[4] = '{theme}/images/no_ava_groups_100.gif';
										$no_img = false;
									} else
										$no_img = true;
									
									if(!$attach_type[3]) $attach_type[3] = '';
										
									if($no_img AND $attach_type[2]){
										if($rec_info_groups['tell_comm']) $no_border_link = 'border:0px';
										
										$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="'.$no_border_link.'"><a href="/away.php?url='.$attach_type[1].'" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="'.$attach_type[4].'" /></div></a><div class="attatch_link_title"><a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$str_title.'</a></div><div style="max-height:50px;overflow:hidden">'.$attach_type[3].'</div></div></div>';

										$resLinkTitle = $attach_type[2];
										$resLinkUrl = $attach_type[1];
									} else if($attach_type[1] AND $attach_type[2]){
										$attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/away.php?url='.$attach_type[1].'" target="_blank">'.$rdomain_url_name.'</a></div></div></div><div class="clear"></div>';
										
										$resLinkTitle = $attach_type[2];
										$resLinkUrl = $attach_type[1];
									}
									
									$cnt_attach_link++;
									
								//Если документ
								} elseif($attach_type[0] == 'doc'){
								
									$doc_id = intval($attach_type[1]);
									
									$row_doc = $db->super_query("SELECT dname, dsize FROM `".PREFIX."_doc` WHERE did = '{$doc_id}'");
									
									if($row_doc){
										
										$attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did='.$doc_id.'" target="_blank" onMouseOver="myhtml.title(\''.$doc_id.$cnt_attach.$row['obj_id'].'\', \'<b>Размер файла: '.$row_doc['dsize'].'</b>\', \'doc_\')" id="doc_'.$doc_id.$cnt_attach.$row['obj_id'].'">'.$row_doc['dname'].'</a></div></div></div><div class="clear"></div>';
											
										$cnt_attach++;
									}
									
									//Если опрос
									} elseif($attach_type[0] == 'vote'){
									
										$vote_id = intval($attach_type[1]);
										
										$row_vote = $db->super_query("SELECT title, answers, answer_num FROM `".PREFIX."_votes` WHERE id = '{$vote_id}'", false, "votes/vote_{$vote_id}");
										
										if($vote_id){

											$checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false, "votes/check{$user_id}_{$vote_id}");
											
											$row_vote['title'] = stripslashes($row_vote['title']);
											
											if(!$row_wall['text'])
												$row_wall['text'] = $row_vote['title'];

											$arr_answe_list = explode('|', stripslashes($row_vote['answers']));
											$max = $row_vote['answer_num'];
											
											$sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `".PREFIX."_votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", 1, "votes/vote_answer_cnt_{$vote_id}");
											$answer = array();
											foreach($sql_answer as $row_answer){
											
												$answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
												
											}
											
											$attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
											
											for($ai = 0; $ai < sizeof($arr_answe_list); $ai++){

												if(!$checkMyVote['cnt']){
												
													$attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
												
												} else {

													$num = $answer[$ai]['cnt'];

													if(!$num ) $num = 0;
													if($max != 0) $proc = (100 * $num) / $max;
													else $proc = 0;
													$proc = round($proc, 2);
													
													$attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
													{$arr_answe_list[$ai]}<br />
													<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:".intval($proc)."%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
													<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
													</div><div class=\"clear\"></div>";
							
												}
											
											}
											
											if($row_vote['answer_num']) $answer_num_text = gram_record($row_vote['answer_num'], 'fave');
											else $answer_num_text = 'человек';
											
											if($row_vote['answer_num'] <= 1) $answer_text2 = 'Проголосовал';
											else $answer_text2 = 'Проголосовало';
												
											$attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
											
										}
										
									} else
					
										$attach_result .= '';
						
							}
							
							if($resLinkTitle AND $row['action_text'] == $resLinkUrl OR !$row['action_text'])
								$row['action_text'] = $resLinkTitle.$attach_result;
							else if($attach_result)
								$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']).$attach_result;
							else
								$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']);
		
						} else
							$row['action_text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row['action_text']);
						
						$resLinkTitle = '';
						
						//Если это запись с "рассказать друзьям"
						if($rec_info_groups['tell_uid']){
							if($rec_info_groups['public'])
								$rowUserTell = $db->super_query("SELECT title, photo FROM `".PREFIX."_communities` WHERE id = '{$rec_info_groups['tell_uid']}'", false, "wall/group{$rec_info_groups['tell_uid']}");
							else
								$rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$rec_info_groups['tell_uid']}'");

							if(date('Y-m-d', $rec_info_groups['tell_date']) == date('Y-m-d', $server_time))
								$dateTell = langdate('сегодня в H:i', $rec_info_groups['tell_date']);
							elseif(date('Y-m-d', $rec_info_groups['tell_date']) == date('Y-m-d', ($server_time-84600)))
								$dateTell = langdate('вчера в H:i', $rec_info_groups['tell_date']);
							else
								$dateTell = langdate('j F Y в H:i', $rec_info_groups['tell_date']);

							if($rec_info_groups['public']){
								$rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
								$tell_link = 'public';
								if($rowUserTell['photo'])
									$avaTell = '/uploads/groups/'.$rec_info_groups['tell_uid'].'/50_'.$rowUserTell['photo'];
								else
									$avaTell = '{theme}/images/no_ava_50.png';
							} else {
								$tell_link = 'u';
								if($rowUserTell['user_photo'])
									$avaTell = '/uploads/users/'.$rec_info_groups['tell_uid'].'/50_'.$rowUserTell['user_photo'];
								else
									$avaTell = '{theme}/images/no_ava_50.png';
							}
							
							if($rec_info_groups['tell_comm']) $border_tell_class = 'wall_repost_border'; else $border_tell_class = 'wall_repost_border3';
				
							$row['action_text'] = <<<HTML
{$rec_info_groups['tell_comm']}
<div class="{$border_tell_class}">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$rec_info_groups['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$rec_info_groups['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row['action_text']}
<div class="clear"></div>
</div>
HTML;
						}
						
						$tpl->set('{comment}', stripslashes($row['action_text']));
						

						//Если есть комменты к записи, то выполняем след. действия
						if($rec_info_groups['fasts_num'] OR $rowInfoUser['comments'] == false)
							$tpl->set_block("'\\[comments-link\\](.*?)\\[/comments-link\\]'si","");
						else {
							$tpl->set('[comments-link]', '');
							$tpl->set('[/comments-link]', '');
						}	

						//Мне нравится
						if(stripos($rec_info_groups['likes_users'], "u{$user_id}|") !== false){
							$tpl->set('{yes-like}', 'public_wall_like_yes');
							$tpl->set('{yes-like-color}', 'public_wall_like_yes_color');
							$tpl->set('{like-js-function}', 'groups.wall_remove_like('.$row['obj_id'].', '.$user_id.')');
						} else {
							$tpl->set('{yes-like}', '');
							$tpl->set('{yes-like-color}', '');
							$tpl->set('{like-js-function}', 'groups.wall_add_like('.$row['obj_id'].', '.$user_id.')');
						}
						
						if($rec_info_groups['likes_num']){
							$tpl->set('{likes}', $rec_info_groups['likes_num']);
							$tpl->set('{likes-text}', '<span id="like_text_num'.$row['obj_id'].'">'.$rec_info_groups['likes_num'].'</span> '.gram_record($rec_info_groups['likes_num'], 'like'));
						} else {
							$tpl->set('{likes}', '');
							$tpl->set('{likes-text}', '<span id="like_text_num'.$row['obj_id'].'">0</span> человеку');
						}
						
						//Выводим информцию о том кто смотрит страницу для себя
						$tpl->set('{viewer-id}', $user_id);
						if($user_info['user_photo'])
							$tpl->set('{viewer-ava}', '/uploads/users/'.$user_id.'/50_'.$user_info['user_photo']);
						else
							$tpl->set('{viewer-ava}', '{theme}/images/no_ava_50.png');
				
						$tpl->set('{rec-id}', $row['obj_id']);
						$tpl->set('[record]', '');
						$tpl->set('[/record]', '');
						$tpl->set('[wall]', '');
						$tpl->set('[/wall]', '');
						$tpl->set('[groups]', '');
						$tpl->set('[/groups]', '');
						$tpl->set_block("'\\[wall-func\\](.*?)\\[/wall-func\\]'si","");
						$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
						$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
						$tpl->compile('content');

						//Если есть комменты, то выводим и страница не "ответы"
						if($rowInfoUser['comments']){
						
							//Помещаем все комменты в id wall_fast_block_{id} это для JS
							$tpl->result['content'] .= '<div id="wall_fast_block_'.$row['obj_id'].'">';
							if($rec_info_groups['fasts_num']){
								if($rec_info_groups['fasts_num'] > 3)
									$comments_limit = $rec_info_groups['fasts_num']-3;
								else
									$comments_limit = 0;
								
								$sql_comments = $db->super_query("SELECT tb1.id, public_id, text, add_date, tb2.user_photo, user_search_pref FROM `".PREFIX."_communities_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.public_id = tb2.user_id AND tb1.fast_comm_id = '{$row['obj_id']}' ORDER by `add_date` ASC LIMIT {$comments_limit}, 3", 1);

								//Загружаем кнопку "Показать N запсии"
								$tpl->set('{gram-record-all-comm}', gram_record(($rec_info_groups['fasts_num']-3), 'prev').' '.($rec_info_groups['fasts_num']-3).' '.gram_record(($rec_info_groups['fasts_num']-3), 'comments'));
								if($rec_info_groups['fasts_num'] < 4)
									$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
								else {
									$tpl->set('{rec-id}', $row['obj_id']);
									$tpl->set('[all-comm]', '');
									$tpl->set('[/all-comm]', '');
								}
								$tpl->set('{author-id}', $row['ac_user_id']);
								$tpl->set('[groups]', '');
								$tpl->set('[/groups]', '');
								$tpl->set_block("'\\[wall-func\\](.*?)\\[/wall-func\\]'si","");
								$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
								$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
								$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
								$tpl->compile('content');
							
								//Сообственно выводим комменты
								foreach($sql_comments as $row_comments){
									$tpl->set('{name}', $row_comments['user_search_pref']);
									if($row_comments['user_photo'])
										$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comments['public_id'].'/50_'.$row_comments['user_photo']);
									else
										$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
										
									$tpl->set('{rec-id}', $row['obj_id']);
									$tpl->set('{comm-id}', $row_comments['id']);
									$tpl->set('{user-id}', $row_comments['public_id']);
									$tpl->set('{public-id}', $row['ac_user_id']);
									
									$expBR2 = explode('<br />', $row_comments['text']);
									$textLength2 = count($expBR2);
									$strTXT2 = strlen($row_comments['text']);
									if($textLength2 > 6 OR $strTXT2 > 470)
										$row_comments['text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row_comments['id'].'" style="max-height:102px"">'.$row_comments['text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row_comments['id'].', this.id)" id="hide_wall_rec_lnk'.$row_comments['id'].'">Показать полностью..</div>';
										
									//Обрабатываем ссылки
									$row_comments['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_comments['text']);
									
									$tpl->set('{text}', stripslashes($row_comments['text']));
									megaDate($row_comments['add_date']);
									if($user_id == $row_comments['public_id']){
										$tpl->set('[owner]', '');
										$tpl->set('[/owner]', '');
									} else
										$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
								
									if($user_id == $row_comments['author_user_id'])
						
										$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
										
									else {

										$tpl->set('[not-owner]', '');
										$tpl->set('[/not-owner]', '');
										
									}
						
									$tpl->set('[comment]', '');
									$tpl->set('[/comment]', '');
									$tpl->set('[groups]', '');
									$tpl->set('[/groups]', '');
									$tpl->set_block("'\\[wall-func\\](.*?)\\[/wall-func\\]'si","");
									$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
									$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
									$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
									$tpl->compile('content');
								}

								//Загружаем форму ответа
								$tpl->set('{rec-id}', $row['obj_id']);
								$tpl->set('{author-id}', $row['ac_user_id']);
								$tpl->set('[comment-form]', '');
								$tpl->set('[/comment-form]', '');
								$tpl->set('[groups]', '');
								$tpl->set('[/groups]', '');
								$tpl->set_block("'\\[wall-func\\](.*?)\\[/wall-func\\]'si","");
								$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
								$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
								$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
								$tpl->compile('content');
							}
							$tpl->result['content'] .= '</div>';
						}
					} else {
						$tpl->set('[record]', '');
						$tpl->set('[/record]', '');
						$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
						$tpl->set_block("'\\[wall\\](.*?)\\[/wall\\]'si","");
						$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
						$tpl->set_block("'\\[comments-link\\](.*?)\\[/comments-link\\]'si","");
						
						if($action_cnt)
							$tpl->compile('content');
					}
				}

				//Выводи низ, если новостей больше 20
				if($c > 19 AND !$_POST['page_cnt']){
					$tpl->load_template('news/head.tpl');
					$tpl->set('{type}', $type);
					$tpl->set('[bottom]', '');
					$tpl->set('[/bottom]', '');
					$tpl->set_block("'\\[news\\](.*?)\\[/news\\]'si","");
					$tpl->compile('content');
				}
			} else
				if(!$_POST['page_cnt'])
					msgbox('', $no_news, 'info_2');
				else
					echo 'no_news';
				
			//Если вызваны предыдущие новости
			if($_POST['page_cnt']){
				AjaxTpl();
				die();
			}

	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>