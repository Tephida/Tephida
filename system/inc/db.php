<?php
/* 
	Appointment: Управление БД
	File: db.php
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

if(isset($_POST['action']) AND count($_REQUEST['ta'])){
	$arr = $_REQUEST['ta'];
	reset($arr);
	
	$tables = "";
	
	while(list($key, $val) = each($arr)){
		$tables .= ", `" . $db->safesql( $val ) . "`";
	}
	
	$tables = substr($tables, 1);
	
	if($_REQUEST['whattodo'] == "optimize"){
		$query = "OPTIMIZE TABLE  ";
	} else {
		$query = "REPAIR TABLE ";
	}
	$query .= $tables;
	
	$db->query($query);
	
	msgbox('Информация', 'Выбранное действие успешно выполнено', '?mod=db');
		
	exit;
}

echoheader();
echohtmlstart('Сохранение резервной копии');

echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
function save(){
	var rndval = new Date().getTime(); 
	$("#progress").html("<iframe width='99%' height='220' src='/{$admin_index}?mod=dumper&action=backup&comp_method=" + $("#comp_method").val() + "&rndval=" + rndval + "' frameborder='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>");
}
function dbload(){
	var rndval = new Date().getTime(); 
	$("#progress2").html("<iframe width='99%' height='220' src='/{$admin_index}?mod=dumper&action=restore&file=" + $("#file").val() + "&rndval=" + rndval + "' frameborder='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>");
}
</script>
Выберете метод сжатия базы данных: <select name="comp_method" id="comp_method" class="inpu"><OPTION VALUE='1'>GZip<OPTION VALUE='0' SELECTED>Без сжатия</select>
<input type="submit" value="Сохранить" name="saveconf" class="inp" style="margin-top:0px" onClick="save(); return false;" />
<div id="progress"></div>
HTML;

echohtmlstart('Загрузка резервной копии с диска');

function fn_select($items, $selected){
	$select = '';
	foreach ($items as $key => $value){
		$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
	}
	return $select;
}

define('PATH', 'backup/');

function file_select(){
	$files = array('');
	if(is_dir(PATH) AND $handle = opendir(PATH)){
		while(false !== ($file = readdir($handle))){
			if(preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)){
				$files[$file] = $file;
			}
		}
		closedir($handle);
	}
	return $files;
}

$files = fn_select(file_select(), '');

echo <<<HTML
Выберите резервную копию базы данных: <select name="file" id="file" class="inpu">{$files}</select>
<input type="submit" value="Воостановить БД" name="saveconf" class="inp" style="margin-top:0px" onClick="dbload(); return false;" />
<div id="progress2"></div>
HTML;

echohtmlstart('Настройка и оптимизация базы данных');

$tabellen = "";
$db->query("SHOW TABLES");
while($row = $db->get_array()){

	$titel = $row[0];
	
	if(substr($titel, 0, strlen(PREFIX)) == PREFIX){
		$tabellen .= "<option value=\"$titel\" selected>$titel</option>\n";
	}

}
$db->free();

echo <<<HTML
<form method="POST" action="">
<select style="width:240px;height:230px;margin-right:10px;float:left" size="7" name="ta[]" class="inpu" multiple="multiple">{$tabellen}</select> 

<input type="radio" name="whattodo" style="margin-right:5px" value="optimize" checked /><b>Оптимизация базы данных</b><br />
<div style="margin-left:273px;margin-bottom:15px">Вы можете произвести оптимизацию базы данных, тем самым будет сэкономлено немного места на диске, а также ускорена работа базы данных. Рекомендуется использовать данную функцию минимум один раз в неделю.</div>

<input type="radio" name="whattodo" style="margin-right:5px" /><b>Ремонт базы данных</b><br />
<div style="margin-left:273px;">При неожиданной остановке MySQL сервера, во время выполнения каких-либо действий, может произойти повреждение структуры таблиц, использование этой функции поможет решить вам эту проблему.</div>

<input type="submit" value="Выолнить действие" name="action" class="inp" style="margin-top:10px;margin-left:21px" />
</form>
HTML;

echohtmlend();
?>