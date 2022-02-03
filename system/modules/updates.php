<?php
/* 
	Appointment: Моментальные оповещания
	File: updates.php 
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

	$user_id = $user_info['user_id'];
	
	$cntCacheUp = mozg_cache("user_{$user_id}/updates");
	
	if($cntCacheUp){
	
		$update_time = $server_time - 70;
		
		$row = $db->super_query("SELECT id, type, from_user_id, text, lnk, user_search_pref, user_photo FROM `".PREFIX."_updates` WHERE for_user_id = '{$user_id}' AND date > '{$update_time}' ORDER by `date` ASC");
		
		if($row){
		
			if($row['user_photo']) $ava = "/uploads/users/{$row['from_user_id']}/50_{$row['user_photo']}";
			else $ava = "/templates/Default/images/no_ava_50.png";
			
			$row['text'] = str_replace("|", "&#124;", $row['text']);
			
			echo $row['type'].'|'.$row['user_search_pref'].'|'.$row['from_user_id'].'|'.stripslashes($row['text']).'|'.$server_time.'|'.$ava.'|'.$row['lnk'];
					
			$db->query("DELETE FROM `".PREFIX."_updates` WHERE id = '{$row['id']}'");
			
		} else
		
			mozg_create_cache("user_{$user_id}/updates", '');
	
	}

}

die();
?>