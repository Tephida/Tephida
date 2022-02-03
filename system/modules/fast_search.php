<?php
/* 
	Appointment: Быстрый поиск
	File: fast_search.php 
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

NoAjaxQuery();

if($logged){
	$user_id = $user_info['user_id'];
	
	$limit_sql = 7;
	
	$query = $db->safesql(ajax_utf8(strip_data($_POST['query'])));
	$query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
	$type = intval($_POST['se_type']);
	
	if(isset($query) AND !empty($query)){
	
		//Если критерий поиск "по людям"
		if($type == 1) 
			$sql_query = "SELECT user_id, user_search_pref, user_photo, user_birthday, user_country_city_name FROM `".PREFIX."_users` WHERE user_search_pref LIKE '%".$query."%' AND user_delet = '0' AND user_ban = '0' ORDER by `user_photo` DESC, `user_country_city_name` DESC LIMIT 0, ".$limit_sql;
			
		 //Если критерий поиск "по видеозаписям"
		else if($type == 2) 
			$sql_query = "SELECT id, photo, title, add_date, owner_user_id FROM `".PREFIX."_videos` WHERE title LIKE '%".$query."%' AND privacy = 1 ORDER by `views` DESC LIMIT 0, ".$limit_sql;
			
		 //Если критерий поиск "по сообщества"
		else if($type == 4) 
			$sql_query = "SELECT id, title, photo, traf, adres FROM `".PREFIX."_communities` WHERE title LIKE '%".$query."%' AND del = '0' AND ban = '0' ORDER by `traf` DESC, `photo` DESC LIMIT 0, ".$limit_sql;
		else
			$sql_query = false;
			
		if($sql_query){
			$sql_ = $db->super_query($sql_query, 1);
			$i = 1;
			if($sql_){
				foreach($sql_ as $row){
					$i++;
					
					 //Если критерий поиск "по видеозаписям"
					if($type == 2){
						$ava = $row['photo'];
						$img_width = 100;
						$row['user_search_pref'] = $row['title'];
						$countr = 'Добавлено '.megaDateNoTpl(strtotime($row['add_date']), 1, 1);
						$row['user_id'] = 'video'.$row['owner_user_id'].'_'.$row['id'].'" onClick="videos.show('.$row['id'].', this.href, location.href); return false';
		
					 //Если критерий поиск "по сообществам"
					} else if($type == 4){
						if($row['photo']) $ava = '/uploads/groups/'.$row['id'].'/50_'.$row['photo'];
						else $ava = '/templates/'.$config['temp'].'/images/no_ava_50.png';
						
						$img_width = 50;
						$row['user_search_pref'] = $row['title'];
						$countr = $row['traf'].' '.gram_record($row['traf'], 'groups_users');
						
						if($row['adres']) $row['user_id'] = $row['adres'];
						else $row['user_id'] = 'public'.$row['id'];
						
					//Если критерий поиск "по людям"
					} else {
						//АВА
						if($row['user_photo']) $ava = '/uploads/users/'.$row['user_id'].'/50_'.$row['user_photo'];
						else $ava = '/templates/'.$config['temp'].'/images/no_ava_50.png';
								
						//Страна город
						$expCountry = explode('|', $row['user_country_city_name']);
						if($expCountry[0]) $countr = $expCountry[0]; else $countr = '';
						if($expCountry[1]) $city = ', '.$expCountry[1]; else $city = '';
						
						//Возраст юзера
						$user_birthday = explode('-', $row['user_birthday']);
						$age = user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]);
						
						$img_width = '';
						
						$row['user_id'] = 'u'.$row['user_id'];
					}
						
					echo <<<HTML
<a href="/{$row['user_id']}" onClick="Page.Go(this.href); return false;" onMouseOver="FSE.ClrHovered(this.id)" id="all_fast_res_clr{$i}"><img src="{$ava}" width="{$img_width}" id="fast_img" /><div id="fast_name">{$row['user_search_pref']}</div><div><span>{$countr}{$city}</span></div><span>{$age}</span><div class="clear"></div></a> 
HTML;
				}
			}
		}
	}
} else
	echo 'no_log';
	
die();
?>