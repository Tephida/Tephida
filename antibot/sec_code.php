<?php
/* 
	Appointment: Выводим текущюю каптчу для AJAX
	File: sec_code.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
@session_start();

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

function clean_url($url){

  if ($url == '') return;

  $url = str_replace("http://", "", strtolower($url));
  $url = str_replace("https://", "", $url );
  if (substr($url, 0, 4) == 'www.')  $url = substr($url, 4);
  $url = explode('/', $url);
  $url = reset($url);
  $url = explode(':', $url);
  $url = reset($url);

  return $url;
  
}

if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST'])) 
	die("Hacking attempt!");

$user_code = $_GET['user_code'];

if($user_code == $_SESSION['sec_code']){
	echo 'ok';
} else {
	echo 'no';
}
?>