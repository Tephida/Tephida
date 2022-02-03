<?php
/* 
	Appointment: Блог сайта
	File: blog.php 
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

if($logged){
	$act = $_GET['act'];
	$user_id = $user_info['user_id'];

$user_speedbar = $lang['blog_descr'];	

	switch($act){
		
		//################### Страница добавления ###################//
		case "add":
			if($user_group[$user_info['user_group']]['addnews']){
				$tpl->load_template('blog/add.tpl');
				$tpl->compile('content');
			} else
				Hacking();
		break;
		
		//################### Добавление новости в БД ###################//
		case "send":
			NoAjaxQuery();
			if($user_group[$user_info['user_group']]['addnews']){
				//Подключаем парсер
				include ENGINE_DIR.'/classes/parse.php';
				$parse = new parse();

				$title = ajax_utf8(textFilter($_POST['title'], false, true));
				$text = $parse->BBparse(ajax_utf8(textFilter($_POST['text'])));

				function BBimg($source){ 
					return "<img src=\"{$source}\" alt=\"\" />";
				}
				$text = preg_replace("#\\[img\\](.*?)\\[/img\\]#ies", "\BBimg('\\1')", $text);

				if(isset($title) AND !empty($title) AND isset($text) AND !empty($text))
					$db->query("INSERT INTO `".PREFIX."_blog` SET title = '{$title}', story = '{$text}', date = '{$server_time}'");
			}
			die();
		break;
		
		//################### Удаление новости в БД ###################//
		case "del":
			NoAjaxQuery();
			if($user_group[$user_info['user_group']]['addnews']){
				$id = intval($_POST['id']);
				$db->query("DELETE FROM `".PREFIX."_blog` WHERE id = '{$id}'");
			}
			die();
		break;
		
		//################### Страница редактирования ###################//
		case "edit":
			if($user_group[$user_info['user_group']]['addnews']){
				$id = intval($_GET['id']);
				$row = $db->super_query("SELECT title, story FROM `".PREFIX."_blog` WHERE id = '{$id}'");
				if($row){
					//Подключаем парсер
					include ENGINE_DIR.'/classes/parse.php';
					$parse = new parse();
					
					function BBdecodeImg($source){
						return '[img]'.$source.'[/img]';
					}
					$row['story'] = preg_replace("#\\<img src=\"(.*?)\\\" alt=\"\" />#ies", "\BBdecodeImg('\\1')", $row['story']);
					
					$tpl->load_template('blog/edit.tpl');
					$tpl->set('{story}', $parse->BBdecode(stripslashes(myBrRn($row['story']))));
					$tpl->set('{title}', stripslashes($row['title']));
					$tpl->set('{id}', $id);
					$tpl->compile('content');
				} else
					Hacking();
			} else
				Hacking();
		break;
		
		//################### Сохранение отредактированых ###################//
		case "save":
			NoAjaxQuery();
			if($user_group[$user_info['user_group']]['addnews']){
				//Подключаем парсер
				include ENGINE_DIR.'/classes/parse.php';
				$parse = new parse();

				$title = ajax_utf8(textFilter($_POST['title'], false, true));
				$text = $parse->BBparse(ajax_utf8(textFilter($_POST['text'])));
				$id = intval($_POST['id']);

				function BBimg($source){ 
					return "<img src=\"{$source}\" alt=\"\" />";
				}
				$text = preg_replace("#\\[img\\](.*?)\\[/img\\]#ies", "\BBimg('\\1')", $text);

				if(isset($title) AND !empty($title) AND isset($text) AND !empty($text))
					$db->query("UPDATE `".PREFIX."_blog` SET title = '{$title}', story = '{$text}' WHERE id = '{$id}'");
			}
			die();
		break;
		
		//################### Загрузка фотографии ###################//
		case "upload":
			NoAjaxQuery();
			if($user_group[$user_info['user_group']]['addnews']){
				//Если нет папки альбома, то создаём её
				$album_dir = ROOT_DIR."/uploads/blog/";

				//Разришенные форматы
				$allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

				//Получаем данные о фотографии
				$image_tmp = $_FILES['uploadfile']['tmp_name'];
				$image_name = totranslit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
				$image_rename = substr(md5($server_time+rand(1,100000)), 0, 20); // имя фотографии
				$image_size = $_FILES['uploadfile']['size']; // размер файла
				$type = end(explode(".", $image_name)); // формат файла
								
				//Проверям если, формат верный то пропускаем
				if(in_array(strtolower($type), $allowed_files)){
					if($image_size < 5000000){
						$res_type = strtolower('.'.$type);

						if(move_uploaded_file($image_tmp, $album_dir.$image_rename.$res_type)){
							//Подключаем класс для фотографий
							include ENGINE_DIR.'/classes/images.php';
							
							//Создание оригинала
							$tmb = new thumbnail($album_dir.$image_rename.$res_type);
							$tmb->size_auto('570', 1);
							$tmb->jpeg_quality('100');
							$tmb->save($album_dir.$image_rename.$res_type);

							$img_url = $config['home_url'].'uploads/blog/'.$image_rename.$res_type;

							//Результат для ответа
							echo $img_url;
						} else
							echo 'big_size';
					} else
						echo 'big_size';
				} else
					echo 'bad_format';
			}
			die();
		break;

		default:
			$id = intval($_GET['id']);
			if($id){
				$sqlWhere = "WHERE id = '{$id}'";
			}
			
			//Вывод последней новости
			$row = $db->super_query("SELECT id, title, story, date FROM `".PREFIX."_blog` {$sqlWhere} ORDER by `date` DESC");
			if(!$row)
				$row = $db->super_query("SELECT id, title, story, date FROM `".PREFIX."_blog` ORDER by `date` DESC");
				
			$tpl->load_template('blog/story.tpl');
			megaDate($row['date'], 1, 1);
			$tpl->set('{story}', stripslashes($row['story']));
			$tpl->set('{title}', stripslashes($row['title']));
			$tpl->set('{id}', $row['id']);
			
			//Вывод последних 20 новостей
			$sql_ = $db->super_query("SELECT id, title FROM `".PREFIX."_blog` ORDER by `date` DESC LIMIT 0, 20", 1);
			$cnt = 0;
			foreach($sql_ as $rowLast){
				$cnt++;
				$rowLast['title'] = stripslashes($rowLast['title']);
				if($_GET['id'] == $rowLast['id'] OR $cnt == 1 AND !$_GET['id'])
					$lastNews .= "<div><a href=\"/blog?id={$rowLast['id']}\" class=\"bloglnkactive\" onClick=\"Page.Go(this.href); return false\">{$rowLast['title']}</a></div>";
				else
					$lastNews .= "<a href=\"/blog?id={$rowLast['id']}\" onClick=\"Page.Go(this.href); return false\">{$rowLast['title']}</a>";
			}
			
			$tpl->set('{last-news}', $lastNews);

			$tpl->compile('content');
	}
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>