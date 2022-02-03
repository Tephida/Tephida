<?php
/* 
	Appointment: Статистика моей страницы
	File: my_stats.php 
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

	$month = intval($_GET['m']);
	if($month AND $month <= 0 OR $month > 12) $month = 1;
	
	$year = intval($_GET['y']);
	if($year AND $year < 2013 OR $year > 2020) $year = 2013;
			
	if($month AND $year){
		
		if($month > 1 AND $month < 10){
		
			$t_date = langdate('F', strtotime($year.'-'.$month));
			
			$stat_date = $year.'0'.$month;
			$r_month = '0'.$month;
		
		} else {
			
			$stat_date = $year.$month;
			$r_month = $month;
			
			$t_date = langdate('F', strtotime($year.'-'.$month));
			
		}
				
	} else {
				
		$stat_date = date('Ym', $server_time);
		$r_month = date('m', $server_time);
					
		$month = date('n', $server_time);
		
		$t_date = langdate('F', strtotime($stat_date));

	}
		
	//Составляем массив для вывода за этот месяц
	$sql_ = $db->super_query("SELECT users, views, date FROM `".PREFIX."_users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `date` ASC", 1);

	if($sql_){
				
		foreach($sql_ as $row){
						
			$dat_exp = date('j', strtotime($row['date']));

			$arr_r_unik[$dat_exp] = $row['users'];

			$arr_r_money[$dat_exp] = $row['views'];
					
		}
				
	}
				
	if($r_month == '01' OR $r_month == '03' OR $r_month == '05' OR $r_month == '07' OR $r_month == '08' OR $r_month == '10' OR $r_month == '12' OR $r_month == '1' OR $r_month == '3' OR $r_month == '5' OR $r_month == '7' OR $r_month == '8') $limit_day = 31;
	elseif($r_month == '02') $limit_day = 28;
	else $limit_day = 30;
				
	for($i = 1; $i <= $limit_day; $i++){
					
		if(!$arr_r_unik[$i]) $arr_r_unik[$i] = 0;
		$r_unik .= '['.$i.', '.$arr_r_unik[$i].'],';
					
		if(!$arr_r_money[$i]) $arr_r_money[$i] = 0;
		$r_moneys .= '['.$i.', '.$arr_r_money[$i].'],';
					
	}

	//Выводим максимальное кол-во юзеров за этот месяц
	$row_max = $db->super_query("SELECT users FROM `".PREFIX."_users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `users` DESC");
				
	$rNum = round($row_max['users'] / 15);
	if($rNum < 1) $rNum = 1;

	$tickSize = $rNum;

	//Выводим максимальное кол-во просмотров за этот месяц
	$row_max_hits = $db->super_query("SELECT views FROM `".PREFIX."_users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `views` DESC");
				
	$rNum_moenys = round($row_max_hits['views'] / 15);
	if($rNum_moenys < 1) $rNum_moenys = 1;

	$tickSize_moneys = $rNum_moenys;

	//Загружаем шаблон
	$tpl->load_template('profile_stats.tpl');
				
	$tpl->set('{r_unik}', $r_unik);
	$tpl->set('{r_moneys}', $r_moneys);
	$tpl->set('{t-date}', $t_date);
	$tpl->set('{tickSize}', $tickSize);
	$tpl->set('{tickSize_moneys}', $tickSize_moneys);
	$tpl->set('{uid}', $user_info['user_id']);

	$tpl->set('{months}', installationSelected($month, '<option value="1">Январь</option><option value="2">Февраль</option><option value="3">Март</option><option value="4">Апрель</option><option value="5">Май</option><option value="6">Июнь</option><option value="7">Июль</option><option value="8">Август</option><option value="9">Сентябрь</option><option value="10">Октябрь</option><option value="11">Ноябрь</option><option value="12">Декабрь</option>'));
	$tpl->set('{year}', installationSelected($year, '<option value="2013">2013</option><option value="2014">2014</option><option value="2015">2015</option><option value="2016">2016</option><option value="2017">2017</option><option value="2018">2018</option><option value="2019">2019</option><option value="2020">2020</option>'));
				
	$tpl->compile('content');

	$tpl->clear();
	$db->free();
	
} else {

	$user_speedbar = $lang['no_infooo'];
	msgbox('', $lang['not_logged'], 'info');
	
}
?>