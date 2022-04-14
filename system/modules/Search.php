<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use FluffyDollop\Http\Request;
use Mozg\classes\Module;

class Search extends Module
{
    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    public function main()
    {
        $lang = $this->lang;
        $db = $this->db;
        $user_info = $this->user_info;
//        $logged = $this->logged();
        $config = settings_get();

        $params['title'] = 'search';

        $_SERVER['QUERY_STRING'] = strip_tags($_SERVER['QUERY_STRING']);
        $query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);
        $user_id = $user_info['user_id'] ?? null;

        $page = (new Request)->int('page', 1);
        $g_count = 20;
        $limit_page = ($page - 1) * $g_count;

        if ((new Request)->filter('query') !== null) {
//                $query = $db->safesql(Validation::strip_data(urldecode($request['query']))));
            $query = strip_data(urldecode((new Request)->filter('query')));
            if ((new Request)->filter('n') !== null) {
                $query = strip_data(urldecode((new Request)->filter('query')));
            }
            //Заменяем пробелы на проценты чтоб поиск был точнее
            $query = strtr($query, array(' ' => '%'));
        } else {
            $query = false;
        }

        $type = (new Request)->int('type');

        //Задаём параметры сортировки
        $sql_sort = '';
        if ((new Request)->int('sex') !== null) {
            $sex = (new Request)->int('sex');
            $sql_sort .= "AND user_sex = '{$sex}'";
        } else {
            $sex = '';
        }

        if (isset($request['day'])) {
            $day = (int)$request['day'];
            $sql_sort .= "AND user_day = '{$day}'";
        } else {
            $day = '';
        }

        if (isset($request['month'])) {
            $month = (int)$request['month'];
            $sql_sort .= "AND user_month = '{$month}'";
        } else {
            $month = '';
        }

        if (isset($request['year'])) {
            $year = (int)$request['year'];
            $sql_sort .= "AND user_year = '{$year}'";
        } else {
            $year = '';
        }

        if (isset($request['country'])) {
            $country = (int)$request['country'];
            $sql_sort .= "AND user_country = '{$country}'";
        } else {
            $country = 0;
        }

        if (isset($request['city'])) {
            $city = (int)$request['city'];
            $sql_sort .= "AND user_city = '{$city}'";
        } else {
            $city = 0;
        }

        if (isset($request['online'])) {
            $online = (int)$request['online'];
            $server_time = time();
            $online_time = $server_time - 60;
            $sql_sort .= "AND user_last_visit >= '{$online_time}'";
        } else {
            $online = '';
        }

        if (isset($request['user_photo'])) {
            $user_photo = (int)$request['user_photo'];
            $sql_sort .= "AND user_photo != ''";
        } else {
            $user_photo = '';
        }

        if (isset($request['sp'])) {
            $sp = (int)$request['sp'];
            $sql_sort .= "AND SUBSTRING(user_sp, 1, 1) regexp '[[:<:]]({$sp})[[:>:]]'";
        }

        $where_sql_gen = null;//bug: undefined

        if ($query || $sql_sort) {
            $where_sql_gen = "WHERE user_search_pref LIKE '%{$query}%' AND user_delet = '0' AND user_ban = '0'";
        }

        if (!$where_sql_gen) {
            $where_sql_gen = "WHERE user_delet = '0' AND user_ban = '0'";
        }

        $gcount = 30;

        //Делаем SQL Запрос в БД на вывод данных
        if ($type === 0) { //Если критерий поиск "по людям"
            $sql_query = "SELECT user_id, user_search_pref, user_photo, user_birthday, user_country_city_name, user_last_visit, user_logged_mobile FROM `users` {$where_sql_gen} {$sql_sort} ORDER by `user_rating` DESC LIMIT {$limit_page}, {$gcount}";
            $sql_count = "SELECT COUNT(*) AS cnt FROM `users` {$where_sql_gen} {$sql_sort}";
        } elseif ($type === 1 and $config['video_mod'] == 'yes' and $config['video_mod_search'] == 'yes') { //Если критерий поиск "по видеозаписям"
            $sql_query = "SELECT id, photo, title, add_date, comm_num, owner_user_id FROM `videos` WHERE title LIKE '%{$query}%' AND privacy = 1 ORDER by `add_date` DESC LIMIT {$limit_page}, {$gcount}";
            $sql_count = "SELECT COUNT(*) AS cnt FROM `videos` WHERE title LIKE '%{$query}%' AND privacy = 1";
        } elseif ($type === 3) { //Если критерий поиск "по сообщества"
            $sql_query = "SELECT id, title, photo, traf, adres FROM `communities` WHERE title LIKE '%{$query}%' AND del = '0' AND ban = '0' ORDER by `traf` DESC, `photo` DESC LIMIT {$limit_page}, {$gcount}";
            $sql_count = "SELECT COUNT(*) AS cnt FROM `communities` WHERE title LIKE '%{$query}%' AND del = '0' AND ban = '0'";
        } elseif ($type === 4 and $config['audio_mod'] == 'yes' and $config['audio_mod_search'] == 'yes') { //Если критерий поиск "по аудиозаписи"
            $sql_query = "SELECT audio.id, url, artist, title, oid, duration,users.user_search_pref FROM audio LEFT JOIN users ON audio.oid = users.user_id WHERE MATCH (title, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR title LIKE '%{$query}%' ORDER by `add_count` DESC LIMIT {$limit_page}, {$gcount}";
            $sql_count = "SELECT COUNT(*) AS cnt FROM `audio` WHERE MATCH (title, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR title LIKE '%{$query}%'";
        } elseif ($type === 2) { //Если критерий поиск "по аудиозаписи"
            $last_users = $db->super_query("SELECT `user_id`, `user_search_pref`, user_photo, user_birthday, `user_country_city_name`, user_last_visit, user_logged_mobile FROM `users` ORDER BY `user_rating` DESC LIMIT 6", 1);
            $last_tracks = $db->super_query("SELECT id, url, artist, title, oid, duration FROM `audio` ORDER BY `add_count` LIMIT 3", 1);
            $last_videos = $db->super_query("SELECT `id`, `owner_user_id`, `photo`, `comm_num`, `title` FROM `videos` ORDER BY id DESC LIMIT 7", 1);
            $last_photos = $db->super_query("SELECT `photo_name`, tb1.user_id, tb2.user_id, `name`, aid, `photo_num` FROM `photos` tb1, `albums` tb2 WHERE tb1.user_id = tb2.user_id ORDER BY tb1.id DESC LIMIT 10", 1);
            $last_group = $db->super_query("SELECT id, title, photo, traf, adres FROM `communities` WHERE del = '0' AND ban = '0' ORDER by `traf` DESC, `photo` DESC LIMIT 6", 1);

            $users = $db->super_query("SELECT COUNT(*) AS `cnt` FROM `users`");
            $tracks = $db->super_query("SELECT COUNT(*) AS cnt FROM `audio`");
            $videos = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos`");
            $photos = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos`");

//                $sql_query = "SELECT user_id, user_search_pref, user_photo, user_birthday, user_country_city_name, user_last_visit, user_logged_mobile FROM `users` {$where_sql_gen} {$sql_sort} ORDER by `user_rating` DESC LIMIT {$limit_page}, {$gcount}";
//                $sql_count = "SELECT COUNT(*) AS cnt FROM `users` {$where_sql_gen} {$sql_sort}";
//                $count['cnt'] = 1;
//                $sql_query = false;
//                $sql_count = false;
        } else {
            $sql_query = false;
            $sql_count = false;
        }

        if (isset($sql_query)) {
            $sql_ = $db->super_query($sql_query, true);
            $count = $db->super_query($sql_count);//FIXME undefined
        } else {
            $sql_ = array();
            $count = array();
            $count['cnt'] = 0;
        }

        if ($query) {
            $query = stripslashes(strtr($query, array('%' => ' ')));
        } else {
            $query = '';
        }

        $params['query_all'] = 'query=' . $query . '&type=3';
        $params['query_people'] = 'query=' . $query . '&type=1';
        $params['query_videos'] = 'query=' . $query . '&type=2';
        $params['query_groups'] = 'query=' . $query . '&type=4';
        $params['query_audios'] = 'query=' . $query . '&type=5';

        $params['type'] = $type;

        if ($type == 0) {

            if ($online) {
                $params['checked_online'] = 'checked';
            } else {
                $params['checked_online'] = '';
            }

            if ($user_photo) {
                $params['checked_user_photo'] = 'checked';
            } else {
                $params['checked_user_photo'] = '';
            }

//            $params['sex'] = Tools::installationSelected($sex, '<option value="1">Мужской</option><option value="2">Женский</option>');
//            $params['day'] = Tools::installationSelected($day, '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>');
//            $params['month'] = Tools::installationSelected($month, '<option value="1">Января</option><option value="2">Февраля</option><option value="3">Марта</option><option value="4">Апреля</option><option value="5">Мая</option><option value="6">Июня</option><option value="7">Июля</option><option value="8">Августа</option><option value="9">Сентября</option><option value="10">Октября</option><option value="11">Ноября</option><option value="12">Декабря</option>');
//            $params['year'] = Tools::installationSelected($year, '<option value="1930">1930</option><option value="1931">1931</option><option value="1932">1932</option><option value="1933">1933</option><option value="1934">1934</option><option value="1935">1935</option><option value="1936">1936</option><option value="1937">1937</option><option value="1938">1938</option><option value="1939">1939</option><option value="1940">1940</option><option value="1941">1941</option><option value="1942">1942</option><option value="1943">1943</option><option value="1944">1944</option><option value="1945">1945</option><option value="1946">1946</option><option value="1947">1947</option><option value="1948">1948</option><option value="1949">1949</option><option value="1950">1950</option><option value="1951">1951</option><option value="1952">1952</option><option value="1953">1953</option><option value="1954">1954</option><option value="1955">1955</option><option value="1956">1956</option><option value="1957">1957</option><option value="1958">1958</option><option value="1959">1959</option><option value="1960">1960</option><option value="1961">1961</option><option value="1962">1962</option><option value="1963">1963</option><option value="1964">1964</option><option value="1965">1965</option><option value="1966">1966</option><option value="1967">1967</option><option value="1968">1968</option><option value="1969">1969</option><option value="1970">1970</option><option value="1971">1971</option><option value="1972">1972</option><option value="1973">1973</option><option value="1974">1974</option><option value="1975">1975</option><option value="1976">1976</option><option value="1977">1977</option><option value="1978">1978</option><option value="1979">1979</option><option value="1980">1980</option><option value="1981">1981</option><option value="1982">1982</option><option value="1983">1983</option><option value="1984">1984</option><option value="1985">1985</option><option value="1986">1986</option><option value="1987">1987</option><option value="1988">1988</option><option value="1989">1989</option><option value="1990">1990</option><option value="1991">1991</option><option value="1992">1992</option><option value="1993">1993</option><option value="1994">1994</option><option value="1995">1995</option><option value="1996">1996</option><option value="1997">1997</option><option value="1998">1998</option><option value="1999">1999</option><option value="2000">2000</option><option value="2001">2001</option><option value="2002">2002</option><option value="2003">2003</option><option value="2004">2004</option><option value="2005">2005</option><option value="2006">2006</option><option value="2007">2007</option>');

            /**
             * Загружаем Страны
             */
//            $params['country'] = (new Support)->allCountry($country);
//            $params['city'] = (new Support)->allCity($country, $city);
        } elseif ($type === 2) {
            $sql_ = array();
        } else {
            $params['search_tab'] = false;
        }

//            $tpl->compile('info');

        //Загружаем шаблон на вывод если он есть одного юзера и выводим

        //Если критерий поиск "по людям"
        if ($type == 0) {
//                    $tpl->load_template('search/result_people.tpl');
            foreach ($sql_ as $key => $row) {
                $sql_[$key]['user_id'] = $row['user_id'];
                $sql_[$key]['name'] = $row['user_search_pref'];
                if ($row['user_photo']) {
                    $sql_[$key]['ava'] = $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/100_' . $row['user_photo'];
                } else {
                    $sql_[$key]['ava'] = '/images/100_no_ava.png';
                }
                //Возраст юзера
                $user_birthday = explode('-', $row['user_birthday']);
                $sql_[$key]['age'] = user_age($user_birthday['0'], $user_birthday['1'], $user_birthday['2']);
                $user_country_city_name = explode('|', $row['user_country_city_name']);
                $sql_[$key]['country'] = $user_country_city_name['0'];
                if (isset($user_country_city_name['1'])) {
                    $sql_[$key]['city'] = ', ' . $user_country_city_name['1'];
                } else {
                    $sql_[$key]['city'] = '';
                }
                if ($row['user_id'] != $user_id) {
                    $sql_[$key]['owner'] = true;
                } else {
                    $sql_[$key]['owner'] = false;
                }
//                $online = Profile::Online($row['user_last_visit']);
                if ($online) {
                    $sql_[$key]['online'] = $lang['online'];
                    $sql_[$key]['ava_online'] = 'avatar-online';
                } else {
                    $sql_[$key]['ava_online'] = '';
                    $sql_[$key]['online'] = '';
                }
            }
        } elseif ($type == 1) {
//                    $tpl->load_template('search/result_video.tpl');
            foreach ($sql_ as $key => $row) {
//                        $tpl->set('{photo}', );
                $sql_[$key]['photo'] = $row['photo'];
//                        $tpl->set('{title}', );
                $sql_[$key]['title'] = stripslashes($row['title']);
//                        $tpl->set('{user-id}', );
                $sql_[$key]['user_id'] = $row['owner_user_id'];
//                        $tpl->set('{id}', );
                $sql_[$key]['id'] = $row['id'];
//                        $tpl->set('{close-link}', );
                $sql_[$key]['close_link'] = '/index.php?' . $query_string . '&page=' . $page;
                $titles = array('комментарий', 'комментария', 'комментариев');//comments
//                        $tpl->set('{comm}', );
                $sql_[$key]['comm'] = $row['comm_num'] . ' ' . Gramatic::declOfNum((int)$row['comm_num'], $titles);

                //                        $tpl->set('{date}', );
                $sql_[$key]['date'] = Date::megaDate(strtotime($row['add_date']), '1', true);
//                        $tpl->compile('content');
            }

        } elseif ($type == 2) {
            foreach ($last_users as $key1 => $row) {
                $last_users[$key1]['user_id'] = $row['user_id'];
                $last_users[$key1]['name'] = $row['user_search_pref'];
                if ($row['user_photo']) {
                    $last_users[$key1]['ava'] = $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/100_' . $row['user_photo'];
                } else {
                    $last_users[$key1]['ava'] = '/images/100_no_ava.png';
                }
                //Возраст юзера
                $user_birthday = explode('-', $row['user_birthday']);
                $sql_[$key1]['age'] = Profile::user_age($user_birthday['0'], $user_birthday['1'], $user_birthday['2']);
                $user_country_city_name = explode('|', $row['user_country_city_name']);
                $sql_[$key1]['country'] = $user_country_city_name['0'];
                if (isset($user_country_city_name['1'])) {
                    $sql_[$key1]['city'] = ', ' . $user_country_city_name['1'];
                } else {
                    $sql_[$key1]['city'] = '';
                }
                if ($row['user_id'] != $user_id) {
                    $sql_[$key1]['owner'] = true;
                } else {
                    $sql_[$key1]['owner'] = false;
                }
                $online = Profile::Online($row['user_last_visit']);
                if ($online) {
                    $sql_[$key1]['online'] = $lang['online'];
                    $sql_[$key1]['ava_online'] = 'avatar-online';
                } else {
                    $sql_[$key1]['ava_online'] = '';
                    $sql_[$key1]['online'] = '';
                }
            }
            $params['last_users'] = $last_users;

            // FIXME $last_tracks undefined
            foreach ($last_tracks as $key2 => $row) {
                if (!$row['artist']) {
                    $last_tracks[$key2]['artist'] = 'Неизвестный исполнитель';
                }
                if (!$row['title']) {
                    $last_tracks[$key2]['title'] = 'Без названия';
                }
            }
            $params['users_count'] = $users['cnt'];
            $params['audios'] = $last_tracks;
            $params['audios_count'] = $tracks['cnt'];
            $params['videos'] = true;

            foreach ($last_group as $key3 => $row) {
                if ($row['photo']) {
                    $last_group[$key3]['ava'] = '/uploads/groups/' . $row['id'] . '/100_' . $row['photo'];
                } else {
                    $last_group[$key3]['ava'] = '/images/no_ava_groups_100.gif';
                }
                $last_group[$key3]['public_id'] = $row['id'];
                $last_group[$key3]['name'] = stripslashes($row['title']);
                $titles = array('участник', 'участника', 'участников');//groups_users
                $last_group[$key3]['traf'] = $row['traf'] . ' ' . Gramatic::declOfNum((int)$row['traf'], $titles);
                if ($row['adres']) {
                    $last_group[$key3]['adres'] = $row['adres'];
                } else {
                    $last_group[$key3]['adres'] = 'public' . $row['id'];
                }
            }
            $params['last_groups'] = $last_group;
            $sql_ = array();
            $params['navigation'] = '';
        } elseif ($type == 3) {
//                    $tpl->load_template('search/result_groups.tpl');
            foreach ($sql_ as $key => $row) {
                if ($row['photo']) {
                    $sql_[$key]['ava'] = '/uploads/groups/' . $row['id'] . '/100_' . $row['photo'];
                } else {
                    $sql_[$key]['ava'] = '/images/no_ava_groups_100.gif';
                }
                $sql_[$key]['public_id'] = $row['id'];
                $sql_[$key]['name'] = stripslashes($row['title']);
                $titles = array('участник', 'участника', 'участников');//groups_users
                $sql_[$key]['traf'] = $row['traf'] . ' ' . Gramatic::declOfNum((int)$row['traf'], $titles);
                if ($row['adres']) {
                    $sql_[$key]['adres'] = $row['adres'];
                } else {
                    $sql_[$key]['adres'] = 'public' . $row['id'];
                }
            }

            //Если критерий поиск "по аудизаписям"
        } elseif ($type == 4) {
            foreach ($sql_ as $key => $row) {
//                        $stime = gmdate("i:s", $row['duration']);
                if (!$row['artist']) {
                    $sql_[$key]['artist'] = 'Неизвестный исполнитель';
                }
                if (!$row['title']) {
                    $sql_[$key]['title'] = 'Без названия';
                }
            }

        }
        $params['search'] = $sql_;
        if ($type !== 2) {
//            $params['navigation'] = Tools::navigation($gcount, (int)$count['cnt'], '/search/?' . $query_string . '&page=');
        }

        $params['search_tab'] = true;
        $params['country'] = '';
        $params['city'] = '';
        $params['sex'] = '';
        $params['day'] = '';
        $params['month'] = '';
        $params['year'] = '';
        $params['navigation'] = '';
        return view('search.search', $params);
    }
}