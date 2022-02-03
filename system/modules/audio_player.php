<?php
/* 
	Appointment: Плеер на всех страницах
	File: audio_player.php 
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
			
		//################### Загрузка плей листа ###################//
		default:
			
			//Если поиск
			$query = textFilter(ajax_utf8(strip_data(urldecode($_POST['query']))));
			$query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
			$doload = intval($_POST['doload']);
			
			$get_user_id = intval($_POST['get_user_id']);
			if($get_user_id == $user_id OR !$get_user_id) $get_user_id = $user_id;

			if(isset($query) AND !empty($query)){
				
				$sql_query = "WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%'";
				$search = true;
				
			} else {
				
				$sql_query = "WHERE auser_id = '{$get_user_id}'";
				$search = false;

			}

			//Выводим из БД
			$limit_select = 20;
			if($_POST['page_cnt'] > 0) $page_cnt = intval($_POST['page_cnt']) * $limit_select;
			else $page_cnt = 0;
			
			$sql_ = $db->super_query("SELECT aid, url, artist, name FROM `".PREFIX."_audio` {$sql_query} ORDER by `adate` DESC LIMIT {$page_cnt}, {$limit_select}", 1);
			
			//Если есть отвеот из БД
			if($sql_){
				
				$jid = $page_cnt;
				
				$tpl->load_template('audio_player/track.tpl');
				foreach($sql_ as $row){
				
					$jid++;
					$tpl->set('{jid}', $jid);
					
					$tpl->set('{aid}', $row['aid']);
					$tpl->set('{url}', $row['url']);
					$tpl->set('{artist}', stripslashes($row['artist']));
					$tpl->set('{name}', stripslashes($row['name']));
					
					if($get_user_id == $user_id AND !$search){
					
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
						$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
						
					} else {
					
						$tpl->set('[not-owner]', '');
						$tpl->set('[/not-owner]', '');
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					
					}
					
					$tpl->compile('audios');
					
				}
				
				if(!$page_cnt AND !$doload){
				
					$tpl->load_template('audio_player/player.tpl');
					
					$tpl->set('{audios}', $tpl->result['audios']);
					$tpl->set('{user-id}', $user_id);
					
					if($jid == $limit_select) $tpl->set('{jQbut}', '');
					else $tpl->set('{jQbut}', 'no_display');
					
					$tpl->compile('content');
				
				} else
					$tpl->result['content'] = $tpl->result['audios'];
			
			} else
				if($doload AND !$page_cnt){
					
					$query = str_replace('%', ' ', $query);
					
					$tpl->result['content'] = '<div class="info_center" style="padding-top:145px;padding-bottom:125px">По запросу <b>'.$query.'</b> не найдено ни одной аудиозаписи.</div>';
					
				} else 
				
					if(!$page_cnt)
						$tpl->result['content'] = '<div class="info_center" style="padding-top:145px;padding-bottom:125px"><center><img src="/templates/'.$config['temp'].'/images/snone.png" style="marign-bottom:60px;margin-top:-80px" /></center><div>Здесь Вы можете хранить Ваши аудиозаписи.<br />Для того, чтобы загрузить Вашу первую аудиозапись, <a href="/audio17" onClick="audio.addBox(1); return false;">нажмите здесь</a>.</div></div>';
				
			AjaxTpl();
			
	}
	
	$tpl->clear();
	$db->free();

}

exit();
?>