<?php
/* 
	Appointment: Доп. поля профилей
	File: xfields.php
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

function profilesave($data) {

	$data = array_values($data);
	$filecontents = "";

    foreach($data as $index => $value){
		$value = array_values($value);
		foreach($value as $index2 => $value2){
			$value2 = stripslashes($value2);
			$value2 = str_replace("|", "&#124;", $value2);
			$value2 = str_replace("\r\n", "__NEWL__", $value2);
			$filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
		}
		$filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
    }
  
    $filehandle = fopen(ENGINE_DIR.'/data/xfields.txt', "w+");
    if(!$filehandle){
		msgbox('Информация', 'Невозможно загрузить файл', 'javascript:history.go(-1)');
		exit;
	}

	$find = array ('/data:/i', '/about:/i', '/vbscript:/i', '/onclick/i', '/onload/i', '/onunload/i', '/onabort/i', '/onerror/i', '/onblur/i', '/onchange/i', '/onfocus/i', '/onreset/i', '/onsubmit/i', '/ondblclick/i', '/onkeydown/i', '/onkeypress/i', '/onkeyup/i', '/onmousedown/i', '/onmouseup/i', '/onmouseover/i', '/onmouseout/i', '/onselect/i', '/javascript/i', '/javascript/i');
	$replace = array ("d&#097;ta:", "&#097;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#097;vascript");
	
	$filecontents = preg_replace($find, $replace, $filecontents);
	$filecontents = preg_replace("#<iframe#i", "&lt;iframe", $filecontents);
	$filecontents = preg_replace("#<script#i", "&lt;script", $filecontents);
	$filecontents = str_replace("<?", "&lt;?", $filecontents);
	$filecontents = str_replace("?>", "?&gt;", $filecontents);
	$filecontents = str_replace("$", "&#036;", $filecontents);

    fwrite($filehandle, $filecontents);
    fclose($filehandle);
    header("Location: ?mod=xfields");
    exit;
}

function profileload() {
	$path = ENGINE_DIR.'/data/xfields.txt';
	$filecontents = file($path);

	if(!is_array($filecontents)){
		msgbox('Информация', 'Невозможно загрузить файл', 'javascript:history.go(-1)');
		exit;
	}
	
	foreach($filecontents as $name => $value){
		$filecontents[$name] = explode("|", trim($value));
		foreach($filecontents[$name] as $name2 => $value2){
			$value2 = str_replace("&#124;", "|", $value2); 
			$value2 = str_replace("__NEWL__", "\r\n", $value2);
			$filecontents[$name][$name2] = $value2;
		}
    }
    return $filecontents;
}

function array_move(&$array, $index1, $dist){
	$index2 = $index1 + $dist;
	if($index1 < 0 or
        $index1 > count($array) - 1 or
        $index2 < 0 or
        $index2 > count($array) - 1) {
      return false;
    }
	$value1 = $array[$index1];
  
	$array[$index1] = $array[$index2];
	$array[$index2] = $value1;
  
	return true;
}

$xfields = profileload();

//**************** Удаление поля ****************** //
if($_GET['act'] == 'del'){
	
	$xfieldsindex = intval($_GET['id']);
	
	unset($xfields[$xfieldsindex]);

	@profilesave($xfields);
		
	exit;
}
  
//**************** Если нажали кнопку "Добавить" ****************** //
if(isset($_POST['save'])){

	$editedxfield = $_POST['editedxfield'];
	
	$xfieldsindex = count($xfields);
		
	if(strlen(trim($editedxfield[0])) > 0 AND strlen(trim($editedxfield[1])) > 0){

		$editedxfield[0] = totranslit(trim($editedxfield[0]));
		$editedxfield[0] = str_replace('-', '_', $editedxfield[0]);
		$editedxfield[1] = htmlspecialchars(trim($editedxfield[1]));
	
		foreach($xfields as $name => $value){
		
			if($name != $xfieldsindex AND $value[0] == $editedxfield[0]){
			
				msgbox('Информация', 'Полe с таким названием уже существует!', 'javascript:history.go(-1)');
				exit;
				
			}
			
		}
		
		if($editedxfield[3] == "select"){
		
			$options = array();
			foreach(explode("\r\n", $editedxfield["6_select"]) as $name => $value){
				$value = trim($value);
				if(!in_array($value, $options)){
					$options[] = $value;
				}
			}
			
			if(count($options) < 2){
				msgbox('Информация', 'Если Вы выбираете список, то Вы должны вписать два или более пункта с различным значением!', 'javascript:history.go(-1)');
				exit;
			}
			
			$editedxfield[6] = implode("\r\n", $options);
			
		} else { 
			$editedxfield[6] = ""; 
		}

		unset($editedxfield['6_select']);
			  
		ksort($editedxfield);
			  
		$xfields[$xfieldsindex] = $editedxfield;
		ksort($xfields);

		@profilesave($xfields);
	
	} else {
	
		msgbox('Информация', 'Введите название и описание поля!', 'javascript:history.go(-1)');
		exit;
		
	}
		  
}

//**************** Двигаем поле вверх ****************** //
if($_GET['act'] == 'moveup'){

	$xfieldsindex = intval($_GET['id']);
	
	if(!isset($xfieldsindex)){
		msgbox('Информация', 'Выберите поле, которое хотите сдвинуть!', 'javascript:history.go(-1)');
		exit;
	}
	
	array_move($xfields, $xfieldsindex, -1);
	@profilesave($xfields);
		
}

//**************** Двигаем поле вниз ****************** //
if($_GET['act'] == 'movedown'){

	$xfieldsindex = intval($_GET['id']);
	
	if(!isset($xfieldsindex)){
		msgbox('Информация', 'Выберите поле, которое хотите сдвинуть!', 'javascript:history.go(-1)');
		exit;
	}
	
	array_move($xfields, $xfieldsindex, +1);
	@profilesave($xfields);
		
}

//**************** Редактирование поля ****************** //
if($_GET['act'] == 'edit'){
	
	$xfieldsindex = intval($_GET['id']);
	
	$editedxfield = $xfields[$xfieldsindex];
	
	//**************** Если нажали кнопку "Сохранить" ****************** //
	if(isset($_POST['edit_save'])){
		$editedxfield = $_POST['editedxfield'];
		
		if(strlen(trim($editedxfield[0])) > 0 AND strlen(trim($editedxfield[1])) > 0){
			
			$editedxfield[0] = totranslit(trim($editedxfield[0]));
			$editedxfield[0] = str_replace('-', '_', $editedxfield[0]);
			$editedxfield[1] = htmlspecialchars(trim($editedxfield[1]));
			
			foreach($xfields as $name => $value){
			
				if($name != $xfieldsindex AND $value[0] == $editedxfield[0]){
				
					msgbox('Информация', 'Полe с таким названием уже существует!', 'javascript:history.go(-1)');
					exit;
					
				}
				
			}
			
			if($editedxfield[3] == "select"){
			
				$options = array();
				foreach(explode("\r\n", $editedxfield["6_select"]) as $name => $value){
					$value = trim($value);
					if(!in_array($value, $options)){
						$options[] = $value;
					}
				}
				
				if(count($options) < 2){
					msgbox('Информация', 'Если Вы выбираете список, то Вы должны вписать два или более пункта с различным значением!', 'javascript:history.go(-1)');
					exit;
				}
				
				$editedxfield[6] = implode("\r\n", $options);
				
			} else { 
				$editedxfield[6] = ""; 
			}

			ksort($editedxfield);
				  
			$xfields[$xfieldsindex] = $editedxfield;
			ksort($xfields);

			@profilesave($xfields);

		} else {
	
			msgbox('Информация', 'Введите название и описание поля!', 'javascript:history.go(-1)');
			exit;
			
		}
	}
	
	if($editedxfield[2] == 'text') $selected = 'selected'; else $selected = '';
	if($editedxfield[2] == 'textarea') $selected = 'selected'; else $selected = '';
	if($editedxfield[2] == 'select') { $selected = 'selected'; $disp = '';} else { $selected = ''; $disp = 'display:none';}
	
	echoheader();
	echohtmlstart('Редактирование дополнительного поля');

	echo <<<HTML
<form method="POST" action="">

<input type="hidden" value="{$xfieldsindex}" name="xfieldsindex" />

<div class="fllogall" style="width:150px">Название поля:</div>
 <input type="text" name="editedxfield[0]" class="inpu" value="{$editedxfield[0]}" />
 &nbsp;(Латинскими буквами)
<div class="mgcler"></div>

<div class="fllogall" style="width:150px">Описание поля:</div>
 <input type="text" name="editedxfield[1]" class="inpu" value="{$editedxfield[1]}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:150px">Тип поля:</div>
 <select name="editedxfield[3]" class="inpu" onChange="if(this.value == 'select') document.getElementById('t').style.display = 'block'; else {document.getElementById('t').style.display = 'none';document.getElementById('6_select').value = '';}">
  <option value="text" {$selected}>Одна строка</option>
  <option value="textarea" {$selected}>Несколько строк</option>
  <option value="select" {$selected}>Список</option>
 </select>
<div class="mgcler"></div>

<div id="t" style="{$disp}">
<div class="fllogall" style="width:150px">Значение по умолчанию: <br /><br />(на одной строке одно значение)</div>
 <textarea name="editedxfield[6_select]" id="6_select" class="inpu">{$editedxfield[3]}</textarea>
<div class="mgcler"></div>
</div>

<div class="fllogall" style="width:150px">&nbsp;</div>
 <input type="submit" value="Сохранить" class="inp" name="edit_save" style="margin-top:0px" />
 <input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />

</form>
HTML;
		
	echohtmlend();
	exit;
}

//**************** Выводим все созданые поля ****************** //
foreach($xfields as $name => $value){
	
	if($value[2] == 'text') $type = 'Одна строка';
	elseif($value[2] == 'textarea') $type = 'Несколько строк';
	else $type = 'Список';
	
	$fielst_list .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:150px;text-align:center;border-bottom:1px dashed #ccc"><a href="?mod=xfields&act=edit&id={$name}">{$value[0]} </a>&nbsp;&nbsp;&nbsp;<a href="?mod=xfields&act=del&id={$name}" title="Удалить"><b>x</b></a>&nbsp;&nbsp;&nbsp;<a href="?mod=xfields&act=moveup&id={$name}" title="Сдвинуть вверх"><b>&#8593;</b></a>&nbsp;&nbsp;&nbsp;<a href="?mod=xfields&act=movedown&id={$name}" title="Сдвинуть вниз"><b>&#8595;</b></a></div>
<div style="background:#fff;float:left;padding:5px;width:150px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc">{$value[1]}</div>
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc">{$type}</div>
<div style="background:#fff;float:left;padding:5px;width:157px;text-align:center;margin-left:1px;border-bottom:1px dashed #ccc">
<input type="text" value="[xfgiven_{$value[0]}]{$value[1]}: [xfvalue_{$value[0]}][/xfgiven_{$value[0]}]" class="inpu" style="padding:2px;margin-bottom:-1px;margin-top:-5px;width:150px" onClick="this.select()" />
</div>
HTML;
	
}

if(!$fielst_list)
	$fielst_list = '<div align="center" style="font-size:13px;margin-top:30px">Дополнительных полей нет!</div>';

echoheader();
echohtmlstart('Список дополнительных полей профиля пользователей');

echo <<<HTML
<div style="background:#f0f0f0;float:left;padding:5px;width:150px;text-align:center;font-weight:bold;margin-top:-5px">Название поля</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:150px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Описание поля</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Тип поля</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:157px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Пример использования</div>
{$fielst_list}
<div class="clr"></div>
HTML;

echohtmlstart('Добавление нового дополнительного поля');

echo <<<HTML
<form method="POST" action="">

<div class="fllogall" style="width:150px">Название поля:</div>
 <input type="text" name="editedxfield[0]" class="inpu" />
 &nbsp;(Латинскими буквами)
<div class="mgcler"></div>

<div class="fllogall" style="width:150px">Описание поля:</div>
 <input type="text" name="editedxfield[1]" class="inpu" />
<div class="mgcler"></div>

<div class="fllogall" style="width:150px">Тип поля:</div>
 <select name="editedxfield[3]" class="inpu" onChange="if(this.value == 'select') document.getElementById('t').style.display = 'block'; else {document.getElementById('t').style.display = 'none';document.getElementById('6_select').value = '';}">
  <option value="text" selected>Одна строка</option>
  <option value="textarea">Несколько строк</option>
  <option value="select">Список</option>
 </select>
<div class="mgcler"></div>

<div id="t" style="display:none">
<div class="fllogall" style="width:150px">Значение по умолчанию: <br /><br />(на одной строке одно значение)</div>
 <textarea name="editedxfield[6_select]" id="6_select" class="inpu"></textarea>
<div class="mgcler"></div>
</div>

<div class="fllogall" style="width:150px">&nbsp;</div>
 <input type="submit" value="Добавить" class="inp" name="save" style="margin-top:0px" />

</form>
HTML;

echohtmlstart('Что такое дополнительные поля?');

echo <<<HTML
Данная функция предназначена для добавление новых полей в профиль пользователей, помимо существующих основных.

Добавить новое поле можно, в окне которое находится выше "Добавление нового дополнительного поля". Вам необходимо ввести уникальное имя поля и описание поля.
<br /><br />
Дополнительные поля доступны при просмотре профиля пользователя, Вам необходимо в нужные шаблоны добавить тег [xfvalue_X], где X - значение поля (имя, которое Вы ввели при добавлении нового поля). Также можно использовать связку [xfgiven_X]...[/xfgiven_X].<br /><br />
Пример работы полей:
<div style="padding:5px;border:1px solid #ccc;background:lightyellow;margin-bottom:5px;margin-top:5px">- Название: city<br />
- Описание: Город<br />
- Тип поля: Одна строка<br />
<br />
в шаблон profile.tpl добавить<br />
[xfgiven_city] Город: [xfvalue_city] [/xfgiven_city]</div>
Если пользователь добавил в своем профиле город например "Москва", то итогом работы будет следующий HTML-код:
<div style="padding:5px;border:1px solid #ccc;background:lightyellow;margin-top:5px;margin-bottom:5px">Город: Москва</div>
Запомните, что для названия поля нужно использовать только латинские буквы и цифры.
HTML;

echohtmlend();
?>