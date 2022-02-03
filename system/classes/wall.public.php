<?php
/* 
	Appointment: Класс для стены сообществ
	File: wall.groups.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/

class wall {

	var $query = false;
	var $template = false;
	var $compile = false;
	
	function query($query){
		global $db;
		
		$this->query = $db->super_query($query, 1);
	}

	function template($template){
		global $tpl;
		$this->template = $tpl->load_template($template);
	}
	
	function compile($compile){
		$this->compile = $compile;
	}
	
	function select($public_admin, $server_time){
		global $tpl, $db, $user_info, $row, $config;
		
		$user_id = $user_info['user_id'];

		$this->template;
		
		foreach($this->query as $row_wall){
			$tpl->set('{rec-id}', $row_wall['id']);
			
			//Закрепить запись
			if($row_wall['fixed']){
				
				$tpl->set('{styles-fasten}', 'style="opacity:1"');
				$tpl->set('{fasten-text}', 'Закрепленная запись');
				$tpl->set('{function-fasten}', 'wall_unfasten');
				
			} else {
				
				$tpl->set('{styles-fasten}', '');
				$tpl->set('{fasten-text}', 'Закрепить запись');
				$tpl->set('{function-fasten}', 'wall_fasten');
				
			}
			
			//КНопка Показать полностью..
			$expBR = explode('<br />', $row_wall['text']);
			$textLength = count($expBR);
			$strTXT = strlen($row_wall['text']);
			if($textLength > 9 OR $strTXT > 600)
				$row_wall['text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row_wall['id'].'">'.$row_wall['text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row_wall['id'].', this.id)" id="hide_wall_rec_lnk'.$row_wall['id'].'">Показать полностью..</div>';
				
			//Прикрипленные файлы
			if($row_wall['attach']){
				$attach_arr = explode('||', $row_wall['attach']);
				$cnt_attach = 1;
				$cnt_attach_link = 1;
				$jid = 0;
				$attach_result = '';
				$attach_result .= '<div class="clear"></div>';
				foreach($attach_arr as $attach_file){
					$attach_type = explode('|', $attach_file);
					
					//Фото со стены сообщества
					if($row_wall['tell_uid'])
						$globParId = $row_wall['tell_uid'];
					else
						$globParId = $row_wall['public_id'];
						
					if($attach_type[0] == 'photo' AND file_exists(ROOT_DIR."/uploads/groups/{$globParId}/photos/c_{$attach_type[1]}")){
						if($cnt_attach < 2)
							$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_wall['id']}\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '{$globParId}', '{$attach_type[1]}', '{$cnt_attach}')\"><img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$globParId}/photos/{$attach_type[1]}\" align=\"left\" /></div>";
						else
							$attach_result .= "<img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$globParId}/photos/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '{$globParId}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_wall['id']}\" />";
						
						$cnt_attach++;
						
						$resLinkTitle = '';
						
					//Фото со стены юзера
					} elseif($attach_type[0] == 'photo_u'){
						$attauthor_user_id = $row_wall['tell_uid'];

						if($attach_type[1] == 'attach' AND file_exists(ROOT_DIR."/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")){
							if($cnt_attach < 2)
								$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_wall['id']}\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/{$attach_type[2]}\" align=\"left\" /></div>";
							else
								$attach_result .= "<img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_wall['id']}\" />";
								
							$cnt_attach++;
						} elseif(file_exists(ROOT_DIR."/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")){
							if($cnt_attach < 2)
								$attach_result .= "<div class=\"profile_wall_attach_photo cursor_pointer page_num{$row_wall['id']}\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '{$attauthor_user_id}', '{$attach_type[1]}', '{$cnt_attach}', 'photo_u')\"><img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/{$attach_type[1]}\" align=\"left\" /></div>";
							else
								$attach_result .= "<img id=\"photo_wall_{$row_wall['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row_wall['id']}', '{$row_wall['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row_wall['id']}\" />";
								
							$cnt_attach++;
						}
						
						$resLinkTitle = '';
						
					//Видео
					} elseif($attach_type[0] == 'video' AND file_exists(ROOT_DIR."/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")){
					
						$for_cnt_attach_video = explode('video|', $row_wall['attach']);
						$cnt_attach_video = count($for_cnt_attach_video)-1;
				
						if($cnt_attach_video == 1 AND preg_match('/(photo|photo_u)/i', $row_wall['attach']) == false){
							
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
							$attach_result .= '<div class="audioForSize'.$row_wall['id'].'" id="audioForSize"><div class="audio_onetrack audio_wall_onemus"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\''.$jid.'\', '.$row_wall['id'].')" id="icPlay_'.$row_wall['id'].$jid.'"></div><div id="music_'.$row_wall['id'].$jid.'" data="'.$audioInfo['url'].'" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query='.$audioInfo['artist'].'&n=1" onClick="Page.Go(this.href); return false"><b>'.stripslashes($audioInfo['artist']).'</b></a> &ndash; '.stripslashes($audioInfo['name']).'</div><div id="play_time'.$row_wall['id'].$jid.'" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall" id="ppbarPro'.$row_wall['id'].$jid.'"></div></div></div>';
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
						$attach_type[3] = iconv_substr($attach_type[3], 0, 200, 'utf-8');
							
						$attach_type[2] = stripslashes($attach_type[2]);
						$str_title = iconv_substr($attach_type[2], 0, 55, 'utf-8');
						
						if(stripos($attach_type[4], '/uploads/attach/') === false){
							$attach_type[4] = '{theme}/images/no_ava_groups_100.gif';
							$no_img = false;
						} else
							$no_img = true;
						
						if(!$attach_type[3]) $attach_type[3] = '';
							
						if($no_img AND $attach_type[2]){
							if($row_wall['tell_comm']) $no_border_link = 'border:0px';
							
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
						
						$row_doc = $db->super_query("SELECT dname, dsize FROM `".PREFIX."_doc` WHERE did = '{$doc_id}'", false, "wall/doc{$doc_id}");
						
						if($row_doc){
							
							$attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did='.$doc_id.'" target="_blank" onMouseOver="myhtml.title(\''.$doc_id.$cnt_attach.$row_wall['id'].'\', \'<b>Размер файла: '.$row_doc['dsize'].'</b>\', \'doc_\')" id="doc_'.$doc_id.$cnt_attach.$row_wall['id'].'">'.$row_doc['dname'].'</a></div></div></div><div class="clear"></div>';
								
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
					
				if($resLinkTitle AND $row_wall['text'] == $resLinkUrl OR !$row_wall['text'])
					$row_wall['text'] = $resLinkTitle.$attach_result;
				else if($attach_result)
					$row_wall['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_wall['text']).$attach_result;
				else
					$row_wall['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_wall['text']);
			} else
				$row_wall['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_wall['text']);
			
			$resLinkTitle = '';
			
			//Если это запись с "рассказать друзьям"
			if($row_wall['tell_uid']){
				if($row_wall['public'])
					$rowUserTell = $db->super_query("SELECT title, photo FROM `".PREFIX."_communities` WHERE id = '{$row_wall['tell_uid']}'", false, "wall/group{$row_wall['tell_uid']}");
				else
					$rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$row_wall['tell_uid']}'");

				if(date('Y-m-d', $row_wall['tell_date']) == date('Y-m-d', $server_time))
					$dateTell = langdate('сегодня в H:i', $row_wall['tell_date']);
				elseif(date('Y-m-d', $row_wall['tell_date']) == date('Y-m-d', ($server_time-84600)))
					$dateTell = langdate('вчера в H:i', $row_wall['tell_date']);
				else
					$dateTell = langdate('j F Y в H:i', $row_wall['tell_date']);
				
				if($row_wall['public']){
					$rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
					$tell_link = 'public';
					if($rowUserTell['photo'])
						$avaTell = '/uploads/groups/'.$row_wall['tell_uid'].'/50_'.$rowUserTell['photo'];
					else
						$avaTell = '{theme}/images/no_ava_50.png';
				} else {
					$tell_link = 'u';
					if($rowUserTell['user_photo'])
						$avaTell = '/uploads/users/'.$row_wall['tell_uid'].'/50_'.$rowUserTell['user_photo'];
					else
						$avaTell = '{theme}/images/no_ava_50.png';
				}
				
				if($row_wall['tell_comm']) $border_tell_class = 'wall_repost_border'; else $border_tell_class = 'wall_repost_border2';
				
				$row_wall['text'] = <<<HTML
{$row_wall['tell_comm']}
<div class="{$border_tell_class}">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$row_wall['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$row_wall['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row_wall['text']}
<div class="clear"></div>
</div>
HTML;
			}
			
			$tpl->set('{text}', stripslashes($row_wall['text']));
			$tpl->set('{name}', $row_wall['title']);
			
			$tpl->set('{user-id}', $row_wall['public_id']);
			if($row_wall['adres']) $tpl->set('{adres-id}', $row_wall['adres']);
			else $tpl->set('{adres-id}', 'public'.$row_wall['public_id']);
			
			megaDate($row_wall['add_date']);
			
			if($row_wall['photo'])
				$tpl->set('{ava}', '/uploads/groups/'.$row_wall['public_id'].'/50_'.$row_wall['photo']);
			else
				$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
			
			//Мне нравится
			if(stripos($row_wall['likes_users'], "u{$user_id}|") !== false){
				$tpl->set('{yes-like}', 'public_wall_like_yes');
				$tpl->set('{yes-like-color}', 'public_wall_like_yes_color');
				$tpl->set('{like-js-function}', 'groups.wall_remove_like('.$row_wall['id'].', '.$user_id.')');
			} else {
				$tpl->set('{yes-like}', '');
				$tpl->set('{yes-like-color}', '');
				$tpl->set('{like-js-function}', 'groups.wall_add_like('.$row_wall['id'].', '.$user_id.')');
			}
			
			if($row_wall['likes_num']){
				$tpl->set('{likes}', $row_wall['likes_num']);
				$tpl->set('{likes-text}', '<span id="like_text_num'.$row_wall['id'].'">'.$row_wall['likes_num'].'</span> '.gram_record($row_wall['likes_num'], 'like'));
			} else {
				$tpl->set('{likes}', '');
				$tpl->set('{likes-text}', '<span id="like_text_num'.$row_wall['id'].'">0</span> человеку');
			}
			
			//Выводим информцию о том кто смотрит страницу для себя
			$tpl->set('{viewer-id}', $user_id);
			if($user_info['user_photo'])
				$tpl->set('{viewer-ava}', '/uploads/users/'.$user_id.'/50_'.$user_info['user_photo']);
			else
				$tpl->set('{viewer-ava}', '{theme}/images/no_ava_50.png');
			
			//Админ
			if($public_admin){
				$tpl->set('[owner]', '');
				$tpl->set('[/owner]', '');
			} else
				$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");

			//Если есть комменты к записи, то выполняем след. действия / Приватность
			if($row_wall['fasts_num'])
				$tpl->set_block("'\\[comments-link\\](.*?)\\[/comments-link\\]'si","");
			else {
				$tpl->set('[comments-link]', '');
				$tpl->set('[/comments-link]', '');
			}
			
			$tpl->set('{public-id}', $row['id']);

			//Приватность комментирования записей
			if($row_wall['comments'] OR $public_admin){
				$tpl->set('[privacy-comment]', '');
				$tpl->set('[/privacy-comment]', '');
			} else
				$tpl->set_block("'\\[privacy-comment\\](.*?)\\[/privacy-comment\\]'si","");
				
			$tpl->set('[record]', '');
			$tpl->set('[/record]', '');
			$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
			$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
			$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
			$tpl->compile($this->compile);

			//Если есть комменты к записи, то открываем форму ответа уже в развернутом виде и выводим комменты к записи
			if($row_wall['comments'] OR $public_admin){
				if($row_wall['fasts_num']){
					
					//Помещаем все комменты в id wall_fast_block_{id} это для JS
					$tpl->result[$this->compile] .= '<div id="wall_fast_block_'.$row_wall['id'].'" class="public_wall_rec_comments">';
			
					if($row_wall['fasts_num'] > 3)
						$comments_limit = $row_wall['fasts_num']-3;
					else
						$comments_limit = 0;
					
					$sql_comments = $db->super_query("SELECT tb1.id, public_id, text, add_date, tb2.user_photo, user_search_pref FROM `".PREFIX."_communities_wall` tb1, `".PREFIX."_users` tb2 WHERE tb1.public_id = tb2.user_id AND tb1.fast_comm_id = '{$row_wall['id']}' ORDER by `add_date` ASC LIMIT {$comments_limit}, 3", 1);

					//Загружаем кнопку "Показать N запсии"
					$tpl->set('{gram-record-all-comm}', gram_record(($row_wall['fasts_num']-3), 'prev').' '.($row_wall['fasts_num']-3).' '.gram_record(($row_wall['fasts_num']-3), 'comments'));
					if($row_wall['fasts_num'] < 4)
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
					else {
						$tpl->set('{rec-id}', $row_wall['id']);
						$tpl->set('[all-comm]', '');
						$tpl->set('[/all-comm]', '');
					}
					$tpl->set('{public-id}', $row['id']);
					$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
					$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
					$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
					$tpl->compile($this->compile);
				
					//Сообственно выводим комменты
					foreach($sql_comments as $row_comments){
						$tpl->set('{public-id}', $row['id']);
						$tpl->set('{name}', $row_comments['user_search_pref']);
						if($row_comments['user_photo'])
							$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comments['public_id'].'/50_'.$row_comments['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						
						$tpl->set('{rec-id}', $row_wall['id']);
						$tpl->set('{comm-id}', $row_comments['id']);
						$tpl->set('{user-id}', $row_comments['public_id']);
						
						$expBR2 = explode('<br />', $row_comments['text']);
						$textLength2 = count($expBR2);
						$strTXT2 = strlen($row_comments['text']);
						if($textLength2 > 6 OR $strTXT2 > 470)
							$row_comments['text'] = '<div class="wall_strlen" id="hide_wall_rec'.$row_comments['id'].'" style="max-height:102px"">'.$row_comments['text'].'</div><div class="wall_strlen_full" onMouseDown="wall.FullText('.$row_comments['id'].', this.id)" id="hide_wall_rec_lnk'.$row_comments['id'].'">Показать полностью..</div>';
								
						//Обрабатываем ссылки
						$row_comments['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/away.php?url=$1" target="_blank">$1</a>', $row_comments['text']);
						
						$tpl->set('{text}', stripslashes($row_comments['text']));
						megaDate($row_comments['add_date']);
						if($public_admin OR $user_id == $row_comments['public_id']){
							$tpl->set('[owner]', '');
							$tpl->set('[/owner]', '');
						} else
							$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					
						if($user_id == $row_comments['public_id'])
						
							$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
							
						else {

							$tpl->set('[not-owner]', '');
							$tpl->set('[/not-owner]', '');
							
						}
						
						$tpl->set('[comment]', '');
						$tpl->set('[/comment]', '');
						$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
						$tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si","");
						$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
						$tpl->compile($this->compile);
					}

					//Загружаем форму ответа
					$tpl->set('{rec-id}', $row_wall['id']);
					$tpl->set('{user-id}', $row_wall['public_id']);
					$tpl->set('[comment-form]', '');
					$tpl->set('[/comment-form]', '');
					$tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si","");
					$tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si","");
					$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
					$tpl->compile($this->compile);
					
					//Закрываем блок для JS
					$tpl->result[$this->compile] .= '</div>';
				}
			}
		}
	}
}
?>