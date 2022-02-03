<?php
/* 
	Appointment: Загрузка городов
	File: loadcity.php 
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
	
$country_id = intval($_POST['country']);

echo '<option value="0">- Выбрать -</option>';

if($country_id){
	$sql_ = $db->super_query("SELECT id, name FROM `".PREFIX."_city` WHERE id_country = '{$country_id}' ORDER by `name` ASC", true, "country_city_".$country_id, true);
	foreach($sql_ as $row2) 
		echo '<option value="'.$row2['id'].'">'.stripslashes($row2['name']).'</option>';
}
?>
<script type="text/javascript">$('#load_mini').hide();</script>
<? die(); ?>