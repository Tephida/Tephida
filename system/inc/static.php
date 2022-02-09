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

//Если начали замену
if(isset($_POST['save'])){
	//Подключаем парсер
	include_once ENGINE_DIR.'/classes/parse.php';
	$parse = new parse();
			
	$title = requestFilter('title', 25000, true);
	$alt_name = to_translit($_POST['alt_name']);
	$text = $parse->BBparse(requestFilter('text'));
	
	if(!empty($title) AND !empty($text) AND AND !empty($alt_name)){
		$db->query("INSERT INTO `".PREFIX."_static` SET alt_name = '".$alt_name."', title = '".$title."', text = '".$text."'");
		header("Location: ?mod=static");
	} else
		msgbox('Ошибка', 'Все поля обязательны к заполнению', 'javascript:history.go(-1)');
} else {
	//Удаление
	if($_GET['act'] == 'del'){
		$id = intval($_GET['id']);
		$db->query("DELETE FROM `".PREFIX."_static` WHERE id = '".$id."'");
		header("Location: ?mod=static");
	}
	
	//Редактирование
	if($_GET['act'] == 'edit'){
		$id = intval($_GET['id']);
		$row = $db->super_query("SELECT title, alt_name, text FROM `".PREFIX."_static` WHERE id = '".$id."'");
		if($row){
		
			//Сохраняем
			if(isset($_POST['save_edit'])){
				//Подключаем парсер
				include_once ENGINE_DIR.'/classes/parse.php';
				$parse = new parse();
						
				$title = requestFilter('title', 25000, true);
				$alt_name = to_translit($_POST['alt_name']);
				$text = $parse->BBparse(requestFilter('text'));
				
				if(!empty($title) AND !empty($text) AND !empty($alt_name)){
					$db->query("UPDATE`".PREFIX."_static` SET alt_name = '".$alt_name."', title = '".$title."', text = '".$text."' WHERE id = '".$id."'");
					header("Location: ?mod=static");
				} else
					msgbox('Ошибка', 'Все поля обязательны к заполнению', 'javascript:history.go(-1)');
					
				die();
			}
			
			echoheader();
			
			$row['title'] = stripslashes($row['title']);
			
			//Подключаем парсер
			include_once ENGINE_DIR.'/classes/parse.php';
			$parse = new parse();
	
			$row['text'] = $parse->BBdecode(myBrRn(stripslashes($row['text'])));
			
			echohtmlstart('Редактирование страницы');
			echo <<<HTML
<form method="POST" action="">

<style type="text/css" media="all">
.inpu{width:458px;}
textarea{width:300px;height:300px;}
</style>

<div class="fllogall" style="width:130px">Заголовок:</div><input class="inpu" type="text" name="title" value="{$row['title']}" /><div class="mgcler"></div>

<div class="fllogall" style="width:130px">Адрес: (например <b>test</b>):</div><input class="inpu" type="text" name="alt_name" value="{$row['alt_name']}" /><div class="mgcler"></div>

<div class="fllogall" style="width:130px">Текст:</div><textarea class="inpu" name="text">{$row['text']}</textarea><div class="mgcler"></div>

<div class="fllogall" style="width:130px">&nbsp;</div>
 <input type="submit" value="Сохранить" name="save_edit" class="inp" style="margin-top:0px" />
 <input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />


</form>
HTML;
			echohtmlend();
		} else
			msgbox('Информация', 'Страница не найдена', '?mod=static');
		
		die();
	}
	
	echoheader();
	
	echohtmlstart('Создание новой страницы');
	echo <<<HTML
<form method="POST" action="">

<style type="text/css" media="all">
.inpu{width:458px;}
textarea{width:300px;height:300px;}
</style>

<div class="fllogall" style="width:130px">Заголовок:</div><input class="inpu" type="text" name="title" /><div class="mgcler"></div>

<div class="fllogall" style="width:130px">Адрес: (например <b>test</b>):</div><input class="inpu" type="text" name="alt_name" /><div class="mgcler"></div>

<div class="fllogall" style="width:130px">Текст:</div><textarea class="inpu" name="text"></textarea><div class="mgcler"></div>

<div class="fllogall" style="width:130px">&nbsp;</div><input type="submit" value="Создать" name="save" class="inp" style="margin-top:0px" />

</form>
HTML;
	
	echohtmlstart('Список статических страниц');
	
	$sql_ = $db->super_query("SELECT id, title, alt_name FROM `".PREFIX."_static` ORDER by `id` DESC", 1);
	foreach($sql_ as $row){
		$row['title'] = stripslashes($row['title']);
		$static_list .= <<<HTML
<div style="margin-bottom:5px;border-bottom:1px dashed #ccc;padding-bottom:5px">&raquo; <a href="?mod=static&act=edit&id={$row['id']}" style="font-size:13px"><b>{$row['title']}</b></a> &nbsp; <span style="color:#777">[ <a href="?mod=static&act=del&id={$row['id']}" style="color:#777">удалить</a> ] [ <a href="/{$row['alt_name']}.html" target="_blank" style="color:#777">просмотр</a> ]</span></div>
HTML;
	}
	
	echo $static_list;
	
	echohtmlend();
}
?>