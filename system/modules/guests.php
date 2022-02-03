<?php
/* 
	Appointment: Гости
	File: guests.php 
	Author: f0rt1 
	Engine: Mozg CMS (Social version)
	Copyright: www.niceweb.in.ua 
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

if($ajax == 'yes')
	NoAjaxQuery();

if($logged){
	$user_id = $user_info['user_id'];

	$metatags['title'] = 'Гости';
	$user_speedbar = 'Гости';
	
	if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
	$gcount = 1;
	$limit_page = ($page-1)*$gcount;
	
	$sql_ = $db->super_query("SELECT SQL_CALC_FOUND_ROWS tb1.guid, gdate, new, tb2.user_search_pref, user_photo FROM `".PREFIX."_guests` tb1, `".PREFIX."_users` tb2 WHERE tb1.guid = tb2.user_id AND tb1.ouid = '{$user_id}' ORDER by `gdate` DESC LIMIT {$limit_page}, {$gcount}", 1);
	
	if($sql_){
	
		$num = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_guests` WHERE ouid = '{$user_id}'");
	
		$tpl->load_template('guests/user.tpl');
		
		foreach($sql_ as $row){
		
			$tpl->set('{name}', $row['user_search_pref']);
			$tpl->set('{user-id}', $row['guid']);

			if($row['new'])
				$tpl->set('{color}', 'style="color:#222"');
			else
				$tpl->set('{color}', '');
			
			megaDate($row['gdate']);
			
			if($row['user_photo'])
				$tpl->set('{ava}', "/uploads/users/{$row['guid']}/100_{$row['user_photo']}");
			else 
				$tpl->set('{ava}',  "{theme}/images/100_no_ava.png");
			
			$tpl->compile('content');
			
		}
		
		navigation($gcount, $num['cnt'], $config['home_url'].'?go=guests&page=');
		
		//Убераем новых гостей
		$db->query("UPDATE `".PREFIX."_guests` SET new = '0' WHERE ouid = '{$user_id}' AND new = '1'");
		
		$db->super_query("UPDATE `".PREFIX."_users` SET guests = '0' WHERE user_id = '{$user_id}'");
		
	} else
		msgbox('', '<br /><br />К Вам пока что никто не заходил.<br /><br /><br />', 'info_2');
	
	$tpl->clear();
	$db->free();
	
} else {

	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
	
}
?>