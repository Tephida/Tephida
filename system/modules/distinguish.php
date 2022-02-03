<?php
/* 
	Appointment: Выделить человека на фото
	File: distinguish.php 
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
	
		//################### Отмечаем человека на фото ###################//
		case "mark":
			$i_left = intval($_POST['i_left']); if($i_left < 0) $i_left = 0;
			$i_top = intval($_POST['i_top']); if($i_top < 0) $i_top = 0;
			$i_width = intval($_POST['i_width']); if($i_width < 0) $i_width = 0;
			$i_height = intval($_POST['i_height']); if($i_height < 0) $i_height = 0;
			$photo_id = intval($_POST['photo_id']);
			$muser_id = intval($_POST['user_id']);
			$mphoto_name = ajax_utf8(strip_data(textFilter($_POST['user_name'], false, true)));
			$msettings_pos = $i_left.", ".$i_top.", ".$i_width.", ".$i_height;
			if($user_id == $muser_id) $approve = 1;
			else $approve = 0;
			
			if($mphoto_name AND $muser_id == 0)
				$row_no = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND mphoto_name = '".$mphoto_name."'");
			else
				$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_photos_mark` WHERE muser_id = '".$muser_id."' AND mphoto_id = '".$photo_id."'");
			
			if($row['cnt'])
				$db->query("UPDATE `".PREFIX."_photos_mark` SET msettings_pos = '".$msettings_pos."' WHERE muser_id = '".$muser_id."' AND mphoto_id = '".$photo_id."'");
			elseif($row_no['cnt'])
				$db->query("UPDATE `".PREFIX."_photos_mark` SET msettings_pos = '".$msettings_pos."' WHERE mphoto_id = '".$photo_id."' AND mphoto_name = '".$mphoto_name."'");
			else
				if($_POST['user_ok'] == 'yes'){
					$db->query("INSERT INTO `".PREFIX."_photos_mark` SET muser_id = '".$muser_id."', mphoto_id = '".$photo_id."', mdate = '".$server_time."', msettings_pos = '".$msettings_pos."', mapprove = '".$approve."', mmark_user_id = '".$user_id."'");
					
					if($user_id != $muser_id)
						$db->query("UPDATE `".PREFIX."_users` SET user_new_mark_photos = user_new_mark_photos+1 WHERE user_id = '".$muser_id."'");			
				} else
					$db->query("INSERT INTO `".PREFIX."_photos_mark` SET muser_id = '".rand(0, 100000)."', mphoto_id = '".$photo_id."', mdate = '".$server_time."', msettings_pos = '".$msettings_pos."', mphoto_name = '".$mphoto_name."', mmark_user_id = '".$user_id."', mapprove = 1");
			
			mozg_clear_cache_file('photos_mark/p'.$photo_id);
		break;
		
		//################### Удаление отметки ###################//
		case "mark_del":
			$photo_id = intval($_POST['photo_id']);
			$muser_id = intval($_POST['user_id']);
			$mphoto_name = ajax_utf8(strip_data(textFilter($_POST['user_name'], false, true)));
			$row = $db->super_query("SELECT user_id FROM `".PREFIX."_photos` WHERE id = '".$photo_id."'");
			
			if($mphoto_name AND $muser_id == 0)
				$row_mark = $db->super_query("SELECT mmark_user_id FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND mphoto_name = '".$mphoto_name."'");
			else
				$row_mark = $db->super_query("SELECT mmark_user_id, mapprove FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND muser_id = '".$muser_id."'");
			
			if($row['user_id'] == $user_id OR $user_id == $muser_id OR $user_id == $row_mark['mmark_user_id']){
				if($mphoto_name AND $muser_id == 0)
					$db->query("DELETE FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND mphoto_name = '".$mphoto_name."'");
				else {
					$db->query("DELETE FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND muser_id = '".$muser_id."' AND mphoto_name = ''");
					
					if(!$row_mark['mapprove'])
						$db->query("UPDATE `".PREFIX."_users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '".$muser_id."'");
				}
				mozg_clear_cache_file('photos_mark/p'.$photo_id);
			}
		break;
		
		//################### Потверждение отметки ###################//
		case "mark_ok":
			$photo_id = intval($_POST['photo_id']);
			$row = $db->super_query("SELECT mapprove FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$photo_id."' AND muser_id = '".$user_id."'");
			if($row AND !$row['mapprove']){
				$db->query("UPDATE `".PREFIX."_photos_mark` SET mapprove = '1' WHERE mphoto_id = '".$photo_id."' AND muser_id = '".$user_id."'");
				$db->query("UPDATE `".PREFIX."_users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '".$user_id."'");
				mozg_clear_cache_file('photos_mark/p'.$photo_id);
			}
		break;
		
		//################### Загрузка 110 друзей из списка ###################//
		case "load_friends":
			$photo_id = intval($_POST['photo_id']);
			$all_limit = 110;
			if($_POST['page'] == 2) $limit = $all_limit.", ".($all_limit*2);
			else $limit = "0, ".$all_limit;
				
			$sql_ = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '".$user_id."' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 ORDER by `user_search_pref` ASC LIMIT ".$limit, 1);
			
			$myRow = $db->super_query("SELECT user_search_pref FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
			
			if($sql_){
				$cnt = 0;
				foreach($sql_ as $row){
					$friend .= '<tr id="user_'.$row['friend_id'].'" class="echoUsersList"><td width="170"><div onClick="Distinguish.SelectUser('.$row['friend_id'].', \''.$row['user_search_pref'].'\', '.$photo_id.')">'.$row['user_search_pref'].'</div></td></tr>';
					$cnt++;
				}
				
				if($cnt == $all_limit AND !$_POST['page'])
					$added_script = "setTimeout('Distinguish.FriendPage(2, ".$photo_id."')', 2500)";
	
			}
			
			echo <<<HTML
<script type="text/javascript" src="/templates/{$config['temp']}/js/fave.filter.js"></script>
<script type="text/javascript">
{$added_script}
</script>
<table class="food_planner" id="fave_users">
<tr id="user_{$user_id}"><td width="170"><div onClick="Distinguish.SelectUser({$user_id}, '{$myRow['user_search_pref']}', {$photo_id})">Я</div></td></tr>
{$friend}
</table>
HTML;

		break;
	}
	$tpl->clear();
	$db->free();
} else
	echo 'no_log';

die();
?>