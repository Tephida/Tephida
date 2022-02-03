<?php
/* 
	Appointment: Временное отключение сайта
	File: offline.php
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die("Hacking attempt!");

if($user_info['user_group'] != '1'){
	$tpl->load_template('offline.tpl');
	$config['offline_msg'] = str_replace('&quot;', '"', stripslashes($config['offline_msg']));
	$tpl->set('{reason}', nl2br($config['offline_msg']));
	$tpl->compile('main');
	echo $tpl->result['main'];
	die();
}
?>