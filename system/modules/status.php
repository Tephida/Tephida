<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die('Hacking attempt!');

NoAjaxQuery();

if($logged){

	$user_id = $user_info['user_id'];
	$text = requestFilter('text', 25000, true);
	$public_id = intval($_POST['public_id']);
	
	//Если обновляем статус группы
	if($_GET['act'] == 'public'){
		
		$row = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$public_id}'");
		
		if(stripos($row['admin'], "u{$user_id}|") !== false){
		
			$db->query("UPDATE `".PREFIX."_communities` SET status_text = '{$text}' WHERE id = '{$public_id}'");
			mozg_clear_cache_folder('groups');
		
		}
	
	//Если пользователь
	} else {
	
		$db->query("UPDATE `".PREFIX."_users` SET user_status = '{$text}' WHERE user_id = '{$user_id}'");
		
		//Чистим кеш
		mozg_clear_cache_file('user_'.$user_id.'/profile_'.$user_id);
		mozg_clear_cache();
		
	}
	
	echo requestFilter('text');
}

die();
