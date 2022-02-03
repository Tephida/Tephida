<?php
/* 
	Appointment: Антивирус
	File: antivirus.php
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

if($_GET['act'] == 'start'){
	require_once ENGINE_DIR.'/classes/antivirus.php';
	$antivirus = new antivirus();

	if($_REQUEST['folder'] == "lokal"){
		$antivirus->scan_files(ROOT_DIR."/backup", false, true);
		$antivirus->scan_files(ROOT_DIR."/system", false, true);
		$antivirus->scan_files(ROOT_DIR."/lang", false, true);
		$antivirus->scan_files(ROOT_DIR."/min", false, true);
		$antivirus->scan_files(ROOT_DIR."/templates", false, false);
		$antivirus->scan_files(ROOT_DIR."/uploads", false, true);
		$antivirus->scan_files(ROOT_DIR."/antibot", false, true);
		$antivirus->scan_files(ROOT_DIR, false, true);
	} elseif($_REQUEST['folder'] == "snap"){
		$antivirus->scan_files(ROOT_DIR."/backup", true);
		$antivirus->scan_files(ROOT_DIR."/system", true);
		$antivirus->scan_files(ROOT_DIR."/lang", true);
		$antivirus->scan_files(ROOT_DIR."/min", true);
		$antivirus->scan_files(ROOT_DIR."/templates", true);
		$antivirus->scan_files(ROOT_DIR."/uploads", true);
		$antivirus->scan_files(ROOT_DIR."/antibot", true);
		$antivirus->scan_files(ROOT_DIR, true );

		$filecontents = "";

		foreach($antivirus->snap_files as $idx => $data){
			$filecontents .= $data['file_path']."|".$data['file_crc']."\r\n";
		}

		$filehandle = fopen(ENGINE_DIR.'/data/snap.db', "w+");
		fwrite($filehandle, $filecontents);
		fclose($filehandle);
		@chmod(ENGINE_DIR.'/data/snap.db', 0666);
	} else {
		$antivirus->snap = false;
		$antivirus->scan_files(ROOT_DIR."/backup", false, true);
		$antivirus->scan_files(ROOT_DIR."/system", false, true);
		$antivirus->scan_files(ROOT_DIR."/lang", false, true);
		$antivirus->scan_files(ROOT_DIR."/templates", false, false);
		$antivirus->scan_files(ROOT_DIR."/uploads", false, true);
		$antivirus->scan_files(ROOT_DIR."/antibot", false, true);
		$antivirus->scan_files(ROOT_DIR, false, true);
	}
	
	@header("Content-type: text/html; charset=".$config['charset']);
	
	if(count($antivirus->bad_files)){
		echo <<<HTML
<table width="100%">
    <tr>
        <td colspan="2" style="padding:2px;"><b>Обнаружены следующие подозрительные файлы:</b></td>
    </tr>
    <tr>
        <td width="255" style="padding:2px;">Имя файла:</td>
        <td width="80">Размер:</td>
        <td width="140">Дата:</td>
        <td width="140">&nbsp;</td>
    </tr>
HTML;

		foreach($antivirus->bad_files as $idx => $data){
			if ($data['file_size'] < 50000) $color = "<font color=\"green\">";
			elseif ($data['file_size'] < 100000) $color = "<font color=\"blue\">";
			else $color = "<font color=\"red\">";

			$data['file_size'] = formatsize($data['file_size']);
			if ($data['type']) $type = 'не соответствует сделанному снимку'; else $type = 'неизвестен дистрибутиву';

			$data['file_path'] = preg_replace("/([0-9]){10}_/", "*****_", $data['file_path']);

			echo <<<HTML
<tr>
	<td style="padding:2px;">{$color}{$data['file_path']}</font></td>
	<td>{$color}{$data['file_size']}</font></td>
	<td>{$color}{$data['file_date']}</font></td>
	<td>{$color}{$type}</font></td>
</tr>
<td><div style="height:1px;border-bottom:1px dashed #ccc"></div></td>
<td><div style="height:1px;border-bottom:1px dashed #ccc"></div></td>
<td><div style="height:1px;border-bottom:1px dashed #ccc"></div></td>
<td><div style="height:1px;border-bottom:1px dashed #ccc"></div></td>
HTML;
		}
	} elseif ($_REQUEST['folder'] == "snap"){
		echo <<<HTML
<table width="100%">
	<tr>
		<td style="padding:2px;" colspan="3">Снимок системных файлов скрипта и шаблонов успешно сделан</td>
	</tr>
HTML;
	} else {
		echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;" colspan="3">При сканировании диска подозрительных файлов не обнаружено.</td>
    </tr>
HTML;
	}

	echo <<<HTML
<tr>
	<td style="padding:2px;" colspan=3><input onclick="StartSacan('global'); return false;" type="button" class="inp" style="width:250px" value="Провести тщательное сканирование"> <input onclick="StartSacan('snap'); return false;" type="button" class="inp" style="width:150px;" value="Сделать снимок"></td>
</tr>
</table>
HTML;

	die();
}

echoheader();

echohtmlstart('Антивирус');
echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
function StartSacan(folder){
	$('#scan_block').fadeIn();
	$('#loading').fadeIn('fast');
	if(folder == 'snap'){
		if(confirm("Вы уверены что список обнаруженных файлов безопасен, и вы хотите сделать новый снимок файлов ?")){
			$.post('/$admin_index?mod=antivirus&act=start', {folder: folder}, function(d){
				$('#result').html(d);
				$('#loading').hide();
			});
		}
	} else {
		$.post('/$admin_index?mod=antivirus&act=start', {folder: folder}, function(d){
			$('#result').html(d);
			$('#loading').hide();
		});
	}
}
</script>
<div id="loading" style="display:none"><div id="loading_text">Загрузка. Пожалуйста, подождите...</div></div>
Проверка папок и файлов скрипта на наличие подозрительных файлов, а также отслеживание несанкционированных изменений в файлах.

<div align="center"><input type="submit" class="inp" value="Запустить сканирование" onClick="StartSacan('lokal'); return false" /></div>
<div id="scan_block" style="display:none">
HTML;

echohtmlstart('Проверка файлов скрипта..');

echo <<<HTML
<div id="result">Подождите, идет проверка файлов скрипта..</div>
</div>
HTML;

echohtmlend();
?>