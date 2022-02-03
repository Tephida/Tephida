<?php
/* 
	Appointment: Поиск и замена
	File: search.php
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

//Если начали замену
if(isset($_POST['save'])){
	if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()){
		$_POST['find'] = stripslashes($_POST['find']);
		$_POST['replace'] = stripslashes($_POST['replace']);
	} 

	$find = $db->safesql(addslashes(trim($_POST['find'])));
	$replace = $db->safesql(addslashes(trim($_POST['replace'])));
	
	if(isset($find) AND !empty($find) AND isset($replace) AND !empty($replace)){
		if($_POST['photo_comm']) $db->query("UPDATE `".PREFIX."_photos_comments` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['video_comm']) $db->query("UPDATE `".PREFIX."_videos_comments` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['notes_comm']) $db->query("UPDATE `".PREFIX."_notes_comments` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['users_wall']) $db->query("UPDATE `".PREFIX."_wall` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['groups_wall']) $db->query("UPDATE `".PREFIX."_communities_wall` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['news']) $db->query("UPDATE `".PREFIX."_news` SET `action_text` = REPLACE(`action_text`, '".$find."', '".$replace."')");
		if($_POST['msg']) $db->query("UPDATE `".PREFIX."_messages` SET `text` = REPLACE(`text`, '".$find."', '".$replace."')");
		if($_POST['gift_msg']) $db->query("UPDATE `".PREFIX."_gifts` SET `msg` = REPLACE(`msg`, '".$find."', '".$replace."')");
		if($_POST['notes_text']) $db->query("UPDATE `".PREFIX."_notes` SET `full_text` = REPLACE(`full_text`, '".$find."', '".$replace."')");
		
		msgbox('Информация', 'Текст в базе данных был успешно заменен.', '?mod=search');
	} else
		msgbox('Ошибка', 'Все поля обязательны к заполнению', 'javascript:history.go(-1)');
} else {
	echoheader();
	
	echohtmlstart('Быстрая замена текста в базе данных скрипта');
	echo <<<HTML
<style type="text/css" media="all">
.inpu{width:308px;}
textarea{width:300px;height:100px;}
</style>
Данная утилита производит замену текста в вашей базе. Например у вас изменился домен и вы хотите его изменить в комментариях, заметках и т.д.
<br /><br />
<b><font color="red">Внимание:</b> Перед заменой не забудьте создать резервную копию базы данных, т.к. данное действие в случае некорректной или не совсем ожидаемой замены, невозможно будет отменить. Мы настоятельно не рекомендуем производить замену коротких слов или предлогов, т.к. они могут встречаться в составе других слов.</font>

<form method="POST" action="" style="margin-top:15px">
<div class="mgcler"></div>

<div class="fllogall">Где заменять:</div>

 <input type="checkbox" name="photo_comm" style="margin-bottom:10px" /> в комментариях к фотографиям<br />
 <input type="checkbox" name="video_comm" style="margin-bottom:10px;margin-left:286px" /> в комментариях к видеозаписям<br />
 <input type="checkbox" name="notes_comm" style="margin-bottom:10px;margin-left:286px" /> в комментариях к заметкам<br />
 <input type="checkbox" name="users_wall" style="margin-bottom:10px;margin-left:286px" /> на стенах пользователей<br />
 <input type="checkbox" name="groups_wall" style="margin-bottom:10px;margin-left:286px" /> на стенах сообществ<br />
 <input type="checkbox" name="news" style="margin-bottom:10px;margin-left:286px" /> в ленте новостей<br />
 <input type="checkbox" name="msg" style="margin-bottom:10px;margin-left:286px" /> в персональных сообщениях<br />
 <input type="checkbox" name="gift_msg" style="margin-bottom:10px;margin-left:286px" /> в сообщениях к подаркам<br />
 <input type="checkbox" name="notes_text" style="margin-bottom:10px;margin-left:286px" /> в содержаниях заметок<br />
 
<div class="mgcler"></div>

<div class="fllogall">Введите старый текст:</div><textarea class="inpu" name="find"></textarea><div class="mgcler"></div>

<div class="fllogall">Введите новый текст:</div><textarea class="inpu" name="replace"></textarea><div class="mgcler"></div>

<div class="fllogall">&nbsp;</div><input type="submit" value="Произвести замену" name="save" class="inp" style="margin-top:0px" />

</form>
HTML;

	echohtmlend();
}
?>