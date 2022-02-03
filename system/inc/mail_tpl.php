<?php
/* 
	Appointment: Шаблоны сообщений
	File: mail_tpl.php
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

	
if(isset($_POST['save'])){
	$find = array ("<", ">");
	$replace = array ("&lt;", "&gt;");
	for($i = 1; $i <= 8; $i++){
		$post = $db->safesql(str_replace($find, $replace, $_POST[$i]));
		$db->query("UPDATE `".PREFIX."_mail_tpl` SET text = '".$post."' WHERE id = '".$i."'");
	}
}

$row1 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '1'");
$row2 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '2'");
$row3 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '3'");
$row4 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '4'");
$row5 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '5'");
$row6 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '6'");
$row7 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '7'");
$row8 = $db->super_query("SELECT text FROM `".PREFIX."_mail_tpl` WHERE id = '8'");

echoheader();
	
echo <<<HTML
<style type="text/css" media="all">
.inpu{width:590px;height:150px;margin-top:5px}
</style>
<form method="POST" action="">
HTML;

echohtmlstart('1. Настройка E-Mail сообщения, которое отсылается при новой заявки в друзья');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который отправил заявку на дружбу
<textarea class="inpu" name="1">{$row1['text']}</textarea>
HTML;
		
echohtmlstart('2. Настройка E-Mail сообщения, которое отсылается при ответе на запись');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который ответил<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на запись
<textarea class="inpu" name="2">{$row2['text']}</textarea>
HTML;
		
echohtmlstart('3. Настройка E-Mail сообщения, которое отсылается при новом комментарии к видео');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который оставил комментарий<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на видеозапись
<textarea class="inpu" name="3">{$row3['text']}</textarea>
HTML;
		
echohtmlstart('4. Настройка E-Mail сообщения, которое отсылается при новом комментарии к фото');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который оставил комментарий<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на фотографию
<textarea class="inpu" name="4">{$row4['text']}</textarea>
HTML;
HTML;
		
echohtmlstart('5. sНастройка E-Mail сообщения, которое отсылается при новом комментарии к заметке');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который оставил комментарий<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на заметку
<textarea class="inpu" name="5">{$row5['text']}</textarea>
HTML;
		
echohtmlstart('6. Настройка E-Mail сообщения, которое отсылается при новом подарке');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который отправил подарок<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на подарки
<textarea class="inpu" name="6">{$row6['text']}</textarea>
HTML;
		
echohtmlstart('7. Настройка E-Mail сообщения, которое отсылается при новой записи на стене');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который оставил запись<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на запись
<textarea class="inpu" name="7">{$row7['text']}</textarea>
HTML;
		
echohtmlstart('8. Настройка E-Mail сообщения, которое отсылается при новом персональном сообщении');
echo <<<HTML
<b>{%user%}</b> &nbsp;-&nbsp; имя пользователя которому предназначено оповещание<br />
<b>{%user-friend%}</b> &nbsp;-&nbsp; пользователь который отправил сообщение<br />
<b>{%rec-link%}</b> &nbsp;-&nbsp; ссылка на сообщение
<textarea class="inpu" name="8">{$row8['text']}</textarea>
HTML;
HTML;

echo <<<HTML
<input type="submit" value="Сохранить" name="save" class="inp" style="margin-top:5px" />
</form>
HTML;

echohtmlend();
?>