<?php
/* 
	Appointment: Просмотр страницы пользователей
	File: profile.php 
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

$user_id = $user_info['user_id'];

if($logged){
	$id = intval($_GET['id']);
	$cache_folder = 'user_'.$id;

	//Читаем кеш
	$row = unserialize(mozg_cache($cache_folder.'/profile_'.$id));

	//Проверяем на наличие кеша, если нету то выводи из БД и создаём его 
	if(!$row){
		$row = $db->super_query("SELECT user_id, user_real, user_search_pref, user_country_city_name, user_birthday, user_xfields, user_xfields_all, user_city, user_country, user_photo, user_friends_num, user_notes_num, user_subscriptions_num, user_wall_num, user_albums_num, user_last_visit, user_videos_num, user_status, user_privacy, user_sp, user_sex, user_gifts, user_public_num, user_audio, user_delet, user_ban_date, xfields, user_logged_mobile , user_cover, user_cover_pos, user_rating FROM `".PREFIX."_users` WHERE user_id = '{$id}'");
		if($row){
			mozg_create_folder_cache($cache_folder);
			mozg_create_cache($cache_folder.'/profile_'.$id, serialize($row));
		}
		$row_online['user_last_visit'] = $row['user_last_visit'];
		$row_online['user_logged_mobile'] = $row['user_logged_mobile'];
	} else 
		$row_online = $db->super_query("SELECT user_last_visit, user_logged_mobile FROM `".PREFIX."_users` WHERE user_id = '{$id}'");

	
	//Если есть такой, юзер то продолжаем выполнение скрипта
	if($row){
		$mobile_speedbar = $row['user_search_pref'];
		$user_speedbar = $row['user_search_pref'];
		$metatags['title'] = $row['user_search_pref'];
		
		//Если удалена
		if($row['user_delet']){
			$tpl->load_template("profile_delete_all.tpl");
			$user_name_lastname_exp = explode(' ', $row['user_search_pref']);
			$tpl->set('{name}', $user_name_lastname_exp[0]);
			$tpl->set('{lastname}', $user_name_lastname_exp[1]);
			$tpl->compile('content');
		//Если заблокирована
		} elseif($row['user_ban_date'] >= $server_time OR $row['user_ban_date'] == '0'){
			$tpl->load_template("profile_baned_all.tpl");
			$user_name_lastname_exp = explode(' ', $row['user_search_pref']);
			$tpl->set('{name}', $user_name_lastname_exp[0]);
			$tpl->set('{lastname}', $user_name_lastname_exp[1]);
			$tpl->compile('content');
		//Если все хорошо, то выводим дальше
		} else {
			$CheckBlackList = CheckBlackList($id);
			
			$user_privacy = xfieldsdataload($row['user_privacy']);

			$user_name_lastname_exp = explode(' ', $row['user_search_pref']);
			$user_country_city_name_exp = explode('|', $row['user_country_city_name']);

			//################### Друзья ###################//
			if($row['user_friends_num']){
				$sql_friends = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.user_id  AND subscriptions = 0 ORDER by rand() DESC LIMIT 0, 6", 1);
				$tpl->load_template('profile_friends.tpl');
				foreach($sql_friends as $row_friends){
					$friend_info = explode(' ', $row_friends['user_search_pref']);
					$tpl->set('{user-id}', $row_friends['friend_id']);
					$tpl->set('{name}', $friend_info[0]);
					$tpl->set('{last-name}', $friend_info[1]);
					if($row_friends['user_photo'])
						$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_friends['friend_id'].'/50_'.$row_friends['user_photo']);
					else
						$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
					$tpl->compile('all_friends');
				}
			}
			
			//################### Друзья на сайте ###################//
			if($user_id != $id)
				//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
				$check_friend = CheckFriends($row['user_id']);
			
			//Кол-во друзей в онлайне
			if($row['user_friends_num']){
				$online_friends = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_users` tb1, `".PREFIX."_friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$id}' AND tb1.user_last_visit >= '{$online_time}' AND subscriptions = 0");
				
				//Если друзья на сайте есть то идем дальше
				if($online_friends['cnt']){
					$sql_friends_online = $db->super_query("SELECT tb1.user_id, user_country_city_name, user_search_pref, user_birthday, user_photo FROM `".PREFIX."_users` tb1, `".PREFIX."_friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$id}' AND tb1.user_last_visit >= '{$online_time}'  AND subscriptions = 0 ORDER by rand() DESC LIMIT 0, 6", 1);
					$tpl->load_template('profile_friends.tpl');
					foreach($sql_friends_online as $row_friends_online){
						$friend_info_online = explode(' ', $row_friends_online['user_search_pref']);
						$tpl->set('{user-id}', $row_friends_online['user_id']);
						$tpl->set('{name}', $friend_info_online[0]);
						$tpl->set('{last-name}', $friend_info_online[1]);
						if($row_friends_online['user_photo'])
							$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_friends_online['user_id'].'/50_'.$row_friends_online['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						$tpl->compile('all_online_friends');
					}
				}
			}
			
			//################### Заметки ###################//
			if($row['user_notes_num']){
				$tpl->result['notes'] = mozg_cache($cache_folder.'/notes_user_'.$id);
				if(!$tpl->result['notes']){
					$sql_notes = $db->super_query("SELECT id, title, date, comm_num FROM `".PREFIX."_notes` WHERE owner_user_id = '{$id}' ORDER by `date` DESC LIMIT 0,5", 1);
					$tpl->load_template('profile_note.tpl');
					foreach($sql_notes as $row_notes){
						$tpl->set('{id}', $row_notes['id']);
						$tpl->set('{title}', stripslashes($row_notes['title']));
						$tpl->set('{comm-num}', $row_notes['comm_num'].' '.gram_record($row_notes['comm_num'], 'comments'));
						megaDate(strtotime($row_notes['date']), 'no_year');
						$tpl->compile('notes');
					}
					mozg_create_cache($cache_folder.'/notes_user_'.$id, $tpl->result['notes']);
				}
			}
			
			//################### Видеозаписи ###################//
			if($row['user_videos_num']){	
				//Настройки приватности
				if($user_id == $id)
					$sql_privacy = "";
				elseif($check_friend){
					$sql_privacy = "AND privacy regexp '[[:<:]](1|2)[[:>:]]'";
					$cache_pref_videos = "_friends";
				} else {
					$sql_privacy = "AND privacy = 1";
					$cache_pref_videos = "_all";
				}
				
				//Если страницу смотрит другой юзер, то считаем кол-во видео
				if($user_id != $id){
					$video_cnt = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_videos` WHERE owner_user_id = '{$id}' {$sql_privacy} AND public_id = '0'", false, "user_{$id}/videos_num{$cache_pref_videos}");
					$row['user_videos_num'] = $video_cnt['cnt'];
				}
					
				$sql_videos = $db->super_query("SELECT id, title, add_date, comm_num, photo FROM `".PREFIX."_videos` WHERE owner_user_id = '{$id}' {$sql_privacy} AND public_id = '0' ORDER by `add_date` DESC LIMIT 0,2", 1, "user_{$id}/page_videos_user{$cache_pref_videos}");
				
				$tpl->load_template('profile_video.tpl');
				foreach($sql_videos as $row_videos){
					$tpl->set('{photo}', $row_videos['photo']);
					$tpl->set('{id}', $row_videos['id']);
					$tpl->set('{user-id}', $id);
					$tpl->set('{title}', stripslashes($row_videos['title']));
					$tpl->set('{comm-num}', $row_videos['comm_num'].' '.gram_record($row_videos['comm_num'], 'comments'));
					megaDate(strtotime($row_videos['add_date']), '');
					$tpl->compile('videos');
				}
			}
			
			//################### Подписки ###################//
			if($row['user_subscriptions_num']){
				$tpl->result['subscriptions'] = mozg_cache('/subscr_user_'.$id);
				if(!$tpl->result['subscriptions']){
					$sql_subscriptions = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo, user_country_city_name, user_status FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.user_id AND  	tb1.subscriptions = 1 ORDER by `friends_date` DESC LIMIT 0,5", 1);
					$tpl->load_template('profile_subscription.tpl');
					foreach($sql_subscriptions as $row_subscr){
						$tpl->set('{user-id}', $row_subscr['friend_id']);
						$tpl->set('{name}', $row_subscr['user_search_pref']);
						
						if($row_subscr['user_status'])
							$tpl->set('{info}', stripslashes(iconv_substr($row_subscr['user_status'], 0, 24, 'utf-8')));
						else {
							$country_city = explode('|', $row_subscr['user_country_city_name']);
							$tpl->set('{info}', $country_city[1]);
						}
						
						if($row_subscr['user_photo'])
							$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_subscr['friend_id'].'/50_'.$row_subscr['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
						$tpl->compile('subscriptions');
					}
					mozg_create_cache('/subscr_user_'.$id, $tpl->result['subscriptions']);
				}
			}

			//################### Музыка ###################//
			if($row['user_audio']){
				$sql_audio = $db->super_query("SELECT url, artist, name FROM `".PREFIX."_audio` WHERE auser_id = '".$id."' ORDER by `adate` DESC LIMIT 0, 3", 1, 'user_'.$id.'/audios_profile');
				$tpl->load_template('audio/profile.tpl');
				$jid = 0;
				foreach($sql_audio as $row_audio){
					$jid++;
					$tpl->set('{jid}', $jid);
					$tpl->set('{uid}', $id);
					$tpl->set('{url}', $row_audio['url']);
					$tpl->set('{artist}', stripslashes($row_audio['artist']));
					$tpl->set('{name}', stripslashes($row_audio['name']));
					$tpl->compile('audios');
				}
			}
			
			//################### Праздники друзей ###################//
			if($user_id == $id AND !$_SESSION['happy_friends_block_hide']){
				$sql_happy_friends = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo, user_birthday FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '".$id."' AND tb1.friend_id = tb2.user_id  AND subscriptions = 0 AND user_day = '".date('j', $server_time)."' AND user_month = '".date('n', $server_time)."' ORDER by `user_last_visit` DESC LIMIT 0, 50", 1);
				$tpl->load_template('profile_happy_friends.tpl');
				$cnt_happfr = 0;
				foreach($sql_happy_friends as $happy_row_friends){
					$cnt_happfr++;
					$tpl->set('{user-id}', $happy_row_friends['friend_id']);
					$tpl->set('{user-name}', $happy_row_friends['user_search_pref']);
					$user_birthday = explode('-', $happy_row_friends['user_birthday']);
					$tpl->set('{user-age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]));
					if($happy_row_friends['user_photo']) $tpl->set('{ava}', '/uploads/users/'.$happy_row_friends['friend_id'].'/100_'.$happy_row_friends['user_photo']);
					else $tpl->set('{ava}', '{theme}/images/100_no_ava.png');	
					$tpl->compile('happy_all_friends');
				}
			}

			//################### Загрузка стены ###################//
			if($row['user_wall_num'])
				include ENGINE_DIR.'/modules/wall.php';
			
			//Общие друзья
			if($row['user_friends_num'] AND $id != $user_info['user_id']){
				
				$count_common = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_friends` tb1 INNER JOIN `".PREFIX."_friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$id}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0");
				
				if($count_common['cnt']){
				
					$sql_mutual = $db->super_query("SELECT tb1.friend_id, tb3.user_photo, user_search_pref FROM `".PREFIX."_users` tb3, `".PREFIX."_friends` tb1 INNER JOIN `".PREFIX."_friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$id}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0 AND tb1.friend_id = tb3.user_id ORDER by rand() LIMIT 0, 3", 1);
					
					$tpl->load_template('profile_friends.tpl');
					
					foreach($sql_mutual as $row_mutual){
						
						$friend_info_mutual = explode(' ', $row_mutual['user_search_pref']);
						
						$tpl->set('{user-id}', $row_mutual['friend_id']);
						$tpl->set('{name}', $friend_info_mutual[0]);
						$tpl->set('{last-name}', $friend_info_mutual[1]);
						
						if($row_mutual['user_photo'])
							$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row_mutual['friend_id'].'/50_'.$row_mutual['user_photo']);
						else
							$tpl->set('{ava}', '{theme}/images/no_ava_50.png');
							
						$tpl->compile('mutual_friends');
						
					}
				
				}
				
			}

			//################### Загрузка самого профиля ###################//
			$tpl->load_template('profile.tpl');

			if($count_common['cnt']){
			
				$tpl->set('{mutual_friends}', $tpl->result['mutual_friends']);
				$tpl->set('{mutual-num}', $count_common['cnt']);
				$tpl->set('[common-friends]', '');
				$tpl->set('[/common-friends]', '');
			
			} else
				$tpl->set_block("'\\[common-friends\\](.*?)\\[/common-friends\\]'si","");

			$tpl->set('{user-id}', $row['user_id']);
			
			//Страна и город
			$tpl->set('{country}', $user_country_city_name_exp[0]);
			$tpl->set('{country-id}', $row['user_country']);
			$tpl->set('{city}', $user_country_city_name_exp[1]);
			$tpl->set('{city-id}', $row['user_city']);
			
			//Если человек сидит с мобильнйо версии
			if($row_online['user_logged_mobile']) $mobile_icon = '<img src="{theme}/images/spacer.gif" class="mobile_online" />';
			else $mobile_icon = '';
	
			if($row_online['user_last_visit'] >= $online_time)
				$tpl->set('{online}', $lang['online'].$mobile_icon);
			else {
				if(date('Y-m-d', $row_online['user_last_visit']) == date('Y-m-d', $server_time))
					$dateTell = langdate('сегодня в H:i', $row_online['user_last_visit']);
				elseif(date('Y-m-d', $row_online['user_last_visit']) == date('Y-m-d', ($server_time-84600)))
					$dateTell = langdate('вчера в H:i', $row_online['user_last_visit']);
				else
					$dateTell = langdate('j F Y в H:i', $row_online['user_last_visit']);
				if($row['user_sex'] == 2)
					$tpl->set('{online}', 'последний раз была '.$dateTell.$mobile_icon);
				else
					$tpl->set('{online}', 'последний раз был '.$dateTell.$mobile_icon);
			}
			
			if($row['user_city'] AND $row['user_country']){
				$tpl->set('[not-all-city]','');
				$tpl->set('[/not-all-city]','');
			} else 
				$tpl->set_block("'\\[not-all-city\\](.*?)\\[/not-all-city\\]'si","");
				
			if($row['user_country']){
				$tpl->set('[not-all-country]','');
				$tpl->set('[/not-all-country]','');
			} else 
				$tpl->set_block("'\\[not-all-country\\](.*?)\\[/not-all-country\\]'si","");
			
			//Конакты
			$xfields = xfieldsdataload($row['user_xfields']);
			$preg_safq_name_exp = explode(', ', 'phone, vk, od, skype, fb, icq, site');
			foreach($preg_safq_name_exp as $preg_safq_name){
				if($xfields[$preg_safq_name]){
					$tpl->set("[not-contact-{$preg_safq_name}]", '');
					$tpl->set("[/not-contact-{$preg_safq_name}]", '');
				} else
					$tpl->set_block("'\\[not-contact-{$preg_safq_name}\\](.*?)\\[/not-contact-{$preg_safq_name}\\]'si","");
			}
			$tpl->set('{vk}', '<a href="'.stripslashes($xfields['vk']).'" target="_blank">'.stripslashes($xfields['vk']).'</a>');
			$tpl->set('{od}', '<a href="'.stripslashes($xfields['od']).'" target="_blank">'.stripslashes($xfields['od']).'</a>');
			$tpl->set('{fb}', '<a href="'.stripslashes($xfields['fb']).'" target="_blank">'.stripslashes($xfields['fb']).'</a>');
			$tpl->set('{skype}', stripslashes($xfields['skype']));
			$tpl->set('{icq}', stripslashes($xfields['icq']));
			$tpl->set('{phone}', stripslashes($xfields['phone']));
			
			if(preg_match('/http:\/\//i', $xfields['site']))
				if(preg_match('/\.ru|\.com|\.net|\.su|\.in\.ua|\.ua/i', $xfields['site']))
					$tpl->set('{site}', '<a href="'.stripslashes($xfields['site']).'" target="_blank">'.stripslashes($xfields['site']).'</a>');
				else
					$tpl->set('{site}', stripslashes($xfields['site']));
			else
				$tpl->set('{site}', 'http://'.stripslashes($xfields['site']));
			
			if(!$xfields['vk'] && !$xfields['od'] && !$xfields['fb'] && !$xfields['skype'] && !$xfields['icq'] && !$xfields['phone'] && !$xfields['site'])
				$tpl->set_block("'\\[not-block-contact\\](.*?)\\[/not-block-contact\\]'si","");
			else {
				$tpl->set('[not-block-contact]', '');
				$tpl->set('[/not-block-contact]', '');
			}
				
			//Интересы
			$xfields_all = xfieldsdataload($row['user_xfields_all']);
			$preg_safq_name_exp = explode(', ', 'activity, interests, myinfo, music, kino, books, games, quote');
			
			if(!$xfields_all['activity'] AND !$xfields_all['interests'] AND !$xfields_all['myinfo'] AND !$xfields_all['music'] AND !$xfields_all['kino'] AND !$xfields_all['books'] AND !$xfields_all['games'] AND !$xfields_all['quote'])
				$tpl->set('{not-block-info}', '<div align="center" style="color:#999;">Информация отсутствует.</div>');
			else
				$tpl->set('{not-block-info}', '');
			
			foreach($preg_safq_name_exp as $preg_safq_name){
				if($xfields_all[$preg_safq_name]){
					$tpl->set("[not-info-{$preg_safq_name}]", '');
					$tpl->set("[/not-info-{$preg_safq_name}]", '');
				} else
					$tpl->set_block("'\\[not-info-{$preg_safq_name}\\](.*?)\\[/not-info-{$preg_safq_name}\\]'si","");
			}
			
			$tpl->set('{activity}', nl2br(stripslashes($xfields_all['activity'])));
			$tpl->set('{interests}', nl2br(stripslashes($xfields_all['interests'])));
			$tpl->set('{myinfo}', nl2br(stripslashes($xfields_all['myinfo'])));
			$tpl->set('{music}', nl2br(stripslashes($xfields_all['music'])));
			$tpl->set('{kino}', nl2br(stripslashes($xfields_all['kino'])));
			$tpl->set('{books}', nl2br(stripslashes($xfields_all['books'])));
			$tpl->set('{games}', nl2br(stripslashes($xfields_all['games'])));
			$tpl->set('{quote}', nl2br(stripslashes($xfields_all['quote'])));
			$tpl->set('{name}', $user_name_lastname_exp[0]);
			$tpl->set('{lastname}', $user_name_lastname_exp[1]);
			
			//День рождение
			$user_birthday = explode('-', $row['user_birthday']);
			$row['user_day'] = $user_birthday[2];
			$row['user_month'] = $user_birthday[1];
			$row['user_year'] = $user_birthday[0];
			
			if($row['user_day'] > 0 && $row['user_day'] <= 31 && $row['user_month'] > 0 && $row['user_month'] < 13){
				$tpl->set('[not-all-birthday]', '');
				$tpl->set('[/not-all-birthday]', '');
				
				if($row['user_day'] && $row['user_month'] && $row['user_year'] > 1929 && $row['user_year'] < 2012)
					$tpl->set('{birth-day}', '<a href="/?go=search&day='.$row['user_day'].'&month='.$row['user_month'].'&year='.$row['user_year'].'" onClick="Page.Go(this.href); return false">'.langdate('j F Y', strtotime($row['user_year'].'-'.$row['user_month'].'-'.$row['user_day'])).' г.</a>');
				else
					$tpl->set('{birth-day}', '<a href="/?go=search&day='.$row['user_day'].'&month='.$row['user_month'].'" onClick="Page.Go(this.href); return false">'.langdate('j F', strtotime($row['user_year'].'-'.$row['user_month'].'-'.$row['user_day'])).'</a>');
			} else {
				$tpl->set_block("'\\[not-all-birthday\\](.*?)\\[/not-all-birthday\\]'si","");
			}
			
			//Показ скрытых текста только для владельца страницы
			if($user_info['user_id'] == $row['user_id']){
				$tpl->set('[owner]', '');
				$tpl->set('[/owner]', '');
				$tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si","");
			} else {
				$tpl->set('[not-owner]', '');
				$tpl->set('[/not-owner]', '');
				$tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si","");
			}
			
			// FOR MOBILE VERSION 1.0
			if($config['temp'] == 'mobile'){
									
				$avaPREFver = '50_';
				$noAvaPrf = 'no_ava_50.png';
								
			} else {
								
				$avaPREFver = '';
				$noAvaPrf = 'no_ava.gif';
								
			}
					
			//Аватарка
			if($row['user_photo']){
				$tpl->set('{ava}', $config['home_url'].'uploads/users/'.$row['user_id'].'/'.$avaPREFver.$row['user_photo']);
				$tpl->set('{display-ava}', 'style="display:block;"');
			} else {
				$tpl->set('{ava}', '/templates/Default/images/'.$noAvaPrf);
				$tpl->set('{display-ava}', 'style="display:none;"');
			}
			
			//Проверка пользователя
		if($row['user_real'] == 1){
	$tpl->set('{user_real}', '<img style="margin-left:5px" src="./templates/Default/images/icons/verifi.png" title="Подтверждённый пользователь">');
		} else {
	$tpl->set('{user_real}', '');
		}

			//################### Альбомы ###################//
			if($user_id == $id){
				$albums_privacy = false;
				$albums_count['cnt'] = $row['user_albums_num'];
			} else if($check_friend){
				$albums_privacy = "AND SUBSTRING(privacy, 1, 1) regexp '[[:<:]](1|2)[[:>:]]'";
				$albums_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_albums` WHERE user_id = '{$id}' {$albums_privacy}", false, "user_{$id}/albums_cnt_friends");
				$cache_pref = "_friends";
			} else {
				$albums_privacy = "AND SUBSTRING(privacy, 1, 1) = 1";
				$albums_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_albums` WHERE user_id = '{$id}' {$albums_privacy}", false, "user_{$id}/albums_cnt_all");
				$cache_pref = "_all";
			}
			$sql_albums = $db->super_query("SELECT aid, name, adate, photo_num, cover FROM `".PREFIX."_albums` WHERE user_id = '{$id}' {$albums_privacy} ORDER by `position` ASC LIMIT 0, 4", 1, "user_{$id}/albums{$cache_pref}");
			if($sql_albums){
				foreach($sql_albums as $row_albums){
					$row_albums['name'] = stripslashes($row_albums['name']);
					$album_date = megaDateNoTpl(strtotime($row_albums['adate']));
					$albums_photonums = gram_record($row_albums['photo_num'], 'photos');
					if($row_albums['cover'])
						$album_cover = "/uploads/users/{$id}/albums/{$row_albums['aid']}/c_{$row_albums['cover']}";
					else
						$album_cover = '{theme}/images/no_cover.png';
					$albums .= "<a href=\"/albums/view/{$row_albums['aid']}\" onClick=\"Page.Go(this.href); return false\" style=\"text-decoration:none\"><div class=\"profile_albums\"><img src=\"{$album_cover}\" /><div class=\"profile_title_album\">{$row_albums['name']}</div>{$row_albums['photo_num']} {$albums_photonums}<br />Обновлён {$album_date}<div class=\"clear\"></div></div></a>";
				}
			}
			$tpl->set('{albums}', $albums);
			$tpl->set('{albums-num}', $albums_count['cnt']);
			if($albums_count['cnt'] AND $config['album_mod'] == 'yes'){
				$tpl->set('[albums]', '');
				$tpl->set('[/albums]', '');
			} else
				$tpl->set_block("'\\[albums\\](.*?)\\[/albums\\]'si","");
				
			//Делаем проверки на существования запрашиваемого юзера у себя в друзьяз, заклаках, в подписка, делаем всё это если страницу смотрет другой человек
			if($user_id != $id){
			
				//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
				if($check_friend){
					$tpl->set('[yes-friends]', '');
					$tpl->set('[/yes-friends]', '');
					$tpl->set_block("'\\[no-friends\\](.*?)\\[/no-friends\\]'si","");
				} else {
					$tpl->set('[no-friends]', '');
					$tpl->set('[/no-friends]', '');
					$tpl->set_block("'\\[yes-friends\\](.*?)\\[/yes-friends\\]'si","");
				}
				
				//Проверка естьли запрашиваемый юзер в закладках у юзера который смотрит стр
				$check_fave = $db->super_query("SELECT user_id FROM `".PREFIX."_fave` WHERE user_id = '{$user_info['user_id']}' AND fave_id = '{$id}'");
				if($check_fave){
					$tpl->set('[yes-fave]', '');
					$tpl->set('[/yes-fave]', '');
					$tpl->set_block("'\\[no-fave\\](.*?)\\[/no-fave\\]'si","");
				} else {
					$tpl->set('[no-fave]', '');
					$tpl->set('[/no-fave]', '');
					$tpl->set_block("'\\[yes-fave\\](.*?)\\[/yes-fave\\]'si","");
				}

				//Проверка естьли запрашиваемый юзер в подписках у юзера который смотрит стр
				$check_subscr = $db->super_query("SELECT user_id FROM `".PREFIX."_friends` WHERE user_id = '{$user_info['user_id']}' AND friend_id = '{$id}' AND subscriptions = 1");
				if($check_subscr){
					$tpl->set('[yes-subscription]', '');
					$tpl->set('[/yes-subscription]', '');
					$tpl->set_block("'\\[no-subscription\\](.*?)\\[/no-subscription\\]'si","");
				} else {
					$tpl->set('[no-subscription]', '');
					$tpl->set('[/no-subscription]', '');
					$tpl->set_block("'\\[yes-subscription\\](.*?)\\[/yes-subscription\\]'si","");
				}
				
				//Проверка естьли запрашиваемый юзер в черном списке
				$MyCheckBlackList = MyCheckBlackList($id);
				if($MyCheckBlackList){
					$tpl->set('[yes-blacklist]', '');
					$tpl->set('[/yes-blacklist]', '');
					$tpl->set_block("'\\[no-blacklist\\](.*?)\\[/no-blacklist\\]'si","");
				} else {
					$tpl->set('[no-blacklist]', '');
					$tpl->set('[/no-blacklist]', '');
					$tpl->set_block("'\\[yes-blacklist\\](.*?)\\[/yes-blacklist\\]'si","");
				}
				
			}

			$author_info = explode(' ', $row['user_search_pref']);
			$tpl->set('{gram-name}', gramatikName($author_info[0]));
			
			$tpl->set('{friends-num}', $row['user_friends_num']);
			$tpl->set('{online-friends-num}', $online_friends['cnt']);
			$tpl->set('{notes-num}', $row['user_notes_num']);
			$tpl->set('{subscriptions-num}', $row['user_subscriptions_num']);
			$tpl->set('{videos-num}', $row['user_videos_num']);
			
			//Если есть заметки то выводим
			if($row['user_notes_num']){
				$tpl->set('[notes]', '');
				$tpl->set('[/notes]', '');
				$tpl->set('{notes}', $tpl->result['notes']);
			} else
				$tpl->set_block("'\\[notes\\](.*?)\\[/notes\\]'si","");

			//Если есть видео то выводим
			if($row['user_videos_num'] AND $config['video_mod'] == 'yes'){
				$tpl->set('[videos]', '');
				$tpl->set('[/videos]', '');
				$tpl->set('{videos}', $tpl->result['videos']);
			} else
				$tpl->set_block("'\\[videos\\](.*?)\\[/videos\\]'si","");

			//Если есть друзья, то выводим
			if($row['user_friends_num']){
				$tpl->set('[friends]', '');
				$tpl->set('[/friends]', '');
				$tpl->set('{friends}', $tpl->result['all_friends']);
			} else
				$tpl->set_block("'\\[friends\\](.*?)\\[/friends\\]'si","");
				
			//Кол-во подписок и Если есть друзья, то выводим
			if($row['user_subscriptions_num']){
				$tpl->set('[subscriptions]', '');
				$tpl->set('[/subscriptions]', '');
				$tpl->set('{subscriptions}', $tpl->result['subscriptions']);
			} else
				$tpl->set_block("'\\[subscriptions\\](.*?)\\[/subscriptions\\]'si","");
				
			//Если есть друзья на сайте, то выводим
			if($online_friends['cnt']){
				$tpl->set('[online-friends]', '');
				$tpl->set('[/online-friends]', '');
				$tpl->set('{online-friends}', $tpl->result['all_online_friends']);
			} else
				$tpl->set_block("'\\[online-friends\\](.*?)\\[/online-friends\\]'si","");
			
			//Если человек пришел после реги, то открываем ему окно загрузи фотографии
			if(intval($_GET['after'])){
				$tpl->set('[after-reg]', '');
				$tpl->set('[/after-reg]', '');
			} else
				$tpl->set_block("'\\[after-reg\\](.*?)\\[/after-reg\\]'si","");

			//Стена
			$tpl->set('{records}', $tpl->result['wall']);

			if($user_id != $id){
				if($user_privacy['val_wall1'] == 3 OR $user_privacy['val_wall1'] == 2 AND !$check_friend){
					$cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
					$row['user_wall_num'] = $cnt_rec['cnt'];
				}
			}
			
			$row['user_wall_num'] = $row['user_wall_num'] ? $row['user_wall_num'] : '';
			if($row['user_wall_num'] > 10){
				$tpl->set('[wall-link]', '');
				$tpl->set('[/wall-link]', '');
			} else
				$tpl->set_block("'\\[wall-link\\](.*?)\\[/wall-link\\]'si","");
			
			$tpl->set('{wall-rec-num}', $row['user_wall_num']);
			
			if($row['user_wall_num'])
				$tpl->set_block("'\\[no-records\\](.*?)\\[/no-records\\]'si","");
			else {
				$tpl->set('[no-records]', '');
				$tpl->set('[/no-records]', '');
			}
			
			//Статус
			$tpl->set('{status-text}', stripslashes($row['user_status']));
			
			if($row['user_status']){
				$tpl->set('[status]', '');
				$tpl->set('[/status]', '');
				$tpl->set_block("'\\[no-status\\](.*?)\\[/no-status\\]'si","");
			} else {
				$tpl->set_block("'\\[status\\](.*?)\\[/status\\]'si","");
				$tpl->set('[no-status]', '');
				$tpl->set('[/no-status]', '');
			}
			
			//Приватность сообщений
			if($user_privacy['val_msg'] == 1 OR $user_privacy['val_msg'] == 2 AND $check_friend){
				$tpl->set('[privacy-msg]', '');
				$tpl->set('[/privacy-msg]', '');
			} else
				$tpl->set_block("'\\[privacy-msg\\](.*?)\\[/privacy-msg\\]'si","");

			//Приватность стены
			if($user_privacy['val_wall1'] == 1 OR $user_privacy['val_wall1'] == 2 AND $check_friend OR $user_id == $id){
				$tpl->set('[privacy-wall]', '');
				$tpl->set('[/privacy-wall]', '');
			} else
				$tpl->set_block("'\\[privacy-wall\\](.*?)\\[/privacy-wall\\]'si","");
				
			if($user_privacy['val_wall2'] == 1 OR $user_privacy['val_wall2'] == 2 AND $check_friend OR $user_id == $id){
				$tpl->set('[privacy-wall]', '');
				$tpl->set('[/privacy-wall]', '');
			} else
				$tpl->set_block("'\\[privacy-wall\\](.*?)\\[/privacy-wall\\]'si","");

			//Приватность информации
			if($user_privacy['val_info'] == 1 OR $user_privacy['val_info'] == 2 AND $check_friend OR $user_id == $id){
				$tpl->set('[privacy-info]', '');
				$tpl->set('[/privacy-info]', '');
			} else
				$tpl->set_block("'\\[privacy-info\\](.*?)\\[/privacy-info\\]'si","");
			
			//Семейное положение
			$user_sp = explode('|', $row['user_sp']);
			if($user_sp[1]){
				$rowSpUserName = $db->super_query("SELECT user_search_pref, user_sp, user_sex FROM `".PREFIX."_users` WHERE user_id = '{$user_sp[1]}'");
				if($row['user_sex'] == 1) $check_sex = 2;
				if($row['user_sex'] == 2) $check_sex = 1;
				if($rowSpUserName['user_sp'] == $user_sp[0].'|'.$id OR $user_sp[0] == 5 AND $rowSpUserName['user_sex'] == $check_sex){
					$spExpName = explode(' ', $rowSpUserName['user_search_pref']);
					$spUserName = $spExpName[0].' '.$spExpName[1];
				}
			}
			if($row['user_sex'] == 1){
				$sp1 = '<a href="/?go=search&sp=1" onClick="Page.Go(this.href); return false">не женат</a>';
				$sp2 = "подруга <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp2_2 = '<a href="/?go=search&sp=2" onClick="Page.Go(this.href); return false">есть подруга</a>';
				$sp3 = "невеста <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp3_3 = '<a href="/?go=search&sp=3" onClick="Page.Go(this.href); return false">помовлен</a>';
				$sp4 = "жена <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp4_4 = '<a href="/?go=search&sp=4" onClick="Page.Go(this.href); return false">женат</a>';
				$sp5 = "любимая <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp5_5 = '<a href="/?go=search&sp=5" onClick="Page.Go(this.href); return false">влюблён</a>';
			}
			if($row['user_sex'] == 2){
				$sp1 = '<a href="/?go=search&sp=1" onClick="Page.Go(this.href); return false">не замужем</a>';
				$sp2 = "друг <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp2_2 = '<a href="/?go=search&sp=2" onClick="Page.Go(this.href); return false">есть друг</a>';
				$sp3 = "жених <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp3_3 = '<a href="/?go=search&sp=3" onClick="Page.Go(this.href); return false">помовлена</a>';
				$sp4 = "муж <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp4_4 = '<a href="/?go=search&sp=4" onClick="Page.Go(this.href); return false">замужем</a>';
				$sp5 = "любимый <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
				$sp5_5 = '<a href="/?go=search&sp=5" onClick="Page.Go(this.href); return false">влюблена</a>';
			}
			$sp6 = "партнёр <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
			$sp6_6 = '<a href="/?go=search&sp=6" onClick="Page.Go(this.href); return false">всё сложно</a>';
			$tpl->set('[sp]', '');
			$tpl->set('[/sp]', '');
			if($user_sp[0] == 1)
				$tpl->set('{sp}', $sp1);
			else if($user_sp[0] == 2)
				if($spUserName) $tpl->set('{sp}', $sp2);
				else $tpl->set('{sp}', $sp2_2);
			else if($user_sp[0] == 3)
				if($spUserName) $tpl->set('{sp}', $sp3);
				else $tpl->set('{sp}', $sp3_3);
			else if($user_sp[0] == 4)
				if($spUserName) $tpl->set('{sp}', $sp4);
				else $tpl->set('{sp}', $sp4_4);
			else if($user_sp[0] == 5)
				if($spUserName) $tpl->set('{sp}', $sp5);
				else $tpl->set('{sp}', $sp5_5);
			else if($user_sp[0] == 6)
				if($spUserName) $tpl->set('{sp}', $sp6);
				else $tpl->set('{sp}', $sp6_6);
			else if($user_sp[0] == 7)
				$tpl->set('{sp}', '<a href="/?go=search&sp=7" onClick="Page.Go(this.href); return false">в активном поиске</a>');
			else
				$tpl->set_block("'\\[sp\\](.*?)\\[/sp\\]'si","");
			
			//ЧС
			if(!$CheckBlackList){
				$tpl->set('[blacklist]', '');
				$tpl->set('[/blacklist]', '');
				$tpl->set_block("'\\[not-blacklist\\](.*?)\\[/not-blacklist\\]'si","");
			} else {
				$tpl->set('[not-blacklist]', '');
				$tpl->set('[/not-blacklist]', '');
				$tpl->set_block("'\\[blacklist\\](.*?)\\[/blacklist\\]'si","");
			}
			
			//################### Подарки ###################//
			if($row['user_gifts']){
				$sql_gifts = $db->super_query("SELECT gift FROM `".PREFIX."_gifts` WHERE uid = '{$id}' ORDER by `gdate` DESC LIMIT 0, 5", 1, "user_{$id}/gifts");
				foreach($sql_gifts as $row_gift){
					$gifts .= "<img src=\"/uploads/gifts/{$row_gift['gift']}.png\" class=\"gift_onepage\" />";
				}
				$tpl->set('[gifts]', '');
				$tpl->set('[/gifts]', '');
				$tpl->set('{gifts}', $gifts);
				$tpl->set('{gifts-text}', $row['user_gifts'].' '.gram_record($row['user_gifts'], 'gifts'));
			} else
				$tpl->set_block("'\\[gifts\\](.*?)\\[/gifts\\]'si","");
			
			//################### Интересные страницы ###################//
			if($row['user_public_num']){
				$sql_groups = $db->super_query("SELECT tb1.friend_id, tb2.id, title, photo, adres, status_text FROM `".PREFIX."_friends` tb1, `".PREFIX."_communities` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.id AND tb1.subscriptions = 2 ORDER by `traf` DESC LIMIT 0, 5", 1, "groups/".$id);
				foreach($sql_groups as $row_groups){
					if($row_groups['adres']) $adres = $row_groups['adres'];
					else $adres = 'public'.$row_groups['id'];
					if($row_groups['photo']) $ava_groups = "/uploads/groups/{$row_groups['id']}/50_{$row_groups['photo']}";
					else $ava_groups = "{theme}/images/no_ava_50.png";	
					$row_groups['status_text'] = iconv_substr($row_groups['status_text'], 0, 24, 'utf-8');
					$groups .= '<div class="onesubscription onesubscriptio2n cursor_pointer" onClick="Page.Go(\'/'.$adres.'\')"><a href="/'.$adres.'" onClick="Page.Go(this.href); return false"><img src="'.$ava_groups.'" /></a><div class="onesubscriptiontitle"><a href="/'.$adres.'" onClick="Page.Go(this.href); return false">'.stripslashes($row_groups['title']).'</a></div><span class="color777 size10">'.stripslashes($row_groups['status_text']).'</span></div>';
				}
				$tpl->set('[groups]', '');
				$tpl->set('[/groups]', '');
				$tpl->set('{groups}', $groups);
				$tpl->set('{groups-num}', $row['user_public_num']);
			} else
				$tpl->set_block("'\\[groups\\](.*?)\\[/groups\\]'si","");

			//################### Музыка ###################//
			if($row['user_audio'] AND $config['audio_mod'] == 'yes'){
				$tpl->set('[audios]', '');
				$tpl->set('[/audios]', '');
				$tpl->set('{audios}', $tpl->result['audios']);
				$tpl->set('{audios-num}', $row['user_audio'].' '.gram_record($row['user_audio'], 'audio'));
			} else
				$tpl->set_block("'\\[audios\\](.*?)\\[/audios\\]'si","");

			//################### Праздники друзей ###################//
			if($cnt_happfr){
				$tpl->set('{happy-friends}', $tpl->result['happy_all_friends']);
				$tpl->set('{happy-friends-num}', $cnt_happfr);
				$tpl->set('[happy-friends]', '');
				$tpl->set('[/happy-friends]', '');
			} else
				$tpl->set_block("'\\[happy-friends\\](.*?)\\[/happy-friends\\]'si","");

			//################### Обработка дополнительных полей ###################//
			$xfieldsdata = xfieldsdataload($row['xfields']);
			$xfields = profileload();
				
			foreach($xfields as $value){

				$preg_safe_name = preg_quote($value[0], "'");

				if(empty($xfieldsdata[$value[0]])){

					$tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);

				} else {

					$tpl->copy_template = str_replace("[xfgiven_{$preg_safe_name}]", "", $tpl->copy_template);
					$tpl->copy_template = str_replace("[/xfgiven_{$preg_safe_name}]", "", $tpl->copy_template);

				}

				$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes($xfieldsdata[$value[0]]), $tpl->copy_template);

			}
	
			if($id == 7) $tpl->set('{group}', '<font color="#f87d7d">Модератор</font>');
			else $tpl->set('{group}', '');

			//Обложка
			if($row['user_photo']){
			
				$avaImgIsinfo = getimagesize(ROOT_DIR."/uploads/users/{$row['user_id']}/{$row['user_photo']}");
				
				if($avaImgIsinfo[1] < 200){
					
					$rForme = $avaImgIsinfo[1] * 100 / 230 * 2;
					
					$ava_marg_top = 'style="margin-top:-'.$rForme.'px"';
					
				}
				
				$tpl->set('{cover-param-7}', $ava_marg_top);
				
			} else
				$tpl->set('{cover-param-7}', "");
			
			if($row['user_cover']){
				
				$imgIsinfo = getimagesize(ROOT_DIR."/uploads/users/{$id}/{$row['user_cover']}");
				
				$tpl->set('{cover}', "/uploads/users/{$id}/{$row['user_cover']}");
				$tpl->set('{cover-height}', $imgIsinfo[1]);
				$tpl->set('{cover-param}', '');
				$tpl->set('{cover-param-2}', 'no_display');
				$tpl->set('{cover-param-3}', 'style="position:absolute;z-index:2;display:block;margin-left:397px"');
				$tpl->set('{cover-param-4}', 'style="cursor:default"');
				$tpl->set('{cover-param-5}', 'style="top:-'.$row['user_cover_pos'].'px;position:relative"');
				$tpl->set('{cover-pos}', $row['user_cover_pos']);
				
				$tpl->set('[cover]', '');
				$tpl->set('[/cover]', '');

			} else {
				
				$tpl->set('{cover}', "");
				$tpl->set('{cover-param}', 'no_display');
				$tpl->set('{cover-param-2}', '');
				$tpl->set('{cover-param-3}', '');
				$tpl->set('{cover-param-4}', '');
				$tpl->set('{cover-param-5}', '');
				$tpl->set('{cover-pos}', '');
				$tpl->set_block("'\\[cover\\](.*?)\\[/cover\\]'si","");
				
			}

			//Rating
			if($row['user_rating'] > 1000){
				
				$tpl->set('{rating-class-left}', 'profile_rate_1000_left');
				$tpl->set('{rating-class-right}', 'profile_rate_1000_right');
				$tpl->set('{rating-class-head}', 'profile_rate_1000_head');
				
			} elseif($row['user_rating'] > 500){
				
				$tpl->set('{rating-class-left}', 'profile_rate_500_left');
				$tpl->set('{rating-class-right}', 'profile_rate_500_right');
				$tpl->set('{rating-class-head}', 'profile_rate_500_head');
				
			} else {
				
				$tpl->set('{rating-class-left}', '');
				$tpl->set('{rating-class-right}', '');
				$tpl->set('{rating-class-head}', '');
				
			}
			
			if(!$row['user_rating']) $row['user_rating'] = 0;
			$tpl->set('{rating}', $row['user_rating']);

			$tpl->compile('content');
			
			//Гости
			if($id != $user_info['user_id']){
			
				$checkGuest = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_guests` WHERE ouid = '{$id}' AND guid = '{$user_id}'");
				
				if($checkGuest['cnt'])
				
					$db->query("UPDATE `".PREFIX."_guests` SET gdate = '{$server_time}', new = '1' WHERE ouid = '{$id}' AND guid = '{$user_id}'");
					
				else
				
					$db->query("INSERT INTO `".PREFIX."_guests` SET gdate = '{$server_time}', ouid = '{$id}', guid = '{$user_id}', new = '1'");
				
				$db->super_query("UPDATE `".PREFIX."_users` SET guests = guests + 1 WHERE user_id = '{$id}'");
				
			}
			
			//Обновляем кол-во посищений на страницу, если юзер есть у меня в друзьях
			if($check_friend)
				$db->query("UPDATE LOW_PRIORITY `".PREFIX."_friends` SET views = views+1 WHERE user_id = '{$user_info['user_id']}' AND friend_id = '{$id}' AND subscriptions = 0");
			
			//Вставляем в статистику
			if($user_info['user_id'] != $id){
			
				$stat_date = date('Ymd', $server_time);
				$stat_x_date = date('Ym', $server_time);
				
				$check_user_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_users_stats_log` WHERE user_id = '{$user_info['user_id']}' AND for_user_id = '{$id}' AND date = '{$stat_date}'");
				
				if(!$check_user_stat['cnt']){
					
					$check_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_users_stats` WHERE user_id = '{$id}' AND date = '{$stat_date}'");
					
					if($check_stat['cnt'])
						
						$db->query("UPDATE `".PREFIX."_users_stats` SET users = users + 1, views = views + 1 WHERE user_id = '{$id}' AND date = '{$stat_date}'");
						
					else
					
						$db->query("INSERT INTO `".PREFIX."_users_stats` SET user_id = '{$id}', date = '{$stat_date}', users = '1', views = '1', date_x = '{$stat_x_date}'");
					
					$db->query("INSERT INTO `".PREFIX."_users_stats_log` SET user_id = '{$user_info['user_id']}', date = '{$stat_date}', for_user_id = '{$id}'");
					
				} else {
					
					$db->query("UPDATE `".PREFIX."_users_stats` SET views = views + 1 WHERE user_id = '{$id}' AND date = '{$stat_date}'");
					
				}
			
			}


		}
	} else {
		$user_speedbar = $lang['no_infooo'];
		msgbox('', $lang['no_upage'], 'info');
	}
	
	$tpl->clear();
	$db->free();
} else {
	$user_speedbar = 'Информация';
	msgbox('', $lang['not_logged'], 'info');
}
?>