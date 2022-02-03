<?php
/* 
	Appointment: Восстановление доступа к странице
	File: restore.php 
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

if($ajax == 'yes')
	NoAjaxQuery();

if(!$logged){
	$act = $_GET['act'];
	$metatags['title'] = $lang['restore_title'];
	
	switch($act){
		
		//################### Проверка данных на воостановления ###################//
		case "next":
			NoAjaxQuery();
			$email = ajax_utf8(textFilter($_POST['email']));
			$check = $db->super_query("SELECT user_id, user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_email = '{$email}'");
			if($check){
				if($check['user_photo'])
					$check['user_photo'] = "/uploads/users/{$check['user_id']}/50_{$check['user_photo']}";
				else
					$check['user_photo'] = "{theme}/images/no_ava_50.png";
				
				echo $check['user_search_pref']."|".$check['user_photo'];
			} else
				echo 'no_user';
			
			die();
		break;
		
		//################### Отправка данных на почту на воостановления ###################//
		case "send":
			NoAjaxQuery();
			$email = ajax_utf8(textFilter($_POST['email']));
			$check = $db->super_query("SELECT user_name FROM `".PREFIX."_users` WHERE user_email = '{$email}'");
			if($check){
				//Удаляем все предыдущие запросы на воостановление
				$db->query("DELETE FROM `".PREFIX."_restore` WHERE email = '{$email}'");
				
				$salt = "abchefghjkmnpqrstuvwxyz0123456789";
				for($i = 0; $i < 15; $i++){
					$rand_lost .= $salt{rand(0, 33)};
				}
				$hash = md5($server_time.$email.rand(0, 100000).$rand_lost.$check['user_name']);

				//Вставляем в базу
				$db->query("INSERT INTO `".PREFIX."_restore` SET email = '{$email}', hash = '{$hash}', ip = '{$_IP}'");
				
				//Отправляем письмо на почту для воостановления
				include_once ENGINE_DIR.'/classes/mail.php';
				$mail = new dle_mail($config);
				$message = <<<HTML
Здравствуйте, {$check['user_name']}.

Чтобы сменить ваш пароль, пройдите по этой ссылке:
{$config['home_url']}restore?act=prefinish&h={$hash}

Мы благодарим Вас за участие в жизни нашего сайта.

{$config['home_url']}
HTML;
				$mail->send($email, $lang['lost_subj'], $message);
			}
			die();
		break;
		
		//################### Страница смены пароля ###################//
		case "prefinish":
			$hash = $db->safesql(strip_data($_GET['h']));
			$row = $db->super_query("SELECT email FROM `".PREFIX."_restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
			if($row){
				$info = $db->super_query("SELECT user_name FROM `".PREFIX."_users` WHERE user_email = '{$row['email']}'");
				$tpl->load_template('restore/prefinish.tpl');
				$tpl->set('{name}', $info['user_name']);
				
				$salt = "abchefghjkmnpqrstuvwxyz0123456789";
				for($i = 0; $i < 15; $i++){
					$rand_lost .= $salt{rand(0, 33)};
				}
				$newhash = md5($server_time.$row['email'].rand(0, 100000).$rand_lost);
				$tpl->set('{hash}', $newhash);
				$db->query("UPDATE `".PREFIX."_restore` SET hash = '{$newhash}' WHERE email = '{$row['email']}'");
				
				$tpl->compile('content');	
			} else {
				$speedbar = $lang['no_infooo'];
				msgbox('', $lang['restore_badlink'], 'info');
			}
		break;
		
		//################### Смена пароля ###################//
		case "finish":
			NoAjaxQuery();
			$hash = $db->safesql(strip_data($_POST['hash']));
			$row = $db->super_query("SELECT email FROM `".PREFIX."_restore` WHERE hash = '{$hash}' AND ip = '{$_IP}'");
			if($row){

				$_POST['new_pass'] = ajax_utf8($_POST['new_pass']);
				$_POST['new_pass2'] = ajax_utf8($_POST['new_pass2']);
				
				$new_pass = md5(md5($_POST['new_pass']));
				$new_pass2 = md5(md5($_POST['new_pass2']));
				
				if(strlen($new_pass) >= 6 AND $new_pass == $new_pass2){
					$db->query("UPDATE `".PREFIX."_users` SET user_password = '{$new_pass}' WHERE user_email = '{$row['email']}'");
					$db->query("DELETE FROM `".PREFIX."_restore` WHERE email = '{$row['email']}'");
				}
			}
			die();
		break;
		
		default:
			$tpl->load_template('restore/main.tpl');
			$tpl->compile('content');	
	}
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>