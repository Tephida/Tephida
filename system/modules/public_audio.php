<?php
/* 
	Appointment: Сообщества -> Публичные страницы -> Аудиозаписи
	File: public_audio.php 
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
	
	switch($act){
		
		//################### Добавление песни в список сообщества ###################//
		case "addlistgroup":
		
			NoAjaxQuery();
			
			$pid = intval($_POST['pid']);
			$aid = intval($_POST['aid']);
			
			$check = $db->super_query("SELECT url, artist, name FROM `".PREFIX."_audio` WHERE aid = '{$aid}'");
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			if($public_admin){
			
				$db->query("INSERT INTO `".PREFIX."_communities_audio` SET public_id = '{$pid}', url = '".$db->safesql($check['url'])."', artist = '".$db->safesql($check['artist'])."', name = '".$db->safesql($check['name'])."',  adate = '{$server_time}'");
				
				$db->query("UPDATE `".PREFIX."_communities` SET audio_num = audio_num+1 WHERE id = '{$pid}'");
				
				mozg_clear_cache_file("groups/audio{$pid}");
				
			}
			
			exit;
			
		break;
		
		//################### Сохранение отредактированых данных ###################//
		case "editsave":
		
			NoAjaxQuery();
			
			$aid = intval($_POST['aid']);
			$pid = intval($_POST['pid']);
			$artist = ajax_utf8(textFilter($_POST['artist'], false, true));
			$name = ajax_utf8(textFilter($_POST['name'], false, true));

			if(isset($artist) AND empty($artist)) $artist = 'Неизвестный исполнитель';
			if(isset($name) AND empty($name)) $name = 'Без названия';
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			if($public_admin){
			
				$db->query("UPDATE `".PREFIX."_communities_audio` SET artist = '{$artist}', name = '{$name}' WHERE aid = '{$aid}'");
				
				mozg_clear_cache_file("groups/audio{$pid}");
				
			}
			
			exit;
			
		break;
		
		//################### Удаление песни из БД ###################//
		case "del":
		
			NoAjaxQuery();
			
			$aid = intval($_POST['aid']);
			$pid = intval($_POST['pid']);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			if($public_admin){

				$db->query("DELETE FROM `".PREFIX."_communities_audio` WHERE aid = '{$aid}'");
				
				$db->query("UPDATE `".PREFIX."_communities` SET audio_num = audio_num-1 WHERE id = '{$pid}'");
				
				mozg_clear_cache_file("groups/audio{$pid}");
				
			}
			
			exit;
			
		break;
		
		//################### Поиск ###################//
		case "search":
			
			NoAjaxQuery();
			
			$sql_limit = 20;
			
			if($_POST['page'] > 0) $page_cnt = intval($_POST['page'])*$sql_limit;
			else $page_cnt = 0;
			
			$pid = intval($_POST['pid']);
	
			$query = $db->safesql(ajax_utf8(strip_data($_POST['query'])));
			$query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
			
			$adres = strip_tags($_POST['adres']);
			
			$row_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_audio` WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%'");
			
			$sql_ = $db->super_query("SELECT ".PREFIX."_audio.aid, url, artist, name, auser_id, ".PREFIX."_users.user_search_pref FROM ".PREFIX."_audio LEFT JOIN ".PREFIX."_users ON ".PREFIX."_audio.auser_id = ".PREFIX."_users.user_id WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%' ORDER by `adate` DESC LIMIT {$page_cnt}, {$sql_limit}", 1);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;

			$tpl->load_template('public_audio/search_result.tpl');

			$jid = intval($page_cnt);
			
			if($sql_){
			
				if(!$page_cnt)
					$tpl->result['content'] .= "<script>langNumric('langNumric', '{$row_count['cnt']}', 'аудиозапись', 'аудиозаписи', 'аудиозаписей', 'аудиозапись', 'аудиозаписей');</script><div class=\"allbar_title\" style=\"margin-bottom:0px\">В поиске найдено <span id=\"seAudioNum\">{$row_count['cnt']}</span> <span id=\"langNumric\"></span> | <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a> | <a href=\"/\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все аудиозаписи</a></div>";
			
				foreach($sql_ as $row){
					$jid++;
					$tpl->set('{jid}', $jid);
					$tpl->set('{aid}', $row['aid']);
					$tpl->set('{url}', $row['url']);
					$tpl->set('{artist}', stripslashes($row['artist']));
					$tpl->set('{name}', stripslashes($row['name']));
					$tpl->set('{author-n}', iconv_substr($row['user_search_pref'], 0, 1, 'utf-8'));
					$expName = explode(' ', $row['user_search_pref']);
					$tpl->set('{author-f}', $expName[1]);
					$tpl->set('{author-id}', $row['auser_id']);
					
					//Права админа
					if($public_admin){
						$tpl->set('[admin-group]', '');
						$tpl->set('[/admin-group]', '');
						$tpl->set_block("'\\[all-users\\](.*?)\\[/all-users\\]'si","");
					} else {
						$tpl->set_block("'\\[admin-group\\](.*?)\\[/admin-group\\]'si","");
						$tpl->set('[all-users]', '');
						$tpl->set('[/all-users]', '');
					}
					
					$tpl->compile('content');
				}
				
			} else {
				
				if(!$page_cnt){
				
					$tpl->result['info'] .= "<div class=\"allbar_title\">Нет аудиозаписей | <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a> | <a href=\"/\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все аудиозаписи</a></div>";
				
					msgbox('', '<br /><br /><br />По запросу <b>'.stripslashes($query).'</b> не найдено ни одной аудиозаписи<br /><br /><br />', 'info_2');
				
				}
			}
			
			AjaxTpl();
			
			exit;
			
		break;
		
		//################### Страница всех аудио ###################//
		default:
			
			$metatags['title'] = 'Аудиозаписи сообщества';
			
			$pid = intval($_GET['pid']);
			
			$sql_limit = 20;
			
			if($_POST['page'] > 0) $page_cnt = intval($_POST['page'])*$sql_limit;
			else $page_cnt = 0;
			
			if($page_cnt)
				NoAjaxQuery();
			
			$sql_ = $db->super_query("SELECT aid, url, artist, name FROM `".PREFIX."_communities_audio` WHERE public_id = '{$pid}' ORDER by `adate` DESC LIMIT {$page_cnt}, {$sql_limit}", 1);

			$infoGroup = $db->super_query("SELECT audio_num, adres, admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(!$page_cnt){
				$tpl->load_template('public_audio/top.tpl');
				$tpl->set('{pid}', $pid);
					
				if($infoGroup['adres']) $tpl->set('{adres}', $infoGroup['adres']);
				else $tpl->set('{adres}', 'public'.$pid);
						
				if($infoGroup['audio_num']) $tpl->set('{audio-num}', $infoGroup['audio_num'].' <span id="langNumricAll"></span>');
				else $tpl->set('{audio-num}', 'Нет аудиозаписей');
					
				$tpl->set('{x-audio-num}', $infoGroup['audio_num']);
					
				if(!$infoGroup['audio_num']){
					$tpl->set('[no]', '');
					$tpl->set('[/no]', '');
				} else
					$tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si","");
				
				$tpl->compile('info');
			}
			
			if($sql_){

				$jid = intval($page_cnt);
				
				if(stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
				else $public_admin = false;
			
				$tpl->load_template('public_audio/track.tpl');
				
				$tpl->result['content'] .= '<div id="allGrAudis">';
				
				foreach($sql_ as $row){
					$jid++;
					$tpl->set('{jid}', $jid);
					$tpl->set('{pid}', $pid);
					$tpl->set('{aid}', $row['aid']);
					$tpl->set('{url}', $row['url']);
					$tpl->set('{artist}', stripslashes($row['artist']));
					$tpl->set('{name}', stripslashes($row['name']));
					
					//Права админа
					if($public_admin){
						$tpl->set('[admin-group]', '');
						$tpl->set('[/admin-group]', '');
						$tpl->set_block("'\\[all-users\\](.*?)\\[/all-users\\]'si","");
					} else {
						$tpl->set_block("'\\[admin-group\\](.*?)\\[/admin-group\\]'si","");
						$tpl->set('[all-users]', '');
						$tpl->set('[/all-users]', '');
					}
					
					$tpl->compile('content');
				}
				

				if($infoGroup['audio_num'] > $sql_limit AND !$page_cnt)
					$tpl->result['content'] .= '<div id="ListAudioAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="ListAudioAddedLoadAjax()" id="wall_l_href_se_audiox"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_loadx">Показать больше аудиозаписей</div></div>';
				
				$tpl->result['content'] .= '</div>';
				
			}
			
			if($page_cnt){
				AjaxTpl();
				exit;
			}
			
	}
	
	$tpl->clear();
	$db->free();
	
} else {
	$user_speedbar = 'Информация';
	msgbox('', $lang['not_logged'], 'info');
}

?>