<?php
/* 
	Appointment: Жалобы
	File: report.php
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

//Удаление жалобы
if($_GET['action'] == 'del'){
	$id = intval($_POST['id']);
	$db->query("DELETE FROM `".PREFIX."_report` WHERE id = '".$id."'");
	die();
}

//Удаление всех жалоб юзера
if($_GET['action'] == 'del_user'){
	$id = intval($_GET['id']);
	$db->query("DELETE FROM `".PREFIX."_report` WHERE ruser_id = '".$id."'");
	header("Location: ?mod=report");
	die();
}

//Удаление фото, видео, заметки, записи
if($_GET['action'] == 'obj'){
	$act_post = $_POST['act_post'];
	$rid = intval($_POST['aid']);
	
	//Запись
	if($act_post == 'wall'){
		$row = $db->super_query("SELECT author_user_id, for_user_id, fast_comm_id, add_date, attach FROM `".PREFIX."_wall` WHERE id = '{$rid}'");
		if($row){
			//удаляем саму запись
			$db->query("DELETE FROM `".PREFIX."_wall` WHERE id = '{$rid}'");

			//Если удаляется НЕ комментарий к записи
			if(!$row['fast_comm_id']){
				//удаляем комменты к записиы
				$db->query("DELETE FROM `".PREFIX."_wall` WHERE fast_comm_id = '{$rid}'");
				
				//удаляем "мне нравится"
				$db->query("DELETE FROM `".PREFIX."_wall_like` WHERE rec_id = '{$rid}'");
					
				//обновляем кол-во записей
				$db->query("UPDATE `".PREFIX."_users` SET user_wall_num = user_wall_num-1 WHERE user_id = '{$row['for_user_id']}'");
					
				//Чистим кеш
				mozg_clear_cache_file('user_'.$row['for_user_id'].'/profile_'.$row['for_user_id']);

				//удаляем из ленты новостей
				$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$rid}' AND action_type = 6");
					
				//Удаляем фотку из прикрипленой ссылке, если она есть
				if(stripos($row['attach'], 'link|') !== false){
					$attach_arr = explode('link|', $row['attach']);
					$attach_arr2 = explode('|/uploads/attach/'.$row['author_user_id'].'/', $attach_arr[1]);
					$attach_arr3 = explode('||', $attach_arr2[1]);
					if($attach_arr3[0])
						@unlink(ROOT_DIR.'/uploads/attach/'.$row['author_user_id'].'/'.$attach_arr3[0]);	
				}
				
				$action_type = 1;
			}

			//Если удаляется комментарий к записи
			if($row['fast_comm_id']){
				$db->query("UPDATE `".PREFIX."_wall` SET fasts_num = fasts_num-1 WHERE id = '{$row['fast_comm_id']}'");
				$rid = $row['fast_comm_id'];
				$action_type = 6;
			}
				
			//удаляем из ленты новостей
			$db->query("DELETE FROM `".PREFIX."_news` WHERE obj_id = '{$rid}' AND action_time = '{$row['add_date']}' AND action_type = {$action_type}");
		}
		
	//Заметка
	} elseif($act_post == 'note'){
		//Проверка на существование заметки
		$row = $db->super_query("SELECT owner_user_id FROM `".PREFIX."_notes` WHERE id = '{$rid}'");
		if($row){
			$db->query("DELETE FROM `".PREFIX."_notes` WHERE id = '{$rid}'");
			$db->query("DELETE FROM `".PREFIX."_notes_comments` WHERE note_id = '{$rid}'");
			$db->query("UPDATE `".PREFIX."_users` SET user_notes_num = user_notes_num-1 WHERE user_id = '{$row['owner_user_id']}'");
				
			//Чистим кеш владельцу заметки и заметок на его стр
			mozg_clear_cache_file('user_'.$row['owner_user_id'].'/profile_'.$row['owner_user_id']);
			mozg_clear_cache_file('user_'.$row['owner_user_id'].'/notes_user_'.$row['owner_user_id']);
		}

	//Видео
	} elseif($act_post == 'video'){
		$row = $db->super_query("SELECT owner_user_id, photo FROM `".PREFIX."_videos` WHERE id = '{$rid}'");
		if($row){
			$db->query("DELETE FROM `".PREFIX."_videos` WHERE id = '{$rid}'");
			$db->query("DELETE FROM `".PREFIX."_videos_comments` WHERE video_id = '{$rid}'");
			$db->query("UPDATE `".PREFIX."_users` SET user_videos_num = user_videos_num-1 WHERE user_id = '{$row['owner_user_id']}'");
					
			//Удаляем фотку
			$exp_photo = explode('/', $row['photo']);
			$photo_name = end($exp_photo);
			@unlink(ROOT_DIR.'/uploads/videos/'.$row['owner_user_id'].'/'.$photo_name);
					
			//Чистим кеш
			mozg_mass_clear_cache_file("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all|user_{$row['owner_user_id']}/profile_{$row['owner_user_id']}|user_{$row['owner_user_id']}/videos_num_all|user_{$row['owner_user_id']}/videos_num_friends");
		}
		
	//Фотография
	} elseif($act_post == 'photo'){
		$row = $db->super_query("SELECT user_id, album_id, photo_name, comm_num, position FROM `".PREFIX."_photos` WHERE id = '{$rid}'");
			
		//Если есть такая фотография и владельце действителен
		if($row){
			
			//Директория удаления
			$del_dir = ROOT_DIR.'/uploads/users/'.$row['user_id'].'/albums/'.$row['album_id'].'/';
				
			//Удаление фотки с сервера
			@unlink($del_dir.'c_'.$row['photo_name']);
			@unlink($del_dir.$row['photo_name']);

			//Удаление фотки из БД
			$db->query("DELETE FROM `".PREFIX."_photos` WHERE id = '{$rid}'");
				
			$check_photo_album = $db->super_query("SELECT id FROM `".PREFIX."_photos` WHERE album_id = '{$row['album_id']}'");
			$album_row = $db->super_query("SELECT cover FROM `".PREFIX."_albums` WHERE aid = '{$row['album_id']}'");
				
			//Если удаляемая фотография является обложкой то обновляем обложку на последнюю фотографию, если фотки еще есть из альбома
			if($album_row['cover'] == $row['photo_name'] AND $check_photo_album){
				$row_last_photo = $db->super_query("SELECT photo_name FROM `".PREFIX."_photos` WHERE user_id = '{$row['user_id']}' AND album_id = '{$row['album_id']}' ORDER by `id` DESC");
				$set_cover = ", cover = '{$row_last_photo['photo_name']}'";
			}
				
			//Если в альбоме уже нет фоток, то удаляем обложку
			if(!$check_photo_album)
				$set_cover = ", cover = ''";
				
			//Удаляем комментарии к фотографии
			$db->query("DELETE FROM `".PREFIX."_photos_comments` WHERE pid = '{$rid}'");
				
			//Обновляем количество комментов у альбома
			$db->query("UPDATE `".PREFIX."_albums` SET photo_num = photo_num-1, comm_num = comm_num-{$row['comm_num']} {$set_cover} WHERE aid = '{$row['album_id']}'");
				
			//Чистим кеш
			mozg_mass_clear_cache_file("user_{$row['user_id']}/albums|user_{$row['user_id']}/albums_all|user_{$row['user_id']}/albums_friends|user_{$row['user_id']}/position_photos_album_{$row['album_id']}");
				
			//Выводим и удаляем отметки если они есть
			$sql_mark = $db->super_query("SELECT muser_id FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$rid."' AND mapprove = '0'", 1);
			if($sql_mark){
				foreach($sql_mark as $row_mark){
					$db->query("UPDATE `".PREFIX."_users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '".$row_mark['muser_id']."'");
				}
			}
			$db->query("DELETE FROM `".PREFIX."_photos_mark` WHERE mphoto_id = '".$rid."'");
		}
	}
	
	die();
}
	
echoheader();
echohtmlstart('Поиск по жалобам');

$act = intval($_GET['act']);
$type = intval($_GET['type']);
$se_uid = intval($_GET['se_uid']);
if(!$se_uid) $se_uid = '';

if($se_uid OR $act OR $type){
	if($se_uid) $where_sql .= "AND ruser_id = '".$se_uid."' ";
	
	if($act == 1) $act_q = 'photo';
	else if($act == 2) $act_q = 'video';
	else if($act == 3) $act_q = 'note';
	else if($act == 4) $act_q = 'wall';
	else $act_q = false;
	if($act_q) $where_sql .= "AND act = '".$act_q."' ";
	
	if($type) $where_sql .= "AND type = '".$type."' ";
}

//Выводим список
if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
$gcount = 20;
$limit_page = ($page-1)*$gcount;

$sql_ = $db->super_query("SELECT tb1.id, ruser_id, act, type, text, mid, date, tb2.user_search_pref FROM `".PREFIX."_report` tb1, `".PREFIX."_users` tb2 WHERE tb1.ruser_id = tb2.user_id ".$where_sql." ORDER by `date` DESC LIMIT ".$limit_page.", ".$gcount, 1);

//Кол-во считаем
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_report` WHERE id != '' ".$where_sql);

$act_type = installationSelected($act, '<option value="1">Фотографии</option><option value="2">Видеозаписи</option><option value="3">Заметки</option><option value="4">Записи</option>');

$selsorlist_type = installationSelected($type, '<option value="1">Материал для взрослых</opyion><option value="2">Детская порнография</opyion><option value="3">Эктремизм</opyion><option value="4">Насилие</opyion><option value="5">Пропаганда наркотиков</opyion>');

echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
function del(act, id, i){
	$('#loading').fadeIn('fast');
	$.post('?mod=report&action=obj', {act_post: act, aid: id}, function(d){
		$('#loading').fadeOut('fast');
		$('#'+i).html('Удалено').attr('onClick', '');
	});
}
function del_rep(id){
	$('#loading').fadeIn('fast');
	$.post('?mod=report&action=del', {id: id}, function(d){
		$('#loading').fadeOut('fast');
		$('#r'+id).html('Жалоба удалена');
	});
}
</script>
<div id="loading" style="display:none"><div id="loading_text">Загрузка. Пожалуйста, подождите...</div></div>
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
.imgaa img{width:200px}
</style>
<form action="{$admin_index}" method="GET">
<input type="hidden" name="mod" value="report" />

<div class="fllogall">Поиск по ID пользователя:</div>
 <input type="text" name="se_uid" class="inpu" value="{$se_uid}" />
<div class="mgcler"></div>

<div class="fllogall">Жалоба на:</div>
 <select name="act" class="inpu">
  <option value="0"></option>
  {$act_type}
 </select>
<div class="mgcler"></div>

<div class="fllogall">Причина:</div>
 <select name="type" class="inpu">
  <option value="0"></option>
  {$selsorlist_type}
 </select>
<div class="mgcler"></div>

<div class="fllogall">&nbsp;</div>
 <input type="submit" value="Найти" class="inp" style="margin-top:0px" />

</form>
HTML;

echohtmlstart('Список жалоб ('.$numRows['cnt'].')');
$i = 20;
foreach($sql_ as $row){
	$i++;
	$row['date'] = langdate('j F Y в H:i', $row['date']);
	if($row['act'] == 'photo'){
		$row_info = $db->super_query("SELECT album_id, user_id, photo_name FROM `".PREFIX."_photos` WHERE id = '".$row['mid']."'");
		$act_q = '<a href="/photo'.$row_info['user_id'].'_'.$row['mid'].'_'.$row_info['album_id'].'" target="_blank"><b>фотографию</b></a>';
		$data_X = '<a href="/photo'.$row_info['user_id'].'_'.$row['mid'].'_'.$row_info['album_id'].'" target="_blank"><img src="/uploads/users/'.$row_info['user_id'].'/albums/'.$row_info['album_id'].'/c_'.$row_info['photo_name'].'" /></a>';
		if(!$row_info['user_id'])
			$data_X = '<font color="blue">Фотография удалена.</font>';
		else
			$del_data_lnk = '<a href="" onClick="del(\'photo\', '.$row['mid'].', this.id); return false" style="float:right;color:#000" id="lnk_photo'.$row['id'].'">удалить фотографию</a>';
	} else if($row['act'] == 'video'){
		$row_info_video = $db->super_query("SELECT owner_user_id, photo, title FROM `".PREFIX."_videos` WHERE id = '".$row['mid']."'");
		$act_q = '<a href="/video'.$row_info_video['owner_user_id'].'_'.$row['mid'].'" target="_blank"><b>видеозапись</b></a>';
		$data_X = '<a href="/video'.$row_info_video['owner_user_id'].'_'.$row['mid'].'" target="_blank"><img src="'.$row_info_video['photo'].'" /><br /><b>'.$row_info_video['title'].'</b></a>';
		if(!$row_info_video['title'])
			$data_X = '<font color="blue">Видеозапись удалена.</font>';
		else
			$del_data_lnk = '<a href="" onClick="del(\'video\', '.$row['mid'].', this.id); return false" id="lnk_video'.$row['id'].'" style="float:right;color:#000">удалить видеозапись</a>';
	} else if($row['act'] == 'note'){
		$row_info_note = $db->super_query("SELECT title FROM `".PREFIX."_notes` WHERE id = '".$row['mid']."'");
		$act_q = '<a href="/notes/view/'.$row['mid'].'" target="_blank"><b>заметку</b></a>';
		$data_X = '<a href="/notes/view/'.$row['mid'].'" target="_blank"><b>'.$row_info_note['title'].'</b></a>';
		if(!$row_info_note['title'])
			$data_X = '<font color="blue">Заметка удалена.</font>';
		else
			$del_data_lnk = '<a href="" onClick="del(\'note\', '.$row['mid'].', this.id); return false" id="lnk_note'.$row['id'].'" style="float:right;color:#000">удалить заметку</a>';
	} else if($row['act'] == 'wall'){
		$row_info_rec = $db->super_query("SELECT for_user_id, text FROM `".PREFIX."_wall` WHERE id = '".$row['mid']."'");
		$act_q = '<a href="/wall'.$row_info_rec['for_user_id'].'_'.$row['mid'].'" target="_blank"><b>запись</b></a>';
		$row_info_rec['text'] = stripslashes($row_info_rec['text']);
		$data_X = '<b>Содержимое сообщения:</b><br /><br /><div class="imgaa">'.$row_info_rec['text'].'</div>';
	
		if(!$row_info_rec['text'])
			$data_X = '<font color="blue">Запись удалена.</font>';
		else
			$del_data_lnk = '<a href="" onClick="del(\'wall\', '.$row['mid'].', this.id); return false" id="lnk_wall'.$row['id'].'" style="float:right;color:#000">удалить запись</a>';
	} else {
		$act_q = '';
		$data_X = '';
		$del_data_lnk = '';
	}
	
	if($row['type'] == 1) $type = 'Материал для взрослых';
	else if($row['type'] == 2) $type = 'Детская порнография';
	else if($row['type'] == 3) $type = 'Эктремизм';
	else if($row['type'] == 4) $type = 'Насилие';
	else if($row['type'] == 5) $type = 'Пропаганда наркотиков';
	else $type = 'Спам';
	
	$row['text'] = stripslashes($row['text']);
	if(!$row['text']) $row['text'] = '---';
	
	if($i >= 10) $strII = substr($i, 1, 1);
	else $strII = $i;
	$bgList = array(2, 4, 6, 8, 0);
	if(in_array($strII, $bgList)) $bg_color = '#f7f7f7';
	else $bg_color = '#eef1f4';
	
	echo <<<HTML
<div style="border-bottom:1px dashed #ccc;padding:10px;background:{$bg_color}" id="r{$row['id']}">
Жалобу на {$act_q} отправил <a href="/u{$row['ruser_id']}" target="_blank"><b>{$row['user_search_pref']}</b></a>, <i style="color:#999">{$row['date']}</i><a href="#" onClick="del_rep({$row['id']}); return false" style="float:right;color:green">удалить жалобу</a><br />
Причина: <b>{$type}</b><a href="?mod=report&action=del_user&id={$row['ruser_id']}" style="float:right;color:#999">удалить все жалобы от этого пользователя</a><br />
Комментарий: <b>{$row['text']}</b>{$del_data_lnk}<br /><br />
{$data_X}
<div class="clear"></div>
</div>
HTML;
}

echo '<div class="clear" style="margin-top:15px"></div>';

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);
echo navigation($gcount, $numRows['cnt'], '?'.$query_string.'&page=');

echohtmlend();
?>