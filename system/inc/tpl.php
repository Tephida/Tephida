<?php
/* 
	Appointment: Шаблоны
	File: tpl.php
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
	
header('Content-type: text/html; charset=utf-8');

$act = $_GET['act'];
$allowed_extensions = array("tpl", "css", "js");

switch($act){
	
	//################### Загрузка TPL файла ###################//
	case "loadTpl":
		$temp = strip_data($_POST['temp']);
		$file_include = $_POST['tpl'];
		$file_include = str_replace(array('..', '...', '/../', '//', './', '\..', '\.'), '', $file_include);		
		$temp_dir = ROOT_DIR.'/templates/'.$temp;
		$content = @file_get_contents($temp_dir.'/'.$file_include);
		$format_file = strtolower(end(explode('.', $file_include)));

		if(is_writable($temp_dir.'/'.$file_include) && in_array($format_file, $allowed_extensions) && file_exists($temp_dir.'/'.$file_include))
			echo $content;
		else
			echo 'Файл шаблона не найден';
			
		die();
	break;

	//################### Открытие папки ###################//
	case "opneFolder":
		$template = strip_data($_POST['template']);
		$folder = strip_data($_POST['folder']);
		
		$files = scandir(ROOT_DIR.'/templates/'.$template.'/'.$folder);
		
		if(is_dir(ROOT_DIR.'/templates/'.$template.'/'.$folder)){
			foreach($files as $file){
				$format_file = strtolower(end(explode('.', $file)));

				if($file != '...' && $file != '..' && $file != '.' && $file != '.htaccess' && in_array($format_file, $allowed_extensions)){
					if($format_file == 'tpl')
						$class = 'tetpl';
					elseif($format_file == 'css')
						$class = 'tecss';
					else
						$class = 'tejs';

					$check_file = count(explode('.', $file))-1;
					
					if($check_file)
						$tpls .= '<div class="'.$class.'" onClick="temp.loadTpl(\''.$template.'\', \''.$folder.'/'.$file.'\'); return false">'.$file.'</div>';
				}
			}
		}

		echo $tpls;
		
		die();
	break;
	
	//################### Сохранение файла ###################//
	case "save":
		$content = convert_unicode($_POST['content'], 'utf-8');
		if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) $content = stripslashes($content);
		$folder = strip_data($_POST['folder']);
		$file_include = $_POST['tpl'];
		$file_include = str_replace(array('..', '...', '/../', '//', './', '\..', '\.'), '', $file_include);
		$temp_dir = ROOT_DIR.'/templates/'.$folder;
		$file_open = $temp_dir.'/'.$file_include;
		$format_file = strtolower(end(explode('.', $file_open)));

		if(is_writable($file_open) && in_array($format_file, $allowed_extensions) && file_exists($file_open)){
			$file = fopen($file_open, "r+");
			file_put_contents($file_open, '');
			fputs($file, $content);
			fclose($file);
			echo 'Файл шаблона был успешно сохранён!';
		} else
			echo 'Файл шаблона не найден';
		
		die();
	break;
	
	//################### Главная ###################//
	default:
		echoheader(900);
		
		//Если загружаем другой шаблон
		if(isset($_POST['chahe_skin']))
			$config['temp'] = strip_data($_POST['newtemp']);
		
		echohtmlstart("Управление шаблонами");
		//Чтение всех шаблон в папке "templates"
		$root = ROOT_DIR.'/templates/';
		$root_dir = scandir($root);
		foreach($root_dir as $templates)
			if($templates != '.' && $templates != '..' && $templates != '.htaccess')
				$for_select .= str_replace('value="'.$config['temp'].'"', 'value="'.$config['temp'].'" selected', '<option value="'.$templates.'">'.$templates.'</option>');
	
		echo "<form method=\"POST\" action=\"\"><div class=\"fllogall\" style=\"width:240px\">Выбранный шаблон для редактирования:</div>
		<select name=\"newtemp\" class=\"inpu fl_l\">{$for_select}</select>
		<div class=\"button_div fl_l\" style=\"margin-left:10px;margin-top:-10px;margin-bottom:5px\"><button name=\"chahe_skin\" class=\"inp\" >Выполнить</button></div>
		<div class=\"mgcler\"></div></form>";
		htmlclear();
		
		echohtmlstart("Редактирование разделов шаблона: <u>{$config['temp']}</u>");
		$temp_dir = ROOT_DIR.'/templates/'.$config['temp'];
		
		if(is_dir($temp_dir)){

			$files = scandir(ROOT_DIR.'/templates/'.$config['temp']);

			foreach($files as $file){
				$format_file = strtolower(end(explode('.', $file)));

				if($file != '...' && $file != '..' && $file != '.' && $file != '.htaccess' && !str_replace($format_file, '', $file))
					$folders .= '<div class="tefolfer" onClick="temp.openFolder(\''.$config['temp'].'\', \''.$file.'\'); return false">'.$file.'</div><div id="folder_'.$file.'" style="margin-left:15px;display:none"></div>';
					
				if($file != '...' && $file != '..' && $file != '.' && $file != '.htaccess' && in_array($format_file, $allowed_extensions)){
					if($format_file == 'tpl')
						$class = 'tetpl';
					elseif($format_file == 'css')
						$class = 'tecss';
					else
						$class = 'tejs';

					$check_file = count(explode('.', $file))-1;
					
					if($check_file)
						$tpls .= '<div class="'.$class.'" onClick="temp.loadTpl(\''.$config['temp'].'\', \''.$file.'\'); return false">'.$file.'</div>
						';
				}
			}
			
			echo "<div class=\"tempdata fl_l\">{$folders}{$tpls}</div>";
			
			echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
var temp = {
	loadTpl: function(folder, tpl){
		$('#loading').fadeIn('fast');
		$.post('/{$admin_index}?mod=tpl&act=loadTpl', {temp: folder, tpl: tpl}, function(data){
			$('#editable').show();
			$('#loadedtpl').text(folder+'/'+tpl);
			$('#result').val(data).scrollTop(0);
			$('#loading').fadeOut('fast');
			
			$('#folder').val(folder);
			$('#tplname').val(tpl);
		});
	},
	save: function(){
		content = $('#result').val();
		folder = $('#folder').val();
		tplname = $('#tplname').val();
		$('#loading').fadeIn('fast');
		$.post('/{$admin_index}?mod=tpl&act=save', {folder: folder, tpl: tplname, content: content}, function(data){
			$('#loading_text').text(data);
			setTimeout('$("#loading").hide();$("#loading_text").text("Загрузка. Пожалуйста, подождите...")', 2000);
		});
	},
	openFolder: function(template, folder){
		$('#loading').fadeIn('fast');
		$.post('/{$admin_index}?mod=tpl&act=opneFolder', {template: template, folder: folder}, function(data){
			$('#loading').fadeOut('fast');
			$('#folder_'+folder).html(data).slideToggle('fast');
		});
	}
}
</script>
HTML;
			
			echo "<div id=\"loading\" style=\"display:none\"><div id=\"loading_text\">Загрузка. Пожалуйста, подождите...</div></div>
			<div class=\"edittable fl_l\">
			<div id=\"editable\"  style=\"display:none\">
			Редактирование файла: <b><span id=\"loadedtpl\"></span></b>
			<textarea class=\"ftext\" id=\"result\"></textarea>
			<div class=\"button_div fl_l\" style=\"margin-top:-2px\" onClick=\"temp.save(); return false\"><button class=\"inp\">Сохранить</button></div>
			<input type=\"hidden\" id=\"folder\" />
			<input type=\"hidden\" id=\"tplname\" />
			</div>
			</div>";
		
		} else
			echo 'Шаблон не найден';

		echohtmlend();
}
?>