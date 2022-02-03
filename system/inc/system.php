<?php
/* 
	Appointment: Настройки системы
	File: system.php
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

//Если сохраянем
if(isset($_POST['saveconf'])){
	$saves = $_POST['save'];

	$find[] = "'\r'";
	$replace[] = "";
	$find[] = "'\n'";
	$replace[] = "";
	
	$handler = fopen(ENGINE_DIR.'/data/config.php', "w");
	fwrite($handler, "<?php \n\n//System Configurations\n\n\$config = array (\n\n");

	foreach($saves as $name => $value ) {
	
		if($name != "offline_msg" AND $name != "lang_list"){
			$value = trim(stripslashes($value));
			$value = htmlspecialchars($value, ENT_QUOTES);
			$value = preg_replace($find, $replace, $value);
			
			$name = trim(stripslashes($name));
			$name = htmlspecialchars($name, ENT_QUOTES);
			$name = preg_replace($find, $replace, $name);
		}
		
		$value = str_replace("$", "&#036;", $value);
		$value = str_replace("{", "&#123;", $value);
		$value = str_replace("}", "&#125;", $value);
		
		$name = str_replace("$", "&#036;", $name);
		$name = str_replace("{", "&#123;", $name);
		$name = str_replace("}", "&#125;", $name);
		
		$value = $db->safesql($value);
		
		fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
	}
	fwrite($handler, ");\n\n?>" );
	fclose($handler);
	
	msgbox('Настройки сохранены', 'Настройки системы были успешно сохранены!', '?mod=system');
} else {
	echoheader();
	echohtmlstart('Общие настройки');

	//Чтение всех шаблон в папке "templates"
	$root = './templates/';
	$root_dir = scandir($root);
	foreach($root_dir as $templates){
		if($templates != '.' AND $templates != '..' AND $templates != '.htaccess')
			$for_select .= str_replace('value="'.$config['temp'].'"', 'value="'.$config['temp'].'" selected', '<option value="'.$templates.'">'.$templates.'</option>');
	}
	
	//Чтение всех языков
	$root_dir2 = scandir('./lang/');
	foreach($root_dir2 as $lang){
		if($lang != '.' AND $lang != '..' AND $lang != '.htaccess')
			$for_select_lang .= str_replace('value="'.$config['lang'].'"', 'value="'.$config['lang'].'" selected', '<option value="'.$lang.'">'.$lang.'</option>');
	}
	
	//GZIP
	$for_select_gzip = installationSelected($config['gzip'], '<option value="yes">Да</option><option value="no">Нет</option>');
	
	//GZIP JS
	$for_select_gzip_js = installationSelected($config['gzip_js'], '<option value="yes">Да</option><option value="no">Нет</option>');
	
	//Offline
	$for_select_offline = installationSelected($config['offline'], '<option value="yes">Да</option><option value="no">Нет</option>');
	
	$config['offline_msg'] = stripslashes($config['offline_msg']);
	
	echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form method="POST" action="">

<div class="fllogall">Название сайта:</div><input type="text" name="save[home]" class="inpu" value="{$config['home']}" /><div class="mgcler"></div>

<div class="fllogall">Используемая кодировка на сайте:</div><input type="text" name="save[charset]" class="inpu" value="{$config['charset']}" /><div class="mgcler"></div>

<div class="fllogall">Адрес сайта:</div><input type="text" name="save[home_url]" class="inpu" value="{$config['home_url']}" /><div class="mgcler"></div>

<div class="fllogall">Шаблон сайта по умолчанию:</div><select name="save[temp]" class="inpu" style="width:auto">{$for_select}</select><div class="mgcler"></div>

<div class="fllogall">Время онлайна людей в секундах:</div><input type="text" name="save[online_time]" class="inpu" value="{$config['online_time']}" /><div class="mgcler"></div>

<div class="fllogall">Используемый язык:</div><select name="save[lang]" class="inpu" style="width:auto">{$for_select_lang}</select><div class="mgcler"></div>

<div class="fllogall">Включить Gzip сжатие HTML страниц:</div><select name="save[gzip]" class="inpu" style="width:auto">{$for_select_gzip}</select><div class="mgcler"></div>

<div class="fllogall">Включить Gzip сжатие JS файлов:</div><select name="save[gzip_js]" class="inpu" style="width:auto">{$for_select_gzip_js}</select><div class="mgcler"></div>

<div class="fllogall">Выключить сайт:</div><select name="save[offline]" class="inpu" style="width:auto">{$for_select_offline}</select><div class="mgcler"></div>

<div class="fllogall">Причина отключения сайта:</div><textarea class="inpu" name="save[offline_msg]">{$config['offline_msg']}</textarea>

<div class="fllogall">Список используемых языков (название папок): <br /><br />пример: <b>Русский | Russian</b></div><textarea class="inpu" name="save[lang_list]">{$config['lang_list']}</textarea>

<div class="fllogall">Бонусный рейтинг за подарок (цена подарка):</div><input type="text" name="save[bonus_rate]" class="inpu" value="{$config['bonus_rate']}" /><div class="mgcler"></div>

<div class="fllogall">Стоимость 1 голоса:</div><input type="text" name="save[cost_balance]" class="inpu" value="{$config['cost_balance']}" /><div class="mgcler"></div>
HTML;

	//Video mod
	echohtmlstart('<a name="video"></a>Настройки видео');

	$for_select_video_mod = installationSelected($config['video_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_comm = installationSelected($config['video_mod_comm'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_add = installationSelected($config['video_mod_add'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_add_my = installationSelected($config['video_mod_add_my'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_privat = installationSelected($config['video_mod_privat'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_del = installationSelected($config['video_mod_del'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_video_mod_search = installationSelected($config['video_mod_search'], '<option value="yes">Да</option><option value="no">Нет</option>');
	
	echo <<<HTML
<div class="fllogall">Выключить модуль:</div><select name="save[video_mod]" class="inpu" style="width:auto">{$for_select_video_mod}</select><div class="mgcler"></div>
		
<div class="fllogall">Разрешить комментирование видео:</div><select name="save[video_mod_comm]" class="inpu" style="width:auto">{$for_select_video_mod_comm}</select><div class="mgcler"></div>
		
<div class="fllogall">Разрешить добавление видео:</div><select name="save[video_mod_add]" class="inpu" style="width:auto">{$for_select_video_mod_add}</select><div class="mgcler"></div>
		
<div class="fllogall">Включить функцию "Добавить в Мои Видеозаписи":</div><select name="save[video_mod_add_my]" class="inpu" style="width:auto">{$for_select_video_mod_add_my}</select><div class="mgcler"></div>

<div class="fllogall">Разрешить поиск по видео:</div><select name="save[video_mod_search]" class="inpu" style="width:auto">{$for_select_video_mod_search}</select>
HTML;

	//Audio mod
	echohtmlstart('<a name="audio"></a>Настройки аудио');

	$for_select_audio_mod = installationSelected($config['audio_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_audio_mod_add = installationSelected($config['audio_mod_add'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_audio_mod_add_my = installationSelected($config['audio_mod_add_my'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_audio_mod_search = installationSelected($config['audio_mod_search'], '<option value="yes">Да</option><option value="no">Нет</option>');

	echo <<<HTML
<div class="fllogall">Выключить модуль:</div><select name="save[audio_mod]" class="inpu" style="width:auto">{$for_select_audio_mod}</select><div class="mgcler"></div>

<div class="fllogall">Разрешить добавление музыки:</div><select name="save[audio_mod_add]" class="inpu" style="width:auto">{$for_select_audio_mod_add}</select><div class="mgcler"></div>

<div class="fllogall">Разрешить поиск по музыке:</div><select name="save[audio_mod_search]" class="inpu" style="width:auto">{$for_select_audio_mod_search}</select>
HTML;

	//Photo mod
	echohtmlstart('<a name="photos"></a>Настройки фото');
	
	$for_select_album_mod = installationSelected($config['album_mod'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_albums_drag = installationSelected($config['albums_drag'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_photos_drag = installationSelected($config['photos_drag'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_photos_comm = installationSelected($config['photos_comm'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_photos_load = installationSelected($config['photos_load'], '<option value="yes">Да</option><option value="no">Нет</option>');

	echo <<<HTML
<div class="fllogall">Выключить модуль "Альбомы":</div><select name="save[album_mod]" class="inpu" style="width:auto">{$for_select_album_mod}</select><div class="mgcler"></div>

<div class="fllogall">Максимальное количество альбомов:</div><input type="text" name="save[max_albums]" class="inpu" value="{$config['max_albums']}" /><div class="mgcler"></div>

<div class="fllogall">Максимальное количество фото в один альбом:</div><input type="text" name="save[max_album_photos]" class="inpu" value="{$config['max_album_photos']}" /><div class="mgcler"></div>

<div class="fllogall">Максимальный размер загужаемой фотографии (кб):</div><input type="text" name="save[max_photo_size]" class="inpu" value="{$config['max_photo_size']}" /><div class="mgcler"></div>

<div class="fllogall">Расширение фотографий, допустимых к загрузке:<br /><small>Например: <b>jpg, jpeg, png</b></small></div><input type="text" name="save[photo_format]" class="inpu" value="{$config['photo_format']}" /><div class="mgcler"></div>

<div class="fllogall">Разрешить менять порядок альбомов:</div><select name="save[albums_drag]" class="inpu" style="width:auto">{$for_select_albums_drag}</select><div class="mgcler"></div>

<div class="fllogall">Разрешить менять порядок фотографий:</div><select name="save[photos_drag]" class="inpu" style="width:auto">{$for_select_photos_drag}</select><div class="mgcler"></div>

<div class="fllogall">Стоимость оценки <b>5+</b>:</div><input type="text" name="save[rate_price]" class="inpu" value="{$config['rate_price']}" /><div class="mgcler"></div>
HTML;

	//E-mail
	echohtmlstart('Настройки E-Mail');
	
	$for_select_mail_metod = installationSelected($config['mail_metod'], '<option value="php">PHP Mail()</option><option value="smtp">SMTP</option>');
		
	echo <<<HTML
<div class="fllogall">E-Mail адрес администратора:</div><input type="text" name="save[admin_mail]" class="inpu" value="{$config['admin_mail']}" /><div class="mgcler"></div>

<div class="fllogall">Метод отправки почты:</div><select name="save[mail_metod]" class="inpu" style="width:auto">{$for_select_mail_metod}</select><div class="mgcler"></div>

<div class="fllogall">SMTP хост:</div><input type="text" name="save[smtp_host]" class="inpu" value="{$config['smtp_host']}" /><div class="mgcler"></div>

<div class="fllogall">SMTP порт:</div><input type="text" name="save[smtp_port]" class="inpu" value="{$config['smtp_port']}" /><div class="mgcler"></div>

<div class="fllogall">SMTP Имя Пользователя:</div><input type="text" name="save[smtp_user]" class="inpu" value="{$config['smtp_user']}" /><div class="mgcler"></div>

<div class="fllogall">SMTP Пароль:</div><input type="text" name="save[smtp_pass]" class="inpu" value="{$config['smtp_pass']}" /><div class="mgcler"></div>
HTML;

	//Настройки E-mail оповещаний
	echohtmlstart('Настройки E-Mail оповещаний');
	
	$for_select_news_mail_1 = installationSelected($config['news_mail_1'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_2 = installationSelected($config['news_mail_2'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_3 = installationSelected($config['news_mail_3'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_4 = installationSelected($config['news_mail_4'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_5 = installationSelected($config['news_mail_5'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_6 = installationSelected($config['news_mail_6'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_7 = installationSelected($config['news_mail_7'], '<option value="yes">Да</option><option value="no">Нет</option>');
	$for_select_news_mail_8 = installationSelected($config['news_mail_8'], '<option value="yes">Да</option><option value="no">Нет</option>');

	echo <<<HTML

<div class="fllogall">Включить уведомление при новой заявки в друзья:</div><select name="save[news_mail_1]" class="inpu" style="width:auto">{$for_select_news_mail_1}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при ответе на запись:</div><select name="save[news_mail_2]" class="inpu" style="width:auto">{$for_select_news_mail_2}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при комментировании видео:</div><select name="save[news_mail_3]" class="inpu" style="width:auto">{$for_select_news_mail_3}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при комментировании фото:</div><select name="save[news_mail_4]" class="inpu" style="width:auto">{$for_select_news_mail_4}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при комментировании заметки:</div><select name="save[news_mail_5]" class="inpu" style="width:auto">{$for_select_news_mail_5}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при новом подарке:</div><select name="save[news_mail_6]" class="inpu" style="width:auto">{$for_select_news_mail_6}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при новой записи на стене:</div><select name="save[news_mail_7]" class="inpu" style="width:auto">{$for_select_news_mail_7}</select><div class="mgcler"></div>

<div class="fllogall">Включить уведомление при новом персональном сообщении:</div><select name="save[news_mail_8]" class="inpu" style="width:auto">{$for_select_news_mail_8}</select><div class="mgcler"></div>
HTML;

	//Настройки пополнения через SMS
	echohtmlstart('Настройки пополнения через SMS (smsbill.ru)');
	
	echo <<<HTML
<div class="fllogall">Кодовое слово:</div><input type="text" name="save[code_word]" class="inpu" value="{$config['code_word']}" /><div class="mgcler"></div>

<div class="fllogall">Короткий номер для оплаты:</div><input type="text" name="save[sms_number]" class="inpu" value="{$config['sms_number']}" /><div class="mgcler"></div>
HTML;

	echo <<<HTML

<div class="fllogall">&nbsp;</div><input type="submit" value="Сохранить" name="saveconf" class="inp" style="margin-top:0px" />

</form>
HTML;

	htmlclear();
	echohtmlend();
}
?>