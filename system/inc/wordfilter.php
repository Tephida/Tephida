<?php
/* 
	Appointment: Фильтр слов
	File: wordfilter.php
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

//Добавление слова
if(isset($_POST['send'])){

	$word_find = trim(strip_tags(stripslashes($_POST['word_find'])));
	
	if($word_find == ""){
	
		msgbox('Информация', 'Введите слово', '?mod=wordfilter');
		
	}
	
	$word_replace = textFilter($_POST['word_replace']);
	
	$word_id = $server_time;
	
	$all_items = file(ENGINE_DIR.'/data/wordfilter.db.php');
	foreach($all_items as $item_line){
		$item_arr = explode("|", $item_line);
		if($item_arr[0] == $word_id){
		
			$word_id ++;
			
		}
	}
	
	foreach($all_items as $word_line){
		$word_arr = explode( "|", $word_line);
		if($word_arr[1] == $word_find){
			msgbox('Информация', 'Такое слово уже есть', '?mod=wordfilter');
			exit;
		}
	}
	
	$new_words = fopen(ENGINE_DIR.'/data/wordfilter.db.php', "a");
	$word_find = str_replace("|", "&#124", $word_find);
	$word_replace = str_replace("|", "&#124", $word_replace);

	$word_find = str_replace("$", "&#036;", $word_find);
	$word_find = str_replace("{", "&#123;", $word_find);
	$word_find = str_replace("}", "&#125;", $word_find);

	$word_replace = str_replace("$", "&#036;", $word_replace);
	$word_replace = str_replace("{", "&#123;", $word_replace);
	$word_replace = str_replace("}", "&#125;", $word_replace);
	
	fwrite($new_words, "$word_id|$word_find|$word_replace|".intval($_POST['type'])."|".intval($_POST['register'])."|".intval($_POST['filter_search'])."|".intval( $_POST['filter_action'])."||\n");
	fclose($new_words);
	
	header("Location: ?mod=wordfilter");
	
	exit;
	
}

//Удаление слова
if($_GET['act'] == 'del'){
	
	$word_id = intval($_REQUEST['wid']);
	
	if(!$word_id){
	
		msgbox('Информация', 'Такого слово нет', '?mod=wordfilter');
		
		exit;
	}
	
	$old_words = file(ENGINE_DIR.'/data/wordfilter.db.php');
	$new_words = fopen(ENGINE_DIR.'/data/wordfilter.db.php', "w");
	
	foreach($old_words as $old_words_line){
		$word_arr = explode("|", $old_words_line);
		if($word_arr[0] != $word_id){
			fwrite($new_words, $old_words_line);
		}
	}
	
	fclose($new_words);
	
	header("Location: ?mod=wordfilter");
	
}

echoheader();

echohtmlstart('Добавление нового слова в фильтр');

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form action="" method="POST">

<div class="fllogall">Введите слово:</div>
 <input type="text" name="word_find" class="inpu" />
<div class="mgcler"></div>

<div class="fllogall">заменить на:</div>
 <input type="text" name="word_replace" class="inpu" />
<div class="mgcler"></div>

<div class="fllogall" style="height:55px">&nbsp;</div>
 <span style="color:#777">Если Вы хотите, чтобы слово удалялось оставьте поле "заменить" пустым. Вы также можете использовать в поле "заменить" HTML код.</span>
<div class="mgcler"></div>

<div class="fllogall">Тип замены:</div>
 <select name="type" class="inpu">
  <option value="0">Любое вхождение</option><option value="1">Точное совпадение слова</option>
 </select>
<div class="mgcler"></div>

<div class="fllogall">С учетом регистра:</div>
 <select name="register" class="inpu">
  <option value="0">Нет</option><option value="1">Да</option>
 </select>
<div class="mgcler"></div>

<input type="hidden" value="0" name="filter_search" />
<input type="hidden" value="0" name="filter_action" />

<div class="fllogall">&nbsp;</div>
 <input type="submit" value="Сохранить" name="send" class="inp" style="margin-top:0px" />

</form>
HTML;

//Список слов
$all_words = file(ENGINE_DIR.'/data/wordfilter.db.php');

$count_words = 0;

usort($all_words, "compare_filter");

foreach($all_words as $word_line){
	
	$word_arr = explode("|", $word_line);
	
	$register = $word_arr[4] ? 'да' : 'нет';
	$type = $word_arr[3] ? 'Точное совпадение' : 'Любое вхождение';
	
	if(!$word_arr[2]) $word_arr[2] = '<font color="red">удалить</font>';
	
	$words .= <<<HTML
<div style="float:left;padding:5px;width:155px;text-align:center;border-bottom:1px dashed #ddd">{$word_arr[1]}</div>
<div style="float:left;padding:5px;width:155px;text-align:center;margin-left:1px;border-bottom:1px dashed #ddd">{$word_arr[2]}</div>
<div style="float:left;padding:5px;width:60px;text-align:center;margin-left:1px;border-bottom:1px dashed #ddd">{$register}</div>
<div style="float:left;padding:5px;width:100px;text-align:center;margin-left:1px;border-bottom:1px dashed #ddd">{$type}</div>
<div style="float:left;padding:5px;width:76px;text-align:center;margin-left:1px;border-bottom:1px dashed #ddd">
[ <a href="?mod=wordfilter&act=del&wid={$word_arr[0]}" title="Удалить">удалить</a> ]
</div>
<div class="clr"></div>
<div class="clr"></div>
HTML;
	
	$count_words++;
	
}

if(!$count_words) $words = '<br /><center><b>Список слов для фильтрации пуст</b></center><br />';

echohtmlstart('Слова');

echo <<<HTML
<div style="background:#f0f0f0;float:left;padding:5px;width:155px;text-align:center;font-weight:bold;margin-top:-5px">Слово</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:155px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Заменить на</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:60px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Регистр</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Тип замены</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:76px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Управление</div>
<div class="clr"></div>
{$words}
<div class="clr"></div>
HTML;

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);
echo navigation($gcount, $numRows['cnt'], '?'.$query_string.'&page=');

htmlclear();
echohtmlend();
?>