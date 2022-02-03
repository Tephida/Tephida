<?php
/* 
	Appointment: Сообщества -> Публичные страницы -> Видеозаписи
	File: public_videos.php 
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
		
		//################### Добавление видеозаписи в сообщество ###################//
		case "add":
			
			NoAjaxQuery();
			
			$pid = intval($_POST['pid']);
			$id = intval($_POST['id']);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			$row = $db->super_query("SELECT video, photo, title, descr FROM `".PREFIX."_videos` WHERE id = '{$id}'");
			
			if($public_admin AND $row){
				
				//Директория загрузки фото
				$upload_dir = ROOT_DIR.'/uploads/videos/'.$user_id;
						
				//Если нет папки юзера, то создаём её
				if(!is_dir($upload_dir)){ 
				
					@mkdir($upload_dir, 0777);
					@chmod($upload_dir, 0777);
					
				}

				$img_name_arr = end(explode(".", $row['photo']));
				$expPhoto = substr(md5(time().md5($row['photo'])), 0, 15).'.'.$img_name_arr;
				@copy($row['photo'], ROOT_DIR."/uploads/videos/{$user_id}/{$expPhoto}");
				
				$newPhoto = "{$config['home_url']}uploads/videos/{$user_id}/{$expPhoto}";
				
				$row['video'] = $db->safesql($row['video']);
				$row['descr'] = $db->safesql($row['descr']);
				$row['title'] = $db->safesql($row['title']);
				
				$db->query("INSERT INTO `".PREFIX."_videos` SET public_id = '{$pid}', owner_user_id = '{$user_id}', video = '{$row['video']}', photo = '{$newPhoto}', title = '{$row['title']}', descr = '{$row['descr']}', add_date = NOW(), privacy = '1'");

				$db->query("UPDATE `".PREFIX."_communities` SET videos_num = videos_num + 1 WHERE id = '{$pid}'");
				
				mozg_clear_cache_file("groups/video{$pid}");
				
			}
			
			die();
			
		break;
		
		//################### Удаление видео ###################//
		case "del":
			
			NoAjaxQuery();
			
			$pid = intval($_POST['pid']);
			$id = intval($_POST['id']);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			$row = $db->super_query("SELECT photo, public_id, owner_user_id FROM `".PREFIX."_videos` WHERE id = '{$id}'");
			
			if($public_admin AND $row['public_id'] == $pid){
				
				//Директория загрузки фото
				$upload_dir = ROOT_DIR.'/uploads/videos/'.$row['owner_user_id'];
				
				$expPho = end(explode('/', $row['photo']));
				@unlink($upload_dir.'/'.$expPho);
				
				$db->query("DELETE FROM `".PREFIX."_videos` WHERE id = '{$id}'");

				$db->query("UPDATE `".PREFIX."_communities` SET videos_num = videos_num - 1 WHERE id = '{$pid}'");
				
				mozg_clear_cache_file("groups/video{$pid}");
				
			}
			
			die();
			
		break;
		
		//################### Окно редактирования видео ###################//
		case "edit":
			
			NoAjaxQuery();
			
			$pid = intval($_POST['pid']);
			$id = intval($_POST['id']);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			$row = $db->super_query("SELECT public_id, title, descr FROM `".PREFIX."_videos` WHERE id = '{$id}'");
			
			if($public_admin AND $row['public_id'] == $pid){
				
				$tpl->load_template('public_videos/edit.tpl');
				$tpl->set('{title}', stripslashes($row['title']));
				$tpl->set('{descr}', myBrRn(stripslashes($row['descr'])));
				$tpl->compile('content');

				AjaxTpl();
				
			}
			
			die();
			
		break;
		
		//################### Сохранение отред. данных ###################//
		case "edit_save":
			
			NoAjaxQuery();
			
			$pid = intval($_POST['pid']);
			$id = intval($_POST['id']);
			
			$title = ajax_utf8(textFilter($_POST['title'], false, true));
			$descr = ajax_utf8(textFilter($_POST['descr'], 3000));
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
			
			$row = $db->super_query("SELECT public_id FROM `".PREFIX."_videos` WHERE id = '{$id}'");
			
			if($public_admin AND $row['public_id'] == $pid AND isset($title) AND !empty($title)){
				
				$db->query("UPDATE `".PREFIX."_videos` SET title = '{$title}', descr = '{$descr}' WHERE id = '{$id}'");
				
				echo stripslashes($descr);
				
				mozg_clear_cache_file("groups/video{$pid}");
				
			}
			
			die();
			
		break;
		
		//################### Поиск по видеозаписям ###################//
		case "search":
		
			NoAjaxQuery();
			
			$sql_limit = 20;
			
			if($_POST['page'] > 0) $page_cnt = intval($_POST['page'])*$sql_limit;
			else $page_cnt = 0;
			
			$pid = intval($_POST['pid']);
	
			$query = $db->safesql(ajax_utf8(strip_data($_POST['query'])));
			$query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
			
			$adres = strip_tags($_POST['adres']);
			
			$row_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_videos` WHERE title LIKE '%{$query}%' AND public_id = '0'");
			
			$sql_ = $db->super_query("SELECT id, owner_user_id, title, descr, photo, comm_num, add_date FROM `".PREFIX."_videos` WHERE title LIKE '%{$query}%' AND public_id = '0' ORDER by `add_date` DESC LIMIT {$page_cnt}, {$sql_limit}", 1);
			
			$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;

			$tpl->load_template('public_videos/search_result.tpl');

			if($sql_){
			
				if(!$page_cnt)
					$tpl->result['content'] .= "<script>langNumric('langNumric', '{$row_count['cnt']}', 'видеозапись', 'видеозаписи', 'видеозаписей', 'видеозапись', 'видеозаписей');</script><div class=\"allbar_title\" style=\"margin-bottom:0px;border-bottom:0px\">В поиске найдено <span id=\"seAudioNum\">{$row_count['cnt']}</span> <span id=\"langNumric\"></span>  |  <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a>  |  <a href=\"/public/videos{$pid}\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все видеозаписи</a></div>";
			
				foreach($sql_ as $row){

					$tpl->set('{photo}', stripslashes($row['photo']));
					$tpl->set('{title}', stripslashes($row['title']));
					$tpl->set('{id}', $row['id']);
					$tpl->set('{pid}', $pid);
					$tpl->set('{user-id}', $row['owner_user_id']);
					
					if($row['descr'])
						$tpl->set('{descr}', stripslashes($row['descr']).'...');
					else
						$tpl->set('{descr}', '');
						
					$tpl->set('{comm}', $row['comm_num'].' '.gram_record($row['comm_num'], 'comments'));
					megaDate(strtotime($row['add_date']));
					
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
				
					$tpl->result['info'] .= "<div class=\"allbar_title\">Нет видеозаписей  |  <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a>  |  <a href=\"/public/videos{$pid}\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все видеозаписи</a></div>";
				
					msgbox('', '<br /><br /><br />По запросу <b>'.stripslashes($query).'</b> не найдено ни одной видеозаписи.<br /><br /><br />', 'info_2');
				
				}
			}
			
			AjaxTpl();

			die();
			
		break;
		
		//################### Страница всех видео ###################//
		default:
			
			$metatags['title'] = 'Видеозаписи сообщества';
			
			$pid = intval($_GET['pid']);
			
			$sql_limit = 20;
			
			if($_POST['page'] > 0) $page_cnt = intval($_POST['page']) * $sql_limit;
			else $page_cnt = 0;
			
			if($page_cnt)
				NoAjaxQuery();
			
			$infoGroup = $db->super_query("SELECT videos_num, adres, admin FROM `".PREFIX."_communities` WHERE id = '{$pid}'");
			
			if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
			else $public_admin = false;
				
			if($infoGroup['videos_num']){
			
				$sql_ = $db->super_query("SELECT id, photo, title, descr, comm_num, add_date, owner_user_id FROM `".PREFIX."_videos` WHERE public_id = '{$pid}' ORDER by `add_date` DESC LIMIT {$page_cnt}, {$sql_limit}", 1);
				
				if($sql_){

					$tpl->load_template('public_videos/video.tpl');
					
					$tpl->result['content'] .= '<div id="allGrAudis">';
				
					foreach($sql_ as $row){

						$tpl->set('{photo}', stripslashes($row['photo']));
						$tpl->set('{title}', stripslashes($row['title']));
						$tpl->set('{id}', $row['id']);
						$tpl->set('{pid}', $pid);
						$tpl->set('{user-id}', $row['owner_user_id']);
						
						if($row['descr'])
							$tpl->set('{descr}', stripslashes($row['descr']).'...');
						else
							$tpl->set('{descr}', '');
							
						$tpl->set('{comm}', $row['comm_num'].' '.gram_record($row['comm_num'], 'comments'));
						megaDate(strtotime($row['add_date']));
						
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
					
					if($infoGroup['videos_num'] > $sql_limit AND !$page_cnt)
						$tpl->result['content'] .= '<div id="ListAudioAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="ListAudioAddedLoadAjax()" id="wall_l_href_se_audiox"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_loadx">Показать больше видеозаписей</div></div>';
						
					$tpl->result['content'] .= '</div>';
				
				}
				
			}
				
			if(!$page_cnt){
				
				$tpl->load_template('public_videos/top.tpl');
				$tpl->set('{pid}', $pid);
					
				if($infoGroup['adres']) $tpl->set('{adres}', $infoGroup['adres']);
				else $tpl->set('{adres}', 'public'.$pid);
						
				if($infoGroup['videos_num']) $tpl->set('{videos-num}', $infoGroup['videos_num'].' <span id="langNumricAll"></span>');
				else $tpl->set('{videos-num}', 'Нет видеозаписей');
					
				$tpl->set('{x-videos-num}', $infoGroup['videos_num']);
					
				if(!$infoGroup['videos_num']){
				
					$tpl->set('[no]', '');
					$tpl->set('[/no]', '');
					$tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si","");
					
				} else {
				
					$tpl->set('[yes]', '');
					$tpl->set('[/yes]', '');
					$tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si","");
				}
				
				$tpl->compile('info');
				
			}
			
			if($page_cnt){
			
				AjaxTpl();
				die();
				
			}
			
	}
	
	$tpl->clear();
	$db->free();
	
} else {

	$user_speedbar = 'Информация';
	msgbox('', $lang['not_logged'], 'info');
	
}
?>