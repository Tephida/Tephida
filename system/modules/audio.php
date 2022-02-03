<?php
/* 
	Appointment: Музыка
	File: audio.php 
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
	$act = $_GET['act'];
	$user_id = $user_info['user_id'];

	$metatags['title'] = $lang['audio'];
	
	switch($act){
		
		//################### Отправление песни в БД ###################//
		case "send":
			NoAjaxQuery();
			$lnk = ajax_utf8(textFilter($_POST['lnk']));
			$format = strtolower(end(explode('.', $lnk)));
			
			if($format == 'mp3' AND isset($lnk) AND !empty($lnk) AND $config['audio_mod_add'] == 'yes'){
				$check_url = @get_headers(stripslashes($lnk));
				if(strpos($check_url[0], '200')){
					//Узнаем исполнителя и название песни по id3
					$ranTmp = rand(0, 100500);
					
					$fp = @fopen(stripslashes($lnk), "rb");
					$fd = @fopen(ROOT_DIR.'/uploads/audio_tmp/'.$ranTmp.'.mp3', "w");
					if($fp AND $fd){
						$st = fread($fp, 4096);
						fwrite($fd, $st);
					}
					@fclose($fp);
					@fclose($fd);

					include ENGINE_DIR."/classes/id3v2.php";
					$id3v2 = new Id3v2;
					$res = $id3v2->read(ROOT_DIR.'/uploads/audio_tmp/'.$ranTmp.'.mp3');
					
					$artist = ajax_utf8(textFilter($res['Artist'], false, true));
					$name = ajax_utf8(textFilter($res['Title'], false, true));
					if(isset($artist) AND empty($artist)) $artist = 'Неизвестный исполнитель';
					if(isset($name) AND empty($name)) $name = 'Без названия';
				
					$db->query("INSERT INTO `".PREFIX."_audio` SET auser_id = '".$user_id."', url = '".$lnk."', artist = '".$artist."', name = '".$name."',  adate = '".$server_time."'");
					$db->query("UPDATE `".PREFIX."_users` SET user_audio = user_audio+1 WHERE user_id = '".$user_id."'");
					
					@unlink(ROOT_DIR.'/uploads/audio_tmp/'.$ranTmp.'.mp3');
					mozg_mass_clear_cache_file('user_'.$user_id.'/audios_profile|user_'.$user_id.'/profile_'.$user_id);
				} else
					echo 2;
			} else
				echo 1;

			die();
		break;
		
		//################### Сохранение отредактированых данных ###################//
		case "editsave":
			NoAjaxQuery();
			$aid = intval($_POST['aid']);
			$artist = ajax_utf8(textFilter($_POST['artist'], false, true));
			$name = ajax_utf8(textFilter($_POST['name'], false, true));
			
			$check = $db->super_query("SELECT auser_id FROM `".PREFIX."_audio` WHERE aid = '".$aid."'");
			
			if(isset($artist) AND empty($artist)) $artist = 'Неизвестный исполнитель';
			if(isset($name) AND empty($name)) $name = 'Без названия';

			if($check['auser_id'] == $user_id){
				$db->query("UPDATE `".PREFIX."_audio` SET artist = '{$artist}', name = '{$name}' WHERE aid = '".$aid."'");
				mozg_clear_cache_file('user_'.$user_id.'/audios_profile');
			}
			die();
		break;
		
		//################### Удаление песни из БД ###################//
		case "del":
			NoAjaxQuery();
			$aid = intval($_POST['aid']);
			
			$check = $db->super_query("SELECT auser_id, url FROM `".PREFIX."_audio` WHERE aid = '".$aid."'");
			
			if($check['auser_id'] == $user_id){
				$audioName = end(explode('/', $check['url']));
				$checkMusSite = explode('http://', $check['url']);
				$expMusO = explode('/', $checkMusSite[1]);
				$checkMusSite2 = explode('http://', $config['home_url']);
				$expMusO2 = explode('/', $checkMusSite2[1]);
				if($expMusO[0] == $expMusO2[0])
					@unlink(ROOT_DIR.'/uploads/audio/'.$user_id.'/'.$audioName);
				
				$db->query("DELETE FROM `".PREFIX."_audio` WHERE aid = '".$aid."'");
				$db->query("UPDATE `".PREFIX."_users` SET user_audio = user_audio-1 WHERE user_id = '".$user_id."'");
				mozg_mass_clear_cache_file('user_'.$user_id.'/audios_profile|user_'.$user_id.'/profile_'.$user_id);
			}
			die();
		break;
		
		//################### Добавление песни к себе в список ###################//
		case "addmylist":
			NoAjaxQuery();
			$aid = intval($_POST['aid']);
			
			$check = $db->super_query("SELECT url, artist, name FROM `".PREFIX."_audio` WHERE aid = '".$aid."'");
			
			if(!$check)
				$check = $db->super_query("SELECT url, artist, name FROM `".PREFIX."_communities_audio` WHERE aid = '".$aid."'");
			
			if($check){
				$db->query("INSERT INTO `".PREFIX."_audio` SET auser_id = '".$user_id."', url = '".$db->safesql($check['url'])."', artist = '".$db->safesql($check['artist'])."', name = '".$db->safesql($check['name'])."',  adate = '".$server_time."'");
				$db->query("UPDATE `".PREFIX."_users` SET user_audio = user_audio+1 WHERE user_id = '".$user_id."'");
				mozg_mass_clear_cache_file('user_'.$user_id.'/audios_profile|user_'.$user_id.'/profile_'.$user_id);
			}
			die();
		break;

		//################### Вывод всех аудио (BOX) ###################//
		case "allMyAudiosBox":
			NoAjaxQuery();
			
			//Для навигатор
			if($_POST['page'] > 0) $page = intval($_POST['page']); else $page = 1;
			$gcount = 20;
			$limit_page = ($page-1)*$gcount;
			
			//Делаем SQL запрос на вывод
			$sql_ = $db->super_query("SELECT aid, url, artist, name FROM `".PREFIX."_audio` WHERE auser_id = '".$user_id."' ORDER by `adate` DESC LIMIT {$limit_page}, {$gcount}", 1);
			
			//Выводим кол-во музыки
			$count = $db->super_query("SELECT user_audio FROM `".PREFIX."_users` WHERE user_id = '".$user_id."'");
			
			if($count['user_audio']){
				echo '<div id="jquery_jplayer"></div><input type="hidden" id="teck_id" value="0" /><input type="hidden" id="typePlay" value="standart" />';
				$tpl->load_template('albums_editcover.tpl');
				$tpl->set('[top]', '');
				$tpl->set('[/top]', '');
				$tpl->set('{photo-num}', $count['user_audio'].' '.gram_record($count['user_audio'], 'audio'));
				$tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si","");
				$tpl->compile('content');
				
				$tpl->load_template('audio/track_box.tpl');
				$jid = 0;
				foreach($sql_ as $row){
					$jid++;
					$tpl->set('{jid}', $jid);
					$tpl->set('{aid}', $row['aid']);
					$tpl->set('{url}', $row['url']);
					$tpl->set('{artist}', stripslashes($row['artist']));
					$tpl->set('{name}', stripslashes($row['name']));
					if($get_user_id == $user_id){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
						$tpl->set('{uid}', $get_user_id);
						$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
					} else {
						$tpl->set('[not-owner]', '');
						$tpl->set('[/not-owner]', '');
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					}
					$tpl->compile('content');
				}
				box_navigation($gcount, $count['user_audio'], $page, 'wall.attach_addaudio', '');
				
				$tpl->load_template('albums_editcover.tpl');
				$tpl->set('[bottom]', '');
				$tpl->set('[/bottom]', '');
				$tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si","");
				$tpl->compile('content');
				
				AjaxTpl();
			} else
				echo $lang['audio_box_none'];
			
			die();
		break;

		//################### Загрузка с компьютера ###################//
		case "upload":
			NoAjaxQuery();

			//Получаем данные о файле
			$file_tmp = $_FILES['uploadfile']['tmp_name'];
			$file_name = totranslit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
			$file_rename = substr(md5($server_time+rand(1,100000)), 0, 15); // имя
			$file_size = $_FILES['uploadfile']['size']; // размер файла
			$type = strtolower(end(explode(".", $file_name))); // формат файла
			
			if($type == 'mp3' AND $config['audio_mod_add'] == 'yes' AND $file_size < 10000000){
				$audio_dir = ROOT_DIR.'/uploads/audio/'.$user_id.'/';
				if(!is_dir($audio_dir)){
					@mkdir($audio_dir, 0777);
					@chmod($audio_dir, 0777);
				}
				$res_type = '.'.$type;
				if(move_uploaded_file($file_tmp, $audio_dir.$file_rename.$res_type)){
					//Узнаем исполнителя и название песни по id3
					include ENGINE_DIR."/classes/id3v2.php";
					$id3v2 = new Id3v2;
					$res = $id3v2->read(ROOT_DIR.'/uploads/audio/'.$user_id.'/'.$file_rename.$res_type);
					
					$artist = ajax_utf8(textFilter($res['Artist'], false, true));
					$name = ajax_utf8(textFilter($res['Title'], false, true));
					if(isset($artist) AND empty($artist)) $artist = 'Неизвестный исполнитель';
					if(isset($name) AND empty($name)) $name = 'Без названия';
					
					$lnk = $config['home_url'].'uploads/audio/'.$user_id.'/'.$file_rename.$res_type;
					
					$db->query("INSERT INTO `".PREFIX."_audio` SET auser_id = '".$user_id."', url = '".$lnk."', artist = '".$artist."', name = '".$name."',  adate = '".$server_time."'");
					$db->query("UPDATE `".PREFIX."_users` SET user_audio = user_audio+1 WHERE user_id = '".$user_id."'");

					mozg_mass_clear_cache_file('user_'.$user_id.'/audios_profile|user_'.$user_id.'/profile_'.$user_id);
				} else
					echo 1;
			} else
				echo 1;

			die();
		break;
		
		default:
		
			//################### Вывод всех аудио ###################//
			
			$uid = intval($_GET['uid']);
			
			$tpl->load_template('audio/head.tpl');
			$tpl->set('{user-id}', $uid);
			$tpl->compile('content');

	}
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>