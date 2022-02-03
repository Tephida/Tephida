<?php
/* 
	Appointment: Помощь
	File: support.php 
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
	$metatags['title'] = $lang['support_title'];

	if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
	$gcount = 20;
	$limit_page = ($page-1)*$gcount;

	switch($act){
		
		//################### Страница создание нового вопроса  ###################//
		case "new":
			$mobile_speedbar = 'Новый вопрос';

			$tpl->load_template('support/new.tpl');
			$tpl->set('{uid}', $user_id);
			$tpl->compile('content');
		break;
		
		//################### Отправка нового вопроса  ###################//
		case "send":
			NoAjaxQuery();
			$title = ajax_utf8(textFilter($_POST['title']), false, true);
			$question = ajax_utf8(textFilter($_POST['question']));
			$limitTime = $server_time-3600;
			$rowLast = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_support` WHERE сdate > '{$limitTime}'");
			if(!$rowLast['cnt'] AND isset($title) AND !empty($title) AND isset($question) AND !empty($question) AND $user_info['user_group'] != 4){
				$question = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $question);
				$db->query("INSERT INTO `".PREFIX."_support` SET title = '{$title}', question = '{$question}', suser_id = '{$user_id}', sfor_user_id = '{$user_id}', sdate = '{$server_time}', сdate = '{$server_time}'");
				$dbid = $db->insert_id();
				$row = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$user_id}'");
				$tpl->load_template('support/show.tpl');
				$tpl->set('{title}', stripslashes($title));
				$tpl->set('{question}', stripslashes($question));
				$tpl->set('{qid}', $dbid);
				megaDate($server_time);
				$tpl->set('{status}', 'Вопрос ожидает обработки.');
				$tpl->set('{name}', $row['user_search_pref']);
				$tpl->set('{uid}', $user_id);
				if($row['user_photo'])
					$tpl->set('{ava}', '/uploads/users/'.$user_id.'/50_'.$row['user_photo']);
				else
					$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
				$tpl->set('{answers}', '');
				$tpl->compile('content');
				AjaxTpl();
				echo 'r|x'.$dbid;
			} else
				echo 'limit';
			
			die();
		break;
		
		//################### Удаление вопроса  ###################//
		case "delet":
			NoAjaxQuery();
			$qid = intval($_POST['qid']);
			$row = $db->super_query("SELECT suser_id FROM `".PREFIX."_support` WHERE id = '{$qid}'");
			if($row['suser_id'] == $user_id OR $user_info['user_group'] == 4){
				$db->query("DELETE FROM `".PREFIX."_support` WHERE id = '{$qid}'");
				$db->query("DELETE FROM `".PREFIX."_support_answers` WHERE qid = '{$qid}'");
			}
			die();
		break;
		
		//################### Удаление Ответа  ###################//
		case "delet_answer":
			NoAjaxQuery();
			$id = intval($_POST['id']);
			$row = $db->super_query("SELECT auser_id FROM `".PREFIX."_support_answers` WHERE id = '{$id}'");
			if($row['auser_id'] == $user_id OR $user_info['user_group'] == 4)
				$db->query("DELETE FROM `".PREFIX."_support_answers` WHERE id = '{$id}'");

			die();
		break;
		
		//################### Закрытие вопроса  ###################//
		case "close":
			NoAjaxQuery();
			$qid = intval($_POST['qid']);
			if($user_info['user_group'] == 4){
				$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_support` WHERE id = '{$qid}'");
				if($row['cnt'])
					$db->query("UPDATE `".PREFIX."_support` SET sfor_user_id = 0 WHERE id = '{$qid}'");
			}
			die();
		break;
		
		//################### Отправка ответа ###################//
		case "answer":
			NoAjaxQuery();
			$qid = intval($_POST['qid']);
			$answer = ajax_utf8(textFilter($_POST['answer']));
			$check = $db->super_query("SELECT suser_id FROM `".PREFIX."_support` WHERE id = '{$qid}'");
			if($check['suser_id'] == $user_id OR $user_info['user_group'] == 4 AND isset($answer) AND !empty($answer)){
				if($user_info['user_group'] == 4){
					$auser_id = 0;
					$db->query("UPDATE `".PREFIX."_users` SET user_support = user_support+1 WHERE user_id = '{$check['suser_id']}'");
				} else
					$auser_id = $user_id;
					
				$answer = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<!--link:$1--><a href="$1" target="_blank">$1</a><!--/link-->', $answer);
				
				$db->query("INSERT INTO `".PREFIX."_support_answers` SET qid = '{$qid}', auser_id = '{$auser_id}', adate = '{$server_time}', answer = '{$answer}'");
				$db->query("UPDATE `".PREFIX."_support` SET sfor_user_id = '{$auser_id}', sdate = '{$server_time}' WHERE id = '{$qid}'");
				
				$row = $db->super_query("SELECT user_search_pref, user_photo FROM `".PREFIX."_users` WHERE user_id = '{$user_id}'");
				
				$tpl->load_template('support/answer.tpl');
				if(!$auser_id){
					$tpl->set('{name}', 'Агент поддержки');
					$tpl->set('{ava}', '{theme}/images/support.png');
					$tpl->set_block("'\\[no-agent\\](.*?)\\[/no-agent\\]'si","");
				} else {
					$tpl->set('{name}', $row['user_search_pref']);
					if($row['user_photo'])
						$tpl->set('{ava}', '/uploads/users/'.$user_id.'/50_'.$row['user_photo']);
					else
						$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
							
					$tpl->set('[no-agent]', '');
					$tpl->set('[/no-agent]', '');
				}
					
				if($auser_id == $user_id OR $user_info['user_group'] == 4){
					$tpl->set('[owner]', '');
					$tpl->set('[/owner]', '');
				} else
					$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					
				$tpl->set('{uid}', $user_id);
				$tpl->set('{answer}', stripslashes($answer));
				megaDate($server_time);
				$tpl->compile('content');
				AjaxTpl();
			}
			die();
		break;
		
		//################### Просмотр вопроса ###################//
		case "show":
			$qid = intval($_GET['qid']);

			$mobile_speedbar = 'Просмотр вопроса';

			if($user_info['user_group'] == 4)
				$sql_where = "";
			else
				$sql_where = "AND tb1.suser_id = '{$user_id}'";

			$row = $db->super_query("SELECT tb1.id, title, question, sdate, sfor_user_id, suser_id, tb2.user_search_pref, user_photo FROM `".PREFIX."_support` tb1, `".PREFIX."_users` tb2 WHERE tb1.id = '{$qid}' AND tb1.suser_id = tb2.user_id {$sql_where}");
			if($row){
				//Выводим ответы
				$sql_answer = $db->super_query("SELECT id, adate, answer, auser_id FROM `".PREFIX."_support_answers` WHERE qid = '{$qid}' ORDER by `adate` ASC LIMIT 0, 100", 1);
				
				$tpl->load_template('support/answer.tpl');
				foreach($sql_answer as $row_answer){
					if(!$row_answer['auser_id']){
						$tpl->set('{name}', 'Агент поддержки');
						$tpl->set('{ava}', '{theme}/images/support.png');
						$tpl->set_block("'\\[no-agent\\](.*?)\\[/no-agent\\]'si","");
					} else {
						$tpl->set('{name}', $row['user_search_pref']);
						if($row['user_photo'])
							$tpl->set('{ava}', '/uploads/users/'.$row['suser_id'].'/50_'.$row['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
							
						$tpl->set('[no-agent]', '');
						$tpl->set('[/no-agent]', '');
					}
					
					if($row_answer['auser_id'] == $user_id OR $user_info['user_group'] == 4){
						$tpl->set('[owner]', '');
						$tpl->set('[/owner]', '');
					} else
						$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
					
					$tpl->set('{id}', $row_answer['id']);
					$tpl->set('{uid}', $user_id);
					$tpl->set('{answer}', stripslashes($row_answer['answer']));
					megaDate($row_answer['adate']);
					$tpl->compile('answers');
				}
				
				$tpl->load_template('support/show.tpl');
				$tpl->set('{title}', stripslashes($row['title']));
				$tpl->set('{question}', stripslashes($row['question']));
				$tpl->set('{qid}', $qid);
				megaDate($row['sdate']);
				
				if($row['sfor_user_id'] == $row['suser_id'])
					$tpl->set('{status}', 'Вопрос ожидает обработки.');
				else
					$tpl->set('{status}', 'Есть ответ.');
				
				$tpl->set('{name}', $row['user_search_pref']);
				
				if($user_info['user_group'] == 4)
					$tpl->set('{uid}', $row['suser_id']);
				else
					$tpl->set('{uid}', $user_id);
				
				if($row['user_photo'])
					$tpl->set('{ava}', '/uploads/users/'.$row['suser_id'].'/50_'.$row['user_photo']);
				else
					$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
				
				$tpl->set('{answers}', $tpl->result['answers']);
				$tpl->compile('content');
			} else {
				$speedbar = $lang['error'];
				msgbox('', $lang['support_no_quest'], 'info');
			}
		break;
			
			//################### Просмотр всех вопросов ###################//
			default:
				$mobile_speedbar = 'Помощь';

				if($user_info['user_support'] AND $user_info['user_group'] != 4)
					$db->query("UPDATE `".PREFIX."_users` SET user_support = 0 WHERE user_id = '{$user_id}'");
				
				if($user_info['user_group'] == 4){
					$sql_where = "ORDER by `sdate` DESC";
					$sql_where_cnt = "";
				} else {
					$sql_where = "AND tb1.suser_id = '{$user_id}' ORDER by `sdate` DESC";
					$sql_where_cnt = "WHERE suser_id = '{$user_id}'";
				}
				
				$sql_ = $db->super_query("SELECT tb1.id, title, suser_id, sfor_user_id, sdate, tb2.user_photo, user_search_pref FROM `".PREFIX."_support` tb1, `".PREFIX."_users` tb2 WHERE tb1.suser_id = tb2.user_id {$sql_where} LIMIT {$limit_page}, {$gcount}", 1);
				
				if($sql_)
					$count = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_support` {$sql_where_cnt}");
					
				$tpl->load_template('support/head.tpl');
				if($sql_)
					if($user_info['user_group'] == 4)
						$tpl->set('{cnt}', $count['cnt'].' '.gram_record($count['cnt'], 'questions'));
					else
						$tpl->set('{cnt}', 'Вы задали '.$count['cnt'].' '.gram_record($count['cnt'], 'questions'));
				else
					$tpl->set('{cnt}', '');

				$tpl->compile('info');
				
				if($sql_){
					$tpl->load_template('support/question.tpl');
					foreach($sql_ as $row){
						$tpl->set('{title}', stripslashes($row['title']));
						megaDate($row['sdate']);
						if($row['sfor_user_id'] == $row['suser_id'] OR $user_info['user_group'] == 4){
							if($row['sfor_user_id'] == $row['suser_id'])
								$tpl->set('{status}', 'Вопрос ожидает обработки.');
							else
								$tpl->set('{status}', 'Есть ответ.');
							$tpl->set('{name}', $row['user_search_pref']);
							$tpl->set('{answer}', '');
							if($row['user_photo'])
								$tpl->set('{ava}', '/uploads/users/'.$row['suser_id'].'/50_'.$row['user_photo']);
							else
								$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						} else {
							$tpl->set('{name}', 'Агент поддержки');
							$tpl->set('{status}', 'Есть ответ.');
							$tpl->set('{ava}', '{theme}/images/support.png');
							$tpl->set('{answer}', 'ответил');
						}
						$tpl->set('{qid}', $row['id']);
						$tpl->compile('content');
					}
					navigation($gcount, $count['cnt'], '/support?page=');
				} else
					if($user_info['user_group'] == 4)
						msgbox('', $lang['support_no_quest3'], 'info_2');
					else
						msgbox('', $lang['support_no_quest2'], 'info_2');
	}
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
}
?>