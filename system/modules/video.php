<?php
/* 
	Appointment: Просмотр видео и комментари к видео
	File: video.php 
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

$vid = intval($_POST['vid']);
$close_link = $_POST['close_link'];

//Выводи данные о видео если оно есть
$row = $db->super_query("SELECT tb1.video, title, add_date, descr, owner_user_id, views, comm_num, privacy, public_id, tb2.user_search_pref FROM `".PREFIX."_videos` tb1, `".PREFIX."_users` tb2 WHERE tb1.id = '{$vid}' AND tb1.owner_user_id = tb2.user_id");

if($row){
	//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
	if($user_id != $get_user_id)
		$check_friend = CheckFriends($row['owner_user_id']);
	
	//Blacklist
	$CheckBlackList = CheckBlackList($row['owner_user_id']);
	
	//Приватность
	if(!$CheckBlackList AND $row['privacy'] == 1 OR $row['privacy'] == 2 AND $check_friend OR $user_info['user_id'] == $row['owner_user_id'])
		$privacy = true;
	else
		$privacy = false;
	
	if($privacy){
		//Выводим комментарии если они есть
		if($row['comm_num'] AND $config['video_mod_comm'] == 'yes'){
			
			if($row['public_id']){
			
				$infoGroup = $db->super_query("SELECT admin FROM `".PREFIX."_communities` WHERE id = '{$row['public_id']}'");
				
				if(strpos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
				else $public_admin = false;
			
			}
			
			if($row['comm_num'] > 3) 
				$limit_comm = $row['comm_num']-3;
			else 
				$limit_comm = 0;
								
			$sql_comm = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `".PREFIX."_videos_comments` tb1, `".PREFIX."_users` tb2 WHERE tb1.video_id = '{$vid}' AND tb1.author_user_id = tb2.user_id ORDER by `add_date` ASC LIMIT {$limit_comm}, {$row['comm_num']}", 1);
			$tpl->load_template('videos/comment.tpl');
			foreach($sql_comm as $row_comm){

				OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
				
				$tpl->set('{uid}', $row_comm['author_user_id']);
				$tpl->set('{author}', $row_comm['user_search_pref']);
				$tpl->set('{comment}', stripslashes($row_comm['text']));
				$tpl->set('{id}', $row_comm['id']);
				megaDate(strtotime($row_comm['add_date']));
				
				if($row_comm['author_user_id'] == $user_id || $row['owner_user_id'] == $user_id || $public_admin){
					$tpl->set('[owner]', '');
					$tpl->set('[/owner]', '');
				} else
					$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");

				if($row_comm['user_photo'])
					$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_comm['author_user_id'].'/50_'.$row_comm['user_photo']);
				else
					$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
				$tpl->compile('comments');
			}
		}

		$tpl->load_template('videos/full.tpl');
		$tpl->set('{vid}', $vid);
		$tpl->set('{video}', $row['video']);
		if($row['views']) $tpl->set('{views}', $row['views'].' '.gram_record($row['views'], 'video_views').'<br /><br />'); else $tpl->set('{views}', '');
		$tpl->set('{title}', stripslashes($row['title']));
		$tpl->set('{descr}', stripslashes($row['descr']));
		$tpl->set('{author}', $row['user_search_pref']);
		$tpl->set('{uid}', $row['owner_user_id']);
		$tpl->set('{comments}', $tpl->result['comments']);
		$tpl->set('{comm-num}', $row['comm_num']);
		$tpl->set('{owner-id}', $row['owner_user_id']);
		$tpl->set('{close-link}', $close_link);
		megaDate(strtotime($row['add_date']));

		if($row['owner_user_id'] == $user_id){
			$tpl->set('[owner]', '');
			$tpl->set('[/owner]', '');
			$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
		} else {
			$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
			$tpl->set('[not-owner]', '');
			$tpl->set('[/not-owner]', '');
		}
		
		if($row['public_id']){
			
			$tpl->set_block("'\\[public\\](.*?)\\[/public\\]'si","");
			
		} else {
			
			$tpl->set('[public]', '');
			$tpl->set('[/public]', '');
			
		}
		
		if($config['video_mod_add_my'] == 'no')
			$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
		
		$tpl->set('{prev-text-comm}', gram_record(($row['comm_num']-3), 'prev').' '.($row['comm_num']-3).' '.gram_record(($row['comm_num']-3), 'comments'));
		if($row['comm_num'] < 4)
			$tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si","");
		else {
			$tpl->set('[all-comm]', '');
			$tpl->set('[/all-comm]', '');
		}
		
		if($config['video_mod_comm'] == 'yes'){
			$tpl->set('[admin-comments]', '');
			$tpl->set('[/admin-comments]', '');
		} else
			$tpl->set_block("'\\[admin-comments\\](.*?)\\[/admin-comments\\]'si","");
			
		$tpl->compile('content');
		AjaxTpl();
		
		$db->query("UPDATE LOW_PRIORITY `".PREFIX."_videos` SET views = views+1 WHERE id = '".$vid."'");
	} else
		echo 'err_privacy';
} else
	echo 'no_video';
?>