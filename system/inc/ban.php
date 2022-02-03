<?php
/* 
	Appointment: Фильтр по: IP, E-Mail
	File: ban.php
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

//Если добавляем
if(isset($_POST['save'])){
	$ban_date = intval($_POST['days']);
	$this_time = $ban_date ? $server_time + ($ban_date * 60 * 60 * 24) : 0;
	if($this_time) $always = 1; else $always = 0;
	if(isset($_POST['ip'])) $ip = $db->safesql(htmlspecialchars(strip_tags(trim($_POST['ip'])))); else $ip = "";
	$descr = textFilter($_POST['descr']);
	
	if($ip){
		$row = $db->super_query("SELECT id FROM `".PREFIX."_banned` WHERE ip ='".$ip."'");
		if($row){
			msgbox('Ошибка', 'Этот IP уже добавлен под фильтр', '?mod=ban');
		} else {
			$db->query("INSERT INTO `".PREFIX."_banned` SET descr = '".$descr."', date = '".$this_time."', always = '".$always."', ip = '".$ip."'");
			@unlink(ENGINE_DIR.'/cache/system/banned.php');
			header("Location: ?mod=ban");
		}
	} else
		msgbox('Ошибка', 'Укажите IP который нужно добавить под фильтр', 'javascript:history.go(-1)');
} else {
	echoheader();
	
	//Разблокировка
	if($_GET['act'] == 'unban'){
		$id = intval($_GET['id']);
		$db->query("DELETE FROM `".PREFIX."_banned` WHERE id = '".$id."'");
		@unlink(ENGINE_DIR.'/cache/system/banned.php');
		header("Location: ?mod=ban");
	}
	
	echohtmlstart('Добавление в фильтр IP адреса');
	echo <<<HTML
<style type="text/css" media="all">
.inpu{width:308px;}
textarea{width:300px;height:100px;}
</style>

Вы можете воспользоваться данным разделом, чтобы заблокировать определенные IP адреса. При входе IP адреса, то доступ на сайт данному IP или подсети закрывается полностью, а не только для регистрации.
<br /><br />
<b>Примечание:</b> вы можете воспользоваться в фильтре символом звездочки * для подстановки в IP адрес или электронный адрес (например: 127.0.*.*).

<form method="POST" action="" style="margin-top:15px">

<div class="fllogall">IP:</div><input type="text" name="ip" class="inpu" value="{$row['user_email']}" /><div class="mgcler"></div>

<div class="fllogall">Количество дней блокировки:<br /><small><b>0</b> неограничен по времени.</small></div><input type="text" name="days" class="inpu" value="{$row['user_name']}" /><div class="mgcler"></div>

<div class="fllogall">Причина блокировки:</div><textarea class="inpu" name="descr"></textarea><div class="mgcler"></div>

<div class="fllogall">&nbsp;</div><input type="submit" value="Сохранить" name="save" class="inp" style="margin-top:0px" />

</form>
HTML;

	echohtmlstart('Список заблокированных IP адресов');
	
	$sql_ = $db->super_query("SELECT id, descr, date, ip FROM `".PREFIX."_banned` ORDER by `id` DESC", 1);
	if($sql_){
		foreach($sql_ as $row){
			if($row['date'])
				$row['date'] = langdate('j F Y в H:i', $row['date']);
			else
				$row['date'] = 'Неограниченно';
				
			$row['descr'] = stripslashes($row['descr']);
			$short = substr(strip_tags($row['descr']), 0, 50).'..';
			$row['descr'] = myBrRn($row['descr']);
			
			$banList .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:150px;text-align:center;border-bottom:1px dashed #ccc">{$row['ip']}</div>
<div style="background:#fff;float:left;padding:5px;width:130px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc">{$row['date']}</div>
<div style="background:#fff;float:left;padding:5px;width:177px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc" title="{$row['descr']}">{$short}</div>
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc"><a href="?mod=ban&act=unban&id={$row['id']}">Разблокировать</a></div>
HTML;
		}
	} else
		$banList = '<center><b>Список пуст</b></center>';
		
	echo <<<HTML
<div style="background:#f0f0f0;float:left;padding:5px;width:150px;text-align:center;font-weight:bold;margin-top:-5px">IP</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:130px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Срок окончания бана</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:177px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Причина бана</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Действие</div>
{$banList}
HTML;

	echohtmlend();
}
?>