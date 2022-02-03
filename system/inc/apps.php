<?php
/* 
	Appointment: Игры
	File: apps.php
	Author: f0rt1 
	Engine: Vee Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

//Загрузка постера
if($_GET['act'] == 'upload'){

	//Получаем данные о файле
	$image_tmp = $_FILES['uploadfile']['tmp_name'];
	$image_name = totranslit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
	$image_rename = substr(md5($server_time+rand(1,100000)), 0, 20); // имя файла
	$image_size = $_FILES['uploadfile']['size']; // размер файла
	$type = end(explode(".", $image_name)); // формат файла
	
	$max_size = 1024 * 5000;
	
	$id = intval($_GET['id']);
	
	//Проверка размера
	if($image_size <= $max_size){
		
		//Разришенные форматы
		$allowed_files = explode(', ', 'jpg, jpeg, jpe, png, gif');
		
		//Проверям если, формат верный то пропускаем
		if(in_array(strtolower($type), $allowed_files)){
				
			$res_type = strtolower('.'.$type);
			
			if($id) $rowCheck = $db->super_query("SELECT poster FROM `".PREFIX."_games` WHERE id = '{$id}'");
			
			//Директория
			if($rowCheck)
				$upDir = ROOT_DIR.'/uploads/apps/'.$id.'/';
			else
				$upDir = ROOT_DIR.'/uploads/apps/temp/'.$user_info['user_id'].'/';
			
			//Если нет папок, то создаём их
			if(!is_dir($upDir)){ 
				@mkdir($upDir, 0777);
				@chmod($upDir, 0777);
			}
			
			$rImg = $upDir.$image_rename.$res_type;
			
			if(move_uploaded_file($image_tmp, $rImg)){
				
					//Подключаем класс для фотографий
					include_once ENGINE_DIR.'/classes/images.php';
					
					//Создание маленькой копии
					$tmb = new thumbnail($rImg);
					$tmb->size_auto('75x75');
					$tmb->jpeg_quality('100');
					$tmb->save($rImg);
					
					//Если редактируем постер
					if($rowCheck){
						
						$db->query("UPDATE `".PREFIX."_games` SET poster = '{$image_rename}{$res_type}' WHERE id = '{$id}'");
						$db->query("UPDATE `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}' WHERE game_id = '{$id}' AND type = 'poster'");
						
						@unlink($upDir.$rowCheck['poster']);
						
						echo $id.'/'.$image_rename.$res_type;
						
					} else {
					
						//Выводим пред. загруженные постеры и удаляем их
						$sql_ = $db->super_query("SELECT file FROM `".PREFIX."_games_files` WHERE user_id = '{$user_info['user_id']}' AND type = 'poster' AND game_id = '0'", 1);
						
						if($sql_){
						
							foreach($sql_ as $row){
							
								@unlink($upDir.$row['file']);
								
							}
							
							$db->query("DELETE FROM `".PREFIX."_games_files` WHERE user_id = '{$user_info['user_id']}' AND type = 'poster' AND game_id = '0'");
							
						}
						
						//Читаем хеш
						$app_hash = $db->safesql($_SESSION['apps_hash']);
						
						//Вставляем в базу
						$db->query("INSERT INTO `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}', hash = '{$app_hash}', type = 'poster', user_id = '{$user_info['user_id']}'");
						
						echo $user_info['user_id'].'/'.$image_rename.$res_type;
						
					}
					
			}
			
		} else
			echo 2;
		
	} else
		echo 1;
	
	exit();
	
};

//Загрузка игры SWF
if($_GET['act'] == 'upload_swf'){

	//Получаем данные о файле
	$image_tmp = $_FILES['uploadfile']['tmp_name'];
	$image_name = totranslit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
	$image_rename = substr(md5($server_time+rand(1,100000)), 0, 20); // имя файла
	$image_size = $_FILES['uploadfile']['size']; // размер файла
	$type = end(explode(".", $image_name)); // формат файла
	
	$max_size = ( 1024 * 5000 ) * 20;
	
	$id = intval($_GET['id']);
	
	//Проверка размера
	if($image_size <= $max_size){

		//Проверям если, формат верный то пропускаем
		if(strtolower($type) == 'swf'){
				
			$res_type = strtolower('.'.$type);
			
			if($id) $rowCheck = $db->super_query("SELECT flash FROM `".PREFIX."_games` WHERE id = '{$id}'");
			
			//Директория
			if($rowCheck)
				$upDir = ROOT_DIR.'/uploads/apps/'.$id.'/';
			else
				$upDir = ROOT_DIR.'/uploads/apps/temp/'.$user_info['user_id'].'/';
			
			//Если нет папок, то создаём их
			if(!is_dir($upDir)){ 
				@mkdir($upDir, 0777);
				@chmod($upDir, 0777);
			}
			
			$rImg = $upDir.$image_rename.$res_type;
			
			if(move_uploaded_file($image_tmp, $rImg)){
				
					//Если редактируем игру
					if($rowCheck){
						
						$db->query("UPDATE `".PREFIX."_games` SET flash = '{$image_rename}{$res_type}' WHERE id = '{$id}'");
						$db->query("UPDATE `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}' WHERE game_id = '{$id}' AND type = 'swf'");
						
						@unlink($upDir.$rowCheck['flash']);
						
					} else {
					
						//Выводим пред. загруженные swf и удаляем их
						$sql_ = $db->super_query("SELECT file FROM `".PREFIX."_games_files` WHERE user_id = '{$user_info['user_id']}' AND type = 'swf' AND game_id = '0'", 1);
						
						if($sql_){
						
							foreach($sql_ as $row){
							
								@unlink($upDir.$row['file']);
								
							}
							
							$db->query("DELETE FROM `".PREFIX."_games_files` WHERE user_id = '{$user_info['user_id']}' AND type = 'swf' AND game_id = '0'");
							
						}
						
						//Читаем хеш
						$app_hash = $db->safesql($_SESSION['apps_hash']);
						
						//Вставляем в базу
						$db->query("INSERT INTO `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}', hash = '{$app_hash}', type = 'swf', user_id = '{$user_info['user_id']}'");
					
					}

			}
			
		} else
			echo 2;
		
	} else
		echo 1;
	
	exit();
	
}

//Загрузка скрин
if($_GET['act'] == 'upload_scrin'){

	//Получаем данные о файле
	$image_tmp = $_FILES['uploadfile']['tmp_name'];
	$image_name = totranslit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
	$image_rename = substr(md5($server_time+rand(1,100000)), 0, 20); // имя файла
	$image_size = $_FILES['uploadfile']['size']; // размер файла
	$type = end(explode(".", $image_name)); // формат файла
	
	$max_size = 1024 * 5000;
	
	$id = intval($_GET['id']);
	
	//Проверка размера
	if($image_size <= $max_size){
		
		//Разришенные форматы
		$allowed_files = explode(', ', 'jpg, jpeg, jpe, png, gif');
		
		//Проверям если, формат верный то пропускаем
		if(in_array(strtolower($type), $allowed_files)){
				
			$res_type = strtolower('.'.$type);
			
			if($id) $rowCheck = $db->super_query("SELECT id FROM `".PREFIX."_games` WHERE id = '{$id}'");
			
			//Директория
			if($rowCheck)
				$upDir = ROOT_DIR.'/uploads/apps/'.$id.'/';
			else
				$upDir = ROOT_DIR.'/uploads/apps/temp/'.$user_info['user_id'].'/';
			
			//Если нет папок, то создаём их
			if(!is_dir($upDir)){ 
				@mkdir($upDir, 0777);
				@chmod($upDir, 0777);
			}
			
			$rImg = $upDir.$image_rename.$res_type;
			
			//Читаем хеш
			$app_hash = $db->safesql($_SESSION['apps_hash']);
			
			//Считаем кол-вол загруженных скринов
			if($rowCheck)
				$cnRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_games_files` WHERE game_id = '{$id}' AND type = 'scrin'");
			else
				$cnRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_games_files` WHERE hash = '{$app_hash}' AND type = 'scrin'");
			
			if($cnRow['cnt'] <= 3){
			
				if(move_uploaded_file($image_tmp, $rImg)){
					
						//Подключаем класс для фотографий
						include_once ENGINE_DIR.'/classes/images.php';
						
						//Создание 607х376
						$tmb = new thumbnail($rImg);
						$tmb->size_auto('607x376');
						$tmb->jpeg_quality('100');
						$tmb->save($rImg);
						
						//Создание 145х90
						$tmb = new thumbnail($rImg);
						$tmb->size_auto('145x90');
						$tmb->jpeg_quality('100');
						$tmb->save($upDir.'m'.$image_rename.$res_type);
						
						//Если редактируем
						if($rowCheck){
							
							$rowHash = $db->super_query("SELECT hash FROM `".PREFIX."_games_files` WHERE game_id = '{$id}'");
							
							//Вставляем в базу
							$db->query("INSERT INTO `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}', hash = '{$rowHash['hash']}', type = 'scrin', user_id = '{$user_info['user_id']}', game_id = '{$id}'");
							
							echo $id.'/m'.$image_rename.$res_type;
							
						} else {
						
							//Удаляем все пред. скрины
							$sql_ = $db->super_query("SELECT file FROM `".PREFIX."_games_files` WHERE hash != '{$app_hash}' AND user_id = '{$user_info['user_id']}' AND type = 'scrin' AND game_id = '0'", 1);
							
							if($sql_){
							
								foreach($sql_ as $row){
								
									@unlink($upDir.$row['file']);
									@unlink($upDir.'m'.$row['file']);
									
								}
								
								$db->query("DELETE FROM `".PREFIX."_games_files` WHERE user_id = '{$user_info['user_id']}' AND type = 'scrin' AND hash != '{$app_hash}' AND game_id = '0'");
								
							}

							//Вставляем в базу
							$db->query("INSERT INTO `".PREFIX."_games_files` SET file = '{$image_rename}{$res_type}', hash = '{$app_hash}', type = 'scrin', user_id = '{$user_info['user_id']}'");
							
							echo $user_info['user_id'].'/m'.$image_rename.$res_type;
						
						}
						
				}
			
			} else
				echo 3;
			
		} else
			echo 2;
		
	} else
		echo 1;
	
	exit();
	
};

//Удаление файла
if($_GET['act'] == 'del'){
	
	$file = $db->safesql(strip_tags($_POST['file']));
	
	$row = $db->super_query("SELECT file, game_id FROM `".PREFIX."_games_files` WHERE file = '{$file}'");
	
	if($row['file']){
		
		if($row['game_id'])
			$upDir = ROOT_DIR.'/uploads/apps/'.$row['game_id'].'/';
		else
			$upDir = ROOT_DIR.'/uploads/apps/temp/'.$user_info['user_id'].'/';
		
		@unlink($upDir.$row['file']);
		@unlink($upDir.'m'.$row['file']);
		
		$db->query("DELETE FROM `".PREFIX."_games_files` WHERE file = '{$file}'");
		
	}
	
	exit();
	
}

//Отправка в базу
if(isset($_POST['send'])){
	
	$title = textFilter($_POST['title'], false, true);
	$descr = textFilter($_POST['descr']);
	$width = intval($_POST['width']);
	$height = intval($_POST['height']);
	
	//Читаем хеш
	$app_hash = $db->safesql($_SESSION['apps_hash']);
	
	if(isset($title) AND !empty($title)){
		
		//Выводим файлы
		$sql_ = $db->super_query("SELECT file, type FROM `".PREFIX."_games_files` WHERE hash = '{$app_hash}' AND user_id = '{$user_info['user_id']}'", 1);
		
		if($sql_){
		
			foreach($sql_ as $row){
				
				if($row['type'] == 'poster') $poster .= $row['file'];
				
				if($row['type'] == 'swf') $swf .= $row['file'];
				
			}
			
			//Всталвяем игру в базу
			$db->query("INSERT INTO `".PREFIX."_games` SET title = '{$title}', descr = '{$descr}', poster = '{$poster}', flash = '{$swf}', date = '{$server_time}', width = '{$width}', height = '{$height}'");
			$id = $db->insert_id();
			
			//Создаем папку игры в uploads
			$gameDir = ROOT_DIR.'/uploads/apps/'.$id.'/';
			@mkdir($gameDir, 0777);
			@chmod($gameDir, 0777);
			
			//Временна директория
			$upDir = ROOT_DIR.'/uploads/apps/temp/'.$user_info['user_id'].'/';
			
			//Копируем файлы из временной папки в новую папку игры
			foreach($sql_ as $row){
				
				if($row['type'] == 'scrin'){
				
					@copy($upDir.'m'.$row['file'], $gameDir.'m'.$row['file']);
					@unlink($upDir.'m'.$row['file']);
					
					@copy($upDir.$row['file'], $gameDir.$row['file']);
					@unlink($upDir.$row['file']);
					
				} else {
				
					@copy($upDir.$row['file'], $gameDir.$row['file']);
					@unlink($upDir.$row['file']);
					
				}
				
			}
			
			//Обновляем ИД в таблице _games_files
			$db->query("UPDATE `".PREFIX."_games_files` SET game_id = '{$id}' WHERE hash = '{$app_hash}' AND user_id = '{$user_info['user_id']}'");
			
			msgbox('Информация', 'Игра успешно добавлена на сайт!', '?mod=apps');
			
			//Удаляем хеш
			unset($_SESSION['apps_hash']);
			
		} else
			msgbox('Ошибка', 'Заполните все поля', '?mod=apps');
		
	} else
		msgbox('Ошибка', 'Заполните все поля', '?mod=apps');
	
	exit();
}

//Удаление игры
if($_GET['act'] == 'del_game'){
	
	$id = intval($_GET['id']);
	
	$row = $db->super_query("SELECT traf FROM `".PREFIX."_games` WHERE id = '{$id}'");
	
	if($row){
	
		//Выводим все файлы и удаляеи их
		$sql_ = $db->super_query("SELECT file, type FROM `".PREFIX."_games_files` WHERE game_id = '{$id}'", 1);
		
		foreach($sql_ as $row_f){
			
			@unlink(ROOT_DIR.'/uploads/apps/'.$id.'/'.$row_f['file']);
			
			if($row_f['type'] == 'scrin')
				@unlink(ROOT_DIR.'/uploads/apps/'.$id.'/m'.$row_f['file']);
		
		}
		
		@rmdir(ROOT_DIR.'/uploads/apps/'.$id.'/');
		
		//Удаляем все файлы к игре
		$db->query("DELETE FROM `".PREFIX."_games_files` WHERE game_id = '{$id}'");
		
		//Удаляем саму игру
		$db->query("DELETE FROM `".PREFIX."_games` WHERE id = '{$id}'");
		
		//Удаляе игру у юзеров
		$db->query("DELETE FROM `".PREFIX."_games_users` WHERE game_id = '{$id}'");
		
		//Удаляем из ленты активности
		$db->query("DELETE FROM `".PREFIX."_games_activity` WHERE game_id = '{$id}'");
		
		msgbox('Ошибка', 'Игра удалена', '?mod=apps');;
		
	} else
		msgbox('Ошибка', 'Игра не найдена', '?mod=apps');;
	
	exit();
}

//Сохраняем отред. данные
if(isset($_POST['edtisave'])){
	
	$title = textFilter($_POST['title'], false, true);
	$descr = textFilter($_POST['descr']);
	$width = intval($_POST['width']);
	$height = intval($_POST['height']);
	
	$id = intval($_GET['id']);
	
	if(isset($title) AND !empty($title)){
		
		$db->query("UPDATE `".PREFIX."_games` SET title = '{$title}', descr = '{$descr}', width = '{$width}', height = '{$height}' WHERE id = '{$id}'");
		
		msgbox('Информация', 'Изменения сохранены', '?mod=apps');
		
	} else
		msgbox('Ошибка', 'Заполните все поля', '?mod=apps');
		
	exit();
	
}

//Редактирование игры
if($_GET['act'] == 'edit'){
	
	$id = intval($_GET['id']);
	
	$row = $db->super_query("SELECT id, title, descr, poster, width, height FROM `".PREFIX."_games` WHERE id = '{$id}'");
	
	if($row){
		
		$row['title'] = stripslashes($row['title']);
		$row['descr'] = stripslashes(myBrRn($row['descr']));
		
		//Выводим скрины
		$sql_scr = $db->super_query("SELECT file, hash FROM `".PREFIX."_games_files` WHERE game_id = '{$id}' AND type = 'scrin'", 1);
		
		if($sql_scr){
		
			foreach($sql_scr as $row_scr){
				
				$exps = explode('.', $row_scr['file']);
				
				$imgs .= <<<HTML
<img id="{$exps[0]}" onClick="del('{$row_scr['file']}')" src="/uploads/apps/{$id}/m{$row_scr['file']}" title="Удалить" style="cursor:pointer;margin-top:8px;margin-bottom:8px;margin-left:2px;margin-right:2px" />
HTML;
				
			}
		
		}
		
		if($row['poster']) $poster = "/uploads/apps/{$row['id']}/{$row['poster']}";
		else $poster = '/uploads/no_app.gif';
		
		echoheader();

		echohtmlstart('Редактирование игры');
			
echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript" src="/system/inc/js/upload.photo.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	aj1 = new AjaxUpload('upload', {
		action: '?mod=apps&act=upload&id={$row['id']}',
		name: 'uploadfile',
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload').hide();
			$('#prog_poster').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else {
				$('#r_poster').attr('src', '/uploads/apps/'+row).show();
			}
			$('#upload').show();
			$('#prog_poster').hide();
		}
	});
	aj2 = new AjaxUpload('upload_2', {
		action: '?mod=apps&act=upload_swf&id={$row['id']}',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if(!(ext && /^(swf)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_2').hide();
			$('#prog_flash').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 100 МБ');
			} else {
				$('#ok_swf').text('Файл загружен!').css('color', 'green');
			}
			$('#upload_2').show();
			$('#prog_flash').hide();
		}
	});
	aj3 = new AjaxUpload('upload_3', {
		action: '?mod=apps&act=upload_scrin&id={$row['id']}',
		name: 'uploadfile',
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_3').hide();
			$('#prog_scrins').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else if(row == 3){
				alert('Максимальное кол-во скринов: 4');
			} else {
				file = row.split('/m');
				fid = file[1].split('.');
				$('#scrins').append('<img id="'+fid[0]+'" onClick="del(\''+file[1]+'\')" src="/uploads/apps/'+row+'" title="Удалить" style="cursor:pointer;margin-top:8px;margin-bottom:8px;margin-left:2px;margin-right:2px" />');
			}
			$('#upload_3').show();
			$('#prog_scrins').hide();
		}
	});
});
function del(f){
	fid = f.split('.');
	$('#'+fid[0]).remove();
	$.post('?mod=apps&act=del', {file: f});
}
</script>

<style type="text/css" media="all">
.inpu{width:350px;}
textarea{width:450px;height:100px;}
</style>

<form action="" method="POST">

<div class="fllogall" style="width:180px">Название:</div>
 <input type="text" name="title" class="inpu" value="{$row['title']}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Описание:</div>
 <textarea type="text" name="descr" class="inpu">{$row['descr']}</textarea>
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Постер 75х75:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload" style="margin-top:0px" /><br />
 <div id="prog_poster" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:182px"><small>Файл не должен превышать 5 Mб.</small></div>
 <img src="{$poster}" id="r_poster" style="margin-left:182px" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Flash игра .swf:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload_2" style="margin-top:0px" /><br />
 <div id="prog_flash" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:180px"><small id="ok_swf">Файл не должен превышать 100 Mб.</small></div>
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Ширина flash игры (px):</div>
 <input type="text" name="width" class="inpu" value="{$row['width']}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Высота flash игры (px):</div>
 <input type="text" name="height" class="inpu" value="{$row['height']}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Скриншоты 607х376:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload_3" style="margin-top:0px" /><br />
 <div id="prog_scrins" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:180px"><small>Файл не должен превышать 5 Mб.</small></div>
 <center><span id="scrins">{$imgs}</span></center>
<div class="mgcler"></div>


<div class="fllogall" style="width:180px">&nbsp;</div>
 <input type="submit" value="Сохранить" class="inp" name="edtisave" style="margin-top:0px" />
 <input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />
</form>
HTML;

		echohtmlend();
		
	} else
		msgbox('Ошибка', 'Игра не найдена', '?mod=apps');;
		
	exit();
	
}

//Устанавливаем в сессию временный хеш
$apps_hash = md5($server_time+rand(0, $server_time));
$_SESSION['apps_hash'] = $apps_hash;

echoheader();

echohtmlstart('Добавление игры');
			
echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript" src="/system/inc/js/upload.photo.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	aj1 = new AjaxUpload('upload', {
		action: '?mod=apps&act=upload',
		name: 'uploadfile',
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload').hide();
			$('#prog_poster').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else {
				$('#r_poster').attr('src', '/uploads/apps/temp/'+row).show();
			}
			$('#upload').show();
			$('#prog_poster').hide();
		}
	});
	aj2 = new AjaxUpload('upload_2', {
		action: '?mod=apps&act=upload_swf',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if(!(ext && /^(swf)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_2').hide();
			$('#prog_flash').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 100 МБ');
			} else {
				$('#ok_swf').text('Файл загружен!').css('color', 'green');
			}
			$('#upload_2').show();
			$('#prog_flash').hide();
		}
	});
	aj3 = new AjaxUpload('upload_3', {
		action: '?mod=apps&act=upload_scrin',
		name: 'uploadfile',
		accept: 'image/*',
		onSubmit: function (file, ext) {
			if(!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				alert('Неверный формат файла');
				return false;
			}
			$('#upload_3').hide();
			$('#prog_scrins').show();
		},
		onComplete: function (file, row){
			if(row == 1){
				alert('Файл привышает 5 МБ');
			} else if(row == 3){
				alert('Максимальное кол-во скринов: 4');
			} else {
				file = row.split('/m');
				fid = file[1].split('.');
				$('#scrins').append('<img id="'+fid[0]+'" onClick="del(\''+file[1]+'\')" src="/uploads/apps/temp/'+row+'" title="Удалить" style="cursor:pointer;margin-top:8px;margin-bottom:8px;margin-left:2px;margin-right:2px" />');
			}
			$('#upload_3').show();
			$('#prog_scrins').hide();
		}
	});
});
function del(f){
	fid = f.split('.');
	$('#'+fid[0]).remove();
	$.post('?mod=apps&act=del', {file: f});
}
</script>

<style type="text/css" media="all">
.inpu{width:350px;}
textarea{width:450px;height:100px;}
</style>

<form action="" method="POST">

<div class="fllogall" style="width:180px">Название:</div>
 <input type="text" name="title" class="inpu" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Описание:</div>
 <textarea type="text" name="descr" class="inpu"></textarea>
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Постер 75х75:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload" style="margin-top:0px" /><br />
 <div id="prog_poster" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:182px"><small>Файл не должен превышать 5 Mб.</small></div>
 <img src="" id="r_poster" style="margin-left:182px;display:none" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Flash игра .swf:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload_2" style="margin-top:0px" /><br />
 <div id="prog_flash" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:180px"><small id="ok_swf">Файл не должен превышать 100 Mб.</small></div>
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Ширина flash игры (px):</div>
 <input type="text" name="width" class="inpu" value="795" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Высота flash игры (px):</div>
 <input type="text" name="height" class="inpu" value="495" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Скриншоты 607х376:</div>
 <input type="submit" value="Выбрать файл" class="inp" id="upload_3" style="margin-top:0px" /><br />
 <div id="prog_scrins" style="display:none;margin-top:-11px;background:url('/system/inc/images/progress_grad.gif');width:94px;height:18px;border:1px solid #006699;margin-left:182px"></div>
 <div style="margin-left:180px"><small>Файл не должен превышать 5 Mб.</small></div>
 <center><span id="scrins"></span></center>
<div class="mgcler"></div>


<div class="fllogall" style="width:180px">&nbsp;</div>
 <input type="submit" value="Добавить игру" class="inp" style="background:#33CC66;color:#fff;border:0px" name="send" style="margin-top:0px" />
</form>
HTML;

//Поиск
echohtmlstart('Поиск');

$title = $db->safesql(strip_tags($_GET['title']));
$id = intval($_GET['id']);
if($id <= 0) $id = '';
if(isset($title) AND !empty($title)) $sql_where = "WHERE title LIKE '%{$title}%'";
if($id) $sql_where = "WHERE id = '{$id}'";

echo <<<HTML
<form action="" method="GET">

<input type="hidden" name="mod" value="apps" />

<div class="fllogall" style="width:180px">По названию:</div>
 <input type="text" name="title" class="inpu" value="{$title}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">По ID:</div>
 <input type="text" name="id" class="inpu" value="{$id}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">&nbsp;</div>
 <input type="submit" value="Найти" class="inp" style="margin-top:0px" />

</form>
HTML;


//Выводим игры
if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
$gcount = 20;
$limit_page = ($page-1)*$gcount;

//Считаем кол-во игр в базе
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_games` {$sql_where}");

$sql_l = $db->super_query("SELECT id, poster, title, traf FROM `".PREFIX."_games` {$sql_where} ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", 1);

if($sql_l){

	foreach($sql_l as $row){
		
		if($row['poster']) $poster = "/uploads/apps/{$row['id']}/{$row['poster']}";
		else $poster = '/uploads/no_app.gif';
		
		$row['title'] = stripslashes($row['title']);
		
		$games .= <<<HTML
<div style="clear:both;height:10px"></div>
<img src="{$poster}" style="float:left;margin-right:10px" />
<a href="/">{$row['title']}</a>
<div style="color:#000">Играет <b>{$row['traf']}</b> чел.</div>
<div><a href="?mod=apps&act=edit&id={$row['id']}">редактировать</a>&nbsp; | &nbsp;<a href="?mod=apps&act=del_game&id={$row['id']}">удалить</a></div>
<div style="clear:both;height:10px"></div>
<div style="clear:both;height:1px;border-top:1px solid #f0f0f0"></div>
HTML;
		
	}

} else
	$games = '<center><b>Нет игр!</b></center>';

echohtmlstart('Список игр ('.$numRows['cnt'].')');

echo <<<HTML
{$games}
<div class="clr" style="height:10px"></div>
HTML;

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);

echo navigation($gcount, $numRows['cnt'], '?'.$query_string.'&page=');

echohtmlend();
?>