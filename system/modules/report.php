<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die('Hacking attempt!');

NoAjaxQuery();

if($logged){
	$act = textFilter($_POST['act']);
	$mid = intval($_POST['id']);
	$type_report = intval($_POST['type_report']);
	$text_report = textFilter($_POST['text_report']);
	$arr_act = array('photo', 'video', 'note', 'wall');
	if($act == 'wall') $type_report = 6;
	if(in_array($act, $arr_act) AND $mid AND $type_report <= 6 AND $type_report > 0){
		$check = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_report` WHERE ruser_id = '".$user_info['user_id']."' AND mid = '".$mid."' AND act = '".$act."'");
		if(!$check['cnt'])
			$db->query("INSERT INTO `".PREFIX."_report` SET act = '".$act."', type = '".$type_report."', text = '".$text_report."', mid = '".$mid."', date = '".$server_time."', ruser_id = '".$user_info['user_id']."'");
	}
}

die();
?>