<?php
/* 
	Appointment: Фоторедактор
	File: photo_editor.php 
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
		
		//################## Отмена редактирования ##################//
		case "close":
		
			$tpl->load_template('photos/editor_close.tpl');
			$tpl->set('{photo}', $_GET['image']);
			$tpl->compile('content');
			
			AjaxTpl();
				
		break;
		
		//################## Сохранение отредактированой фотки ##################//
		default:
		
			//Разришенные форматы
			$allowed_files = explode(', ', $config['photo_format']);
			
			$res_image = $_GET['image'];
			$format = end(explode('.', $res_image));
			$pid = $_GET['pid'];

			if(stripos($_SERVER['HTTP_REFERER'], 'pixlr.com') !== false AND $pid AND $format){
			
				//Выодим информацию о фото
				$row = $db->super_query("SELECT photo_name, album_id FROM `".PREFIX."_photos` WHERE user_id = '{$user_id}' AND id = '{$pid}'");
				
				//Проверям если, формат верный то пропускаем
				if(in_array(strtolower($format), $allowed_files) AND $row['photo_name']){
						
					@copy($res_image, ROOT_DIR."/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
					
					//Подключаем класс для фотографий
					include ENGINE_DIR.'/classes/images.php';
									
					//Создание оригинала
					$tmb = new thumbnail(ROOT_DIR."/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
					$tmb->size_auto('770');
					$tmb->jpeg_quality('85');
					$tmb->save(ROOT_DIR."/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
									
					//Создание маленькой копии
					$tmb = new thumbnail(ROOT_DIR."/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
					$tmb->size_auto('140x100');
					$tmb->jpeg_quality('90');
					$tmb->save(ROOT_DIR."/uploads/users/{$user_id}/albums/{$row['album_id']}/c_{$row['photo_name']}");
					
					$tpl->load_template('photos/editor.tpl');
					$tpl->set('{photo}', "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}?{$server_time}");
					$tpl->compile('content');
					
					AjaxTpl();
					
				}

			} else
				echo 'Hacking attempt!';
		
	}

}
	
exit();
?>