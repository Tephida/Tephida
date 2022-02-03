<?php
/*
	Appointment: Основные функции сайта
	File: functions.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if (!defined('MOZG')) die('Hacking attempt!');
function informationText($array) {
    global $db;
    $array = json_decode($array, 1);
    $row = $db->super_query("SELECT user_search_pref FROM  " . PREFIX . "_users WHERE user_id = '" . ($array['type'] == 1 ? $array['oid2'] : $array['oid']) . "'");
    if ($array['type'] == 5) $row2 = $db->super_query("SELECT user_search_pref FROM  " . PREFIX . "_users WHERE user_id = '" . $array['oid2'] . "'");
    $text = array(0 => $row['user_search_pref'] . ' создал(а) беседу', 1 => $row['user_search_pref'] . ' приглашен(а) в беседу', 2 => $row['user_search_pref'] . ' покинул(а) беседу', 3 => $row['user_search_pref'] . ' обновил(а) название беседы', 4 => $row['user_search_pref'] . ' обновил(а) фотографию беседы', 5 => $row['user_search_pref'] . ' исключил(а) участника "' . $row2['user_search_pref'] . '"',);
    $text = $text[$array['type']];
    return $text;
}
class microTimer {
    function start() {
        global $starttime;
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    function stop() {
        global $starttime;
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round(($endtime - $starttime), 5);
        return $totaltime;
    }
}
function totranslit($var, $lower = true, $punkt = true) {
    global $langtranslit;
    if (is_array($var)) return "";
    if (!is_array($langtranslit) OR !count($langtranslit)) {
        $langtranslit = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', "ї" => "yi", "є" => "ye", 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya', "Ї" => "yi", "Є" => "ye",);
    }
    $var = str_replace(".php", "", $var);
    $var = trim(strip_tags($var));
    $var = preg_replace("/\s+/ms", "-", $var);
    $var = strtr($var, $langtranslit);
    if ($punkt) $var = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $var);
    else $var = preg_replace("/[^a-z0-9\_\-]+/mi", "", $var);
    $var = preg_replace('#[\-]+#i', '-', $var);
    if ($lower) $var = strtolower($var);
    if (strlen($var) > 200) {
        $var = substr($var, 0, 200);
        if (($temp_max = strrpos($var, '-'))) $var = substr($var, 0, $temp_max);
    }
    return $var;
}
function GetVar($v) {
    if (ini_get('magic_quotes_gpc')) return stripslashes($v);
    return $v;
}
function check_xss() {
    $url = html_entity_decode(urldecode($_SERVER['QUERY_STRING']));
    if ($url) {
        if ((strpos($url, '<') !== false) || (strpos($url, '>') !== false) || (strpos($url, '"') !== false) || (strpos($url, './') !== false) || (strpos($url, '../') !== false) || (strpos($url, '\'') !== false) || (strpos($url, '.php') !== false)) {
            if ($_GET['go'] != "search" AND $_GET['go'] != "messages") die('Hacking attempt!');
        }
    }
    $url = html_entity_decode(urldecode($_SERVER['REQUEST_URI']));
    if ($url) {
        if ((strpos($url, '<') !== false) || (strpos($url, '>') !== false) || (strpos($url, '"') !== false) || (strpos($url, '\'') !== false)) {
            if ($_GET['go'] != "search" AND $_GET['go'] != "messages") die('Hacking attempt!');
        }
    }
}
function langdate($format, $stamp) {
    global $langdate;
    return strtr(@date($format, $stamp), $langdate);
}
function navigation($gc, $num, $type) {
    global $tpl, $page;
    $gcount = $gc;
    $cnt = $num;
    $items_count = $cnt;
    $items_per_page = $gcount;
    $page_refers_per_page = 5;
    $pages = '';
    $pages_count = (($items_count % $items_per_page != 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
    $start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
    $page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
    if ($page > 1) $pages.= '<a href="' . $type . ($page - 1) . '" onClick="Page.Go(this.href); return false">&laquo;</a>';
    else $pages.= '';
    if ($start_page > 1) {
        $pages.= '<a href="' . $type . '1" onClick="Page.Go(this.href); return false">1</a>';
        $pages.= '<a href="' . $type . ($start_page - 1) . '" onClick="Page.Go(this.href); return false">...</a>';
    }
    for ($index = - 1;++$index <= $page_refers_per_page_count - 1;) {
        if ($index + $start_page == $page) $pages.= '<span>' . ($start_page + $index) . '</span>';
        else $pages.= '<a href="' . $type . ($start_page + $index) . '" onClick="Page.Go(this.href); return false">' . ($start_page + $index) . '</a>';
    }
    if ($page + $page_refers_per_page <= $pages_count) {
        $pages.= '<a href="' . $type . ($start_page + $page_refers_per_page_count) . '" onClick="Page.Go(this.href); return false">...</a>';
        $pages.= '<a href="' . $type . $pages_count . '" onClick="Page.Go(this.href); return false">' . $pages_count . '</a>';
    }
    $resif = $cnt / $gcount;
    if (ceil($resif) == $page) $pages.= '';
    else $pages.= '<a href="' . $type . ($page + 1) . '" onClick="Page.Go(this.href); return false">&raquo;</a>';
    if ($pages_count <= 1) $pages = '';
    $tpl_2 = new mozg_template();
    $tpl_2->dir = TEMPLATE_DIR;
    $tpl_2->load_template('nav.tpl');
    $tpl_2->set('{pages}', $pages);
    $tpl_2->compile('content');
    $tpl_2->clear();
    $tpl->result['content'].= $tpl_2->result['content'];
}
function box_navigation($gc, $num, $id, $function, $act) {
    global $tpl, $page;
    $gcount = $gc;
    $cnt = $num;
    $items_count = $cnt;
    $items_per_page = $gcount;
    $page_refers_per_page = 5;
    $pages = '';
    $pages_count = (($items_count % $items_per_page != 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
    $start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
    $page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
    if (!$act) $act = "''";
    else $act = "'{$act}'";
    if ($page > 1) $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($page - 1) . ', ' . $act . '); return false">&laquo;</a>';
    else $pages.= '';
    if ($start_page > 1) {
        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', 1, ' . $act . '); return false">1</a>';
        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page - 1) . ', ' . $act . '); return false">...</a>';
    }
    for ($index = - 1;++$index <= $page_refers_per_page_count - 1;) {
        if ($index + $start_page == $page) $pages.= '<span>' . ($start_page + $index) . '</span>';
        else $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $index) . ', ' . $act . '); return false">' . ($start_page + $index) . '</a>';
    }
    if ($page + $page_refers_per_page <= $pages_count) {
        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $page_refers_per_page_count) . ', ' . $act . '); return false">...</a>';
        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . $pages_count . ', ' . $act . '); return false">' . $pages_count . '</a>';
    }
    $resif = $cnt / $gcount;
    if (ceil($resif) == $page) $pages.= '';
    else $pages.= '<a href="/" onClick="' . $function . '(' . $id . ', ' . ($page + 1) . ', ' . $act . '); return false">&raquo;</a>';
    if ($pages_count <= 1) $pages = '';
    $tpl_2 = new mozg_template();
    $tpl_2->dir = TEMPLATE_DIR;
    $tpl_2->load_template('nav.tpl');
    $tpl_2->set('{pages}', $pages);
    $tpl_2->compile('content');
    $tpl_2->clear();
    $tpl->result['content'].= $tpl_2->result['content'];
}
function msgbox($title, $text, $tpl_name) {
    global $tpl;
    $tpl_2 = new mozg_template();
    $tpl_2->dir = TEMPLATE_DIR;
    $tpl_2->load_template($tpl_name . '.tpl');
    $tpl_2->set('{error}', $text);
    $tpl_2->set('{title}', $title);
    $tpl_2->compile('info');
    $tpl_2->clear();
    $tpl->result['info'].= $tpl_2->result['info'];
}
function check_smartphone() {
    if ($_SESSION['mobile_enable']) return true;
    $phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'mobile windows', 'cellphone', 'opera mobi', 'operamobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'symbos', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser', 'android');
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($phone_array as $value) {
        if (strpos($agent, $value) !== false) return true;
    }
    return false;
}
function creat_system_cache($prefix, $cache_text) {
    $filename = ENGINE_DIR . '/cache/system/' . $prefix . '.php';
    $fp = @fopen($filename, 'wb+');
    fwrite($fp, $cache_text);
    fclose($fp);
    @chmod($filename, 0666);
}
function system_cache($prefix) {
    $filename = ENGINE_DIR . '/cache/system/' . $prefix . '.php';
    return @file_get_contents($filename);
}
function mozg_clear_cache() {
    $fdir = opendir(ENGINE_DIR . '/cache/' . $folder);
    while ($file = readdir($fdir)) if ($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system') @unlink(ENGINE_DIR . '/cache/' . $file);
}
function mozg_clear_cache_folder($folder) {
    $fdir = opendir(ENGINE_DIR . '/cache/' . $folder);
    while ($file = readdir($fdir)) @unlink(ENGINE_DIR . '/cache/' . $folder . '/' . $file);
}
function mozg_clear_cache_file($prefix) {
    @unlink(ENGINE_DIR . '/cache/' . $prefix . '.tmp');
}
function mozg_mass_clear_cache_file($prefix) {
    $arr_prefix = explode('|', $prefix);
    foreach ($arr_prefix as $file) @unlink(ENGINE_DIR . '/cache/' . $file . '.tmp');
}
function mozg_create_folder_cache($prefix) {
    if (!is_dir(ROOT_DIR . '/system/cache/' . $prefix)) {
        @mkdir(ROOT_DIR . '/system/cache/' . $prefix, 0777);
        @chmod(ROOT_DIR . '/system/cache/' . $prefix, 0777);
    }
}
function mozg_create_cache($prefix, $cache_text) {
    $filename = ENGINE_DIR . '/cache/' . $prefix . '.tmp';
    if (file_exists($filename)) {
        $fp = fopen($filename, 'wb+');
        fwrite($fp, $cache_text);
        fclose($fp);
        @chmod($filename, 0666);
    } else file_put_contents($filename, $cache_text);
}
function mozg_cache($prefix) {
    $filename = ENGINE_DIR . '/cache/' . $prefix . '.tmp';
    return @file_get_contents($filename);
}
function strip_data($text) {
    $quotes = array("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"');
    $goodquotes = array("-", "+", "#");
    $repquotes = array("\-", "\+", "\#");
    $text = stripslashes($text);
    $text = trim(strip_tags($text));
    $text = str_replace($quotes, '', $text);
    $text = str_replace($goodquotes, $repquotes, $text);
    return $text;
}
function installationSelected($id, $options) {
    $source = str_replace('value="' . $id . '"', 'value="' . $id . '" selected', $options);
    return $source;
}
function ajax_utf8($source) {
    return $source;
}
function xfieldsdataload($id) {
    if ($id == "") return;
    $xfieldsdata = explode("||", $id);
    foreach ($xfieldsdata as $xfielddata) {
        list($xfielddataname, $xfielddatavalue) = explode("|", $xfielddata);
        $xfielddataname = str_replace("&#124;", "|", $xfielddataname);
        $xfielddataname = str_replace("__NEWL__", "\r\n", $xfielddataname);
        $xfielddatavalue = str_replace("&#124;", "|", $xfielddatavalue);
        $xfielddatavalue = str_replace("__NEWL__", "\r\n", $xfielddatavalue);
        $data[$xfielddataname] = $xfielddatavalue;
    }
    return $data;
}
function profileload() {
    $path = ENGINE_DIR . '/data/xfields.txt';
    $filecontents = file($path);
    if (!is_array($filecontents)) {
        exit('Невозможно загрузить файл');
    }
    foreach ($filecontents as $name => $value) {
        $filecontents[$name] = explode("|", trim($value));
        foreach ($filecontents[$name] as $name2 => $value2) {
            $value2 = str_replace("&#124;", "|", $value2);
            $value2 = str_replace("__NEWL__", "\r\n", $value2);
            $filecontents[$name][$name2] = $value2;
        }
    }
    return $filecontents;
}
function NoAjaxQuery() {
    if (clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST') header('Location: /index.php?go=none');
}
function replace_rn($source) {
    $find[] = "'\r'";
    $replace[] = "";
    $find[] = "'\n'";
    $replace[] = "";
    $source = preg_replace($find, $replace, $source);
    return $source;
}
function myBr($source) {
    $find[] = "'\r'";
    $replace[] = "<br />";
    $find[] = "'\n'";
    $replace[] = "<br />";
    $source = preg_replace($find, $replace, $source);
    return $source;
}
function myBrRn($source) {
    $find[] = "<br />";
    $replace[] = "\r";
    $find[] = "<br />";
    $replace[] = "\n";
    $source = str_replace($find, $replace, $source);
    return $source;
}
function rn_replace($source) {
    $find[] = "'\r'";
    $replace[] = "";
    $find[] = "'\n'";
    $replace[] = "";
    $source = preg_replace($find, $replace, $source);
    return $source;
}
function gram_record($num, $type) {
    $strlen_num = strlen($num);
    if ($num <= 21) {
        $numres = $num;
    } elseif ($strlen_num == 2) {
        $parsnum = substr($num, 1, 2);
        $numres = str_replace('0', '10', $parsnum);
    } elseif ($strlen_num == 3) {
        $parsnum = substr($num, 2, 3);
        $numres = str_replace('0', '10', $parsnum);
    } elseif ($strlen_num == 4) {
        $parsnum = substr($num, 3, 4);
        $numres = str_replace('0', '10', $parsnum);
    } elseif ($strlen_num == 5) {
        $parsnum = substr($num, 4, 5);
        $numres = str_replace('0', '10', $parsnum);
    }
    if ($type == 'rec') {
        if ($numres == 1) {
            $gram_num_record = 'запись';
        } elseif ($numres < 5) {
            $gram_num_record = 'записи';
        } elseif ($numres < 21) {
            $gram_num_record = 'записей';
        } elseif ($numres == 21) {
            $gram_num_record = 'запись';
        }
    }
    if ($type == 'comments') {
        if ($numres == 0) {
            $gram_num_record = 'комментариев';
        } elseif ($numres == 1) {
            $gram_num_record = 'комментарий';
        } elseif ($numres < 5) {
            $gram_num_record = 'комментария';
        } elseif ($numres < 21) {
            $gram_num_record = 'комментариев';
        } elseif ($numres == 21) {
            $gram_num_record = 'комментарий';
        }
    }
    if ($type == 'albums') {
        if ($numres == 0) {
            $gram_num_record = 'альбомов';
        } elseif ($numres == 1) {
            $gram_num_record = 'альбом';
        } elseif ($numres < 5) {
            $gram_num_record = 'альбома';
        } elseif ($numres < 21) {
            $gram_num_record = 'альбомов';
        } elseif ($numres == 21) {
            $gram_num_record = 'альбом';
        }
    }
    if ($type == 'photos') {
        if ($numres == 0) {
            $gram_num_record = 'фотографий';
        } elseif ($numres == 1) {
            $gram_num_record = 'фотография';
        } elseif ($numres < 5) {
            $gram_num_record = 'фотографии';
        } elseif ($numres < 21) {
            $gram_num_record = 'фотографий';
        } elseif ($numres == 21) {
            $gram_num_record = 'фотография';
        }
    }
    if ($type == 'friends_demands') {
        if ($numres == 0) {
            $gram_num_record = 'нет заявок в друзья';
        } elseif ($numres == 1) {
            $gram_num_record = 'заявка в друзья';
        } elseif ($numres < 5) {
            $gram_num_record = 'заявки в друзья';
        } elseif ($numres < 21) {
            $gram_num_record = 'заявок в друзья';
        } elseif ($numres == 21) {
            $gram_num_record = 'заявка в друзья';
        }
    }
    if ($type == 'user_age') {
        if ($numres == 0) {
            $gram_num_record = 'лет';
        } elseif ($numres == 1) {
            $gram_num_record = 'год';
        } elseif ($numres < 5) {
            $gram_num_record = 'года';
        } elseif ($numres < 21) {
            $gram_num_record = 'лет';
        } elseif ($numres == 21) {
            $gram_num_record = 'год';
        }
    }
    if ($type == 'friends_common') {
        if ($numres == 1) {
            $gram_num_record = 'общий друг';
        } elseif ($numres < 5) {
            $gram_num_record = 'общих друга';
        } elseif ($numres < 21) {
            $gram_num_record = 'общих друзей';
        } elseif ($numres == 21) {
            $gram_num_record = 'общий друг';
        }
    }
    if ($type == 'friends') {
        if ($numres == 0) {
            $gram_num_record = 'нет друзей';
        } elseif ($numres == 1) {
            $gram_num_record = 'друг';
        } elseif ($numres < 5) {
            $gram_num_record = 'друга';
        } elseif ($numres < 21) {
            $gram_num_record = 'друзей';
        } elseif ($numres == 21) {
            $gram_num_record = 'друг';
        }
    }
    if ($type == 'friends_online') {
        if ($numres == 0) {
            $gram_num_record = 'нет друзей';
        } elseif ($numres == 1) {
            $gram_num_record = 'друг на сайте';
        } elseif ($numres < 5) {
            $gram_num_record = 'друга на сайте';
        } elseif ($numres < 21) {
            $gram_num_record = 'друзей на сайте';
        } elseif ($numres == 21) {
            $gram_num_record = 'друг на сайте';
        }
    }
    if ($type == 'fave') {
        if ($numres == 0) {
            $gram_num_record = 'нет людей';
        } elseif ($numres == 1) {
            $gram_num_record = 'человек';
        } elseif ($numres < 5) {
            $gram_num_record = 'человека';
        } elseif ($numres < 21) {
            $gram_num_record = 'человек';
        } elseif ($numres == 21) {
            $gram_num_record = 'человек';
        }
    }
    if ($type == 'prev') {
        if ($numres == 0) {
            $gram_num_record = 'нет комментариев';
        } elseif ($numres == 1) {
            $gram_num_record = 'предыдущий';
        } elseif ($numres < 5) {
            $gram_num_record = 'предыдущие';
        } elseif ($numres < 21) {
            $gram_num_record = 'предыдущие';
        } elseif ($numres == 21) {
            $gram_num_record = 'предыдущий';
        }
    }
    if ($type == 'subscr') {
        if ($numres == 0) {
            $gram_num_record = 'нет подписчиков';
        } elseif ($numres == 1) {
            $gram_num_record = 'подписка';
        } elseif ($numres < 5) {
            $gram_num_record = 'подписки';
        } elseif ($numres < 21) {
            $gram_num_record = 'подписок';
        } elseif ($numres == 21) {
            $gram_num_record = 'подписка';
        }
    }
    if ($type == 'videos') {
        if ($numres == 0) {
            $gram_num_record = 'нет видеозаписей';
        } elseif ($numres == 1) {
            $gram_num_record = 'видеозапись';
        } elseif ($numres < 5) {
            $gram_num_record = 'видеозаписи';
        } elseif ($numres < 21) {
            $gram_num_record = 'видеозаписей';
        } elseif ($numres == 21) {
            $gram_num_record = 'видеозапись';
        }
    }
    if ($type == 'notes') {
        if ($numres == 0) {
            $gram_num_record = 'нет заметок';
        } elseif ($numres == 1) {
            $gram_num_record = 'заметка';
        } elseif ($numres < 5) {
            $gram_num_record = 'заметки';
        } elseif ($numres < 21) {
            $gram_num_record = 'заметок';
        } elseif ($numres == 21) {
            $gram_num_record = 'заметка';
        }
    }
    if ($type == 'like') {
        if ($numres == 0) {
            $gram_num_record = 'человеку';
        } elseif ($numres == 1) {
            $gram_num_record = 'человеку';
        } elseif ($numres < 5) {
            $gram_num_record = 'людям';
        } elseif ($numres < 21) {
            $gram_num_record = 'людям';
        } elseif ($numres == 21) {
            $gram_num_record = 'человеку';
        }
    }
    if ($type == 'updates') {
        if ($numres == 0) {
            $gram_num_record = '';
        } elseif ($numres == 1) {
            $gram_num_record = 'человека';
        } elseif ($numres < 5) {
            $gram_num_record = 'человек';
        } elseif ($numres < 21) {
            $gram_num_record = 'человек';
        } elseif ($numres == 21) {
            $gram_num_record = 'человека';
        }
    }
    if ($type == 'msg') {
        if ($numres == 1) {
            $gram_num_record = 'сообщение';
        } elseif ($numres < 5) {
            $gram_num_record = 'сообщения';
        } elseif ($numres < 21) {
            $gram_num_record = 'сообщений';
        } elseif ($numres == 21) {
            $gram_num_record = 'сообщение';
        }
    }
    if ($type == 'questions') {
        if ($numres == 1) {
            $gram_num_record = 'вопрос';
        } elseif ($numres < 5) {
            $gram_num_record = 'вопроса';
        } elseif ($numres < 21) {
            $gram_num_record = 'вопросов';
        } elseif ($numres == 21) {
            $gram_num_record = 'вопрос';
        }
    }
    if ($type == 'gifts') {
        if ($numres == 1) {
            $gram_num_record = 'подарок';
        } elseif ($numres < 5) {
            $gram_num_record = 'подарка';
        } elseif ($numres < 21) {
            $gram_num_record = 'подарков';
        } elseif ($numres == 21) {
            $gram_num_record = 'подарок';
        }
    }
    if ($type == 'groups_users') {
        if ($numres == 1) {
            $gram_num_record = 'участник';
        } elseif ($numres < 5) {
            $gram_num_record = 'участника';
        } elseif ($numres < 21) {
            $gram_num_record = 'участников';
        } elseif ($numres == 21) {
            $gram_num_record = 'участник';
        }
    }
    if ($type == 'groups') {
        if ($numres == 1) {
            $gram_num_record = 'сообществе';
        } elseif ($numres < 5) {
            $gram_num_record = 'сообществах';
        } elseif ($numres < 21) {
            $gram_num_record = 'сообществах';
        } elseif ($numres == 21) {
            $gram_num_record = 'сообществе';
        }
    }
    if ($type == 'subscribers') {
        if ($numres == 1) {
            $gram_num_record = 'подписчик';
        } elseif ($numres < 5) {
            $gram_num_record = 'подписчика';
        } elseif ($numres < 21) {
            $gram_num_record = 'подписчиков';
        } elseif ($numres == 21) {
            $gram_num_record = 'подписчик';
        }
    }
    if ($type == 'subscribers2') {
        if ($numres == 1) {
            $gram_num_record = 'Подписался <span id="traf2">' . $num . '</span> человек';
        } elseif ($numres < 5) {
            $gram_num_record = 'Подписались <span id="traf2">' . $num . '</span> человека';
        } elseif ($numres < 21) {
            $gram_num_record = 'Подписались <span id="traf2">' . $num . '</span> человек';
        } elseif ($numres == 21) {
            $gram_num_record = 'Подписался <span id="traf2">' . $num . '</span> человек';
        }
    }
    if ($type == 'feedback') {
        if ($numres == 1) {
            $gram_num_record = 'контакт';
        } elseif ($numres < 5) {
            $gram_num_record = 'контакта';
        } elseif ($numres < 21) {
            $gram_num_record = 'контактов';
        } elseif ($numres == 21) {
            $gram_num_record = 'контакт';
        }
    }
    if ($type == 'se_groups') {
        if ($numres == 1) {
            $gram_num_record = 'сообщество';
        } elseif ($numres < 5) {
            $gram_num_record = 'сообщества';
        } elseif ($numres < 21) {
            $gram_num_record = 'сообществ';
        } elseif ($numres == 21) {
            $gram_num_record = 'сообщество';
        }
    }
    if ($type == 'audio') {
        if ($numres == 1) {
            $gram_num_record = 'песня';
        } elseif ($numres < 5) {
            $gram_num_record = 'песни';
        } elseif ($numres < 21) {
            $gram_num_record = 'песен';
        } elseif ($numres == 21) {
            $gram_num_record = 'песня';
        }
    }
    if ($type == 'video_views') {
        if ($numres == 1) {
            $gram_num_record = 'просмотр';
        } elseif ($numres < 5) {
            $gram_num_record = 'просмотра';
        } elseif ($numres < 21) {
            $gram_num_record = 'просмотров';
        } elseif ($numres == 21) {
            $gram_num_record = 'просмотр';
        }
    }
    return $gram_num_record;
}
function gramatikName($source) {
    $name_u_gram = $source;
    $str_1_name = strlen($name_u_gram);
    $str_2_name = $str_1_name - 2;
    $str_3_name = substr($name_u_gram, $str_2_name, $str_1_name);
    $str_5_name = substr($name_u_gram, 0, $str_2_name);
    $str_4_name = strtr($str_3_name, array('ай' => 'ая', 'ил' => 'ила', 'др' => 'дра', 'ей' => 'ея', 'кс' => 'кса', 'ша' => 'ши', 'на' => 'ны', 'ка' => 'ки', 'ад' => 'ада', 'ма' => 'мы', 'ля' => 'ли', 'ня' => 'ни', 'ин' => 'ина', 'ик' => 'ика', 'ор' => 'ора', 'им' => 'има', 'ём' => 'ёма', 'ий' => 'ия', 'рь' => 'ря', 'тя' => 'ти', 'ся' => 'си', 'из' => 'иза', 'га' => 'ги', 'ур' => 'ура', 'са' => 'сы', 'ис' => 'иса', 'ст' => 'ста', 'ел' => 'ла', 'ав' => 'ава', 'он' => 'она', 'ра' => 'ры', 'ан' => 'ана', 'ир' => 'ира', 'рд' => 'рда', 'ян' => 'яна', 'ов' => 'ова', 'ла' => 'лы', 'ия' => 'ии', 'ва' => 'вой', 'ыч' => 'ыча', 'ич' => 'ича'));
    $name_user_gram = $str_5_name . $str_4_name;
    return $name_user_gram;
}
function Hacking() {
    global $ajax, $lang;
    if ($ajax) {
        NoAjaxQuery();
        echo <<<HTML
<script type="text/javascript">
document.title = '{$lang['error']}';
document.getElementById('speedbar').innerHTML = '{$lang['error']}';
document.getElementById('page').innerHTML = '{$lang['no_notes']}';
</script>
HTML;
        die();
    } else return header('Location: /index.php?go=none');
}
function textFilter($source, $substr_num = false, $strip_tags = false) {
    global $db;
    if (function_exists("get_magic_quotes_gpc") AND get_magic_quotes_gpc()) $source = stripslashes($source);
    $find = array('/data:/i', '/about:/i', '/vbscript:/i', '/onclick/i', '/onload/i', '/onunload/i', '/onabort/i', '/onerror/i', '/onblur/i', '/onchange/i', '/onfocus/i', '/onreset/i', '/onsubmit/i', '/ondblclick/i', '/onkeydown/i', '/onkeypress/i', '/onkeyup/i', '/onmousedown/i', '/onmouseup/i', '/onmouseover/i', '/onmouseout/i', '/onselect/i', '/javascript/i');
    $replace = array("d&#097;ta:", "&#097;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#097;vascript");
    $source = preg_replace("#<iframe#i", "&lt;iframe", $source);
    $source = preg_replace("#<script#i", "&lt;script", $source);
    if (!$substr_num) $substr_num = 25000;
    $source = $db->safesql(myBr(htmlspecialchars(substr(trim($source), 0, $substr_num))));
    $source = str_ireplace("{", "&#123;", $source);
    $source = str_ireplace("`", "&#96;", $source);
    $source = str_ireplace("{theme}", "&#123;theme}", $source);
    $source = preg_replace($find, $replace, $source);
    if ($strip_tags) $source = strip_tags($source);
    return $source;
}
function user_age($user_year, $user_month, $user_day) {
    global $server_time;
    if ($user_year) {
        $current_year = date('Y', $server_time);
        $current_month = date('n', $server_time);
        $current_day = date('j', $server_time);
        $current_str = strtotime($current_year . '-' . $current_month . '-' . $current_day);
        $current_user = strtotime($current_year . '-' . $user_month . '-' . $user_day);
        if ($current_str >= $current_user) $user_age = $current_year - $user_year;
        else $user_age = $current_year - $user_year - 1;
        if ($user_month AND $user_month AND $user_day) return $user_age . ' ' . gram_record($user_age, 'user_age');
        else return false;
    }
}
function megaDate($date, $func = false, $full = false) {
    global $tpl, $server_time;
    $date_comm = $date;
    if (date('Y-m-d', $date_comm) == date('Y-m-d', $server_time)) return $tpl->set('{date}', langdate('сегодня в H:i', $date_comm));
    elseif (date('Y-m-d', $date_comm) == date('Y-m-d', ($server_time - 84600))) return $tpl->set('{date}', langdate('вчера в H:i', $date_comm));
    else if ($func == 'no_year') return $tpl->set('{date}', langdate('j M в H:i', $date_comm));
    else if ($full) return $tpl->set('{date}', langdate('j F Y в H:i', $date_comm));
    else return $tpl->set('{date}', langdate('j M Y в H:i', $date_comm));
}
function megaDateNoTpl($date, $func = false, $full = false) {
    global $server_time;
    if (date('Y-m-d', $date) == date('Y-m-d', $server_time)) return $date = langdate('сегодня в H:i', $date);
    elseif (date('Y-m-d', $date) == date('Y-m-d', ($server_time - 84600))) return $date = langdate('вчера в H:i', $date);
    else if ($func == 'no_year') return $date = langdate('j M в H:i', $date);
    else if ($full) return $date = langdate('j F Y в H:i', $date);
    else return $date = langdate('j M Y в H:i', $date);
}
function OnlineTpl($time, $mobile = false) {
    global $tpl, $online_time, $lang;
    //Если человек сидит с мобильнйо версии
    if ($mobile) $mobile_icon = '<img src="{theme}/images/spacer.gif" class="mobile_online" />';
    else $mobile_icon = '';
    if ($time >= $online_time) return $tpl->set('{online}', $lang['online'] . $mobile_icon);
    else return $tpl->set('{online}', '');
}
function AjaxTpl() {
    global $tpl, $config;
    echo str_replace('{theme}', '/templates/' . $config['temp'], $tpl->result['info'] . $tpl->result['content']);
}
function GenerateAlbumPhotosPosition($uid, $aid = false) {
    global $db;
    //Выводим все фотографии из альбома и обновляем их позицию только для просмотра альбома
    if ($uid AND $aid) {
        $sql_ = $db->super_query("SELECT id FROM `" . PREFIX . "_photos` WHERE album_id = '{$aid}' ORDER by `position` ASC", 1);
        $count = 1;
        foreach ($sql_ as $row) {
            $db->query("UPDATE LOW_PRIORITY `" . PREFIX . "_photos` SET position = '{$count}' WHERE id = '{$row['id']}'");
            $photo_info.= $count . '|' . $row['id'] . '||';
            $count++;
        }
        mozg_create_cache('user_' . $uid . '/position_photos_album_' . $aid, $photo_info);
    }
}
function CheckFriends($friendId) {
    global $user_info;
    $openMyList = mozg_cache("user_{$user_info['user_id']}/friends");
    if (stripos($openMyList, "u{$friendId}|") !== false) return true;
    else return false;
}
function CheckBlackList($userId) {
    global $user_info;
    $openMyList = mozg_cache("user_{$userId}/blacklist");
    if (stripos($openMyList, "|{$user_info['user_id']}|") !== false) return true;
    else return false;
}
function MyCheckBlackList($userId) {
    global $user_info;
    $openMyList = mozg_cache("user_{$user_info['user_id']}/blacklist");
    if (stripos($openMyList, "|{$userId}|") !== false) return true;
    else return false;
}
function check_ip($ips) {
    $_IP = $_SERVER['REMOTE_ADDR'];
    $blockip = FALSE;
    if (is_array($ips)) {
        foreach ($ips as $ip_line) {
            $ip_arr = rtrim($ip_line['ip']);
            $ip_check_matches = 0;
            $db_ip_split = explode(".", $ip_arr);
            $this_ip_split = explode(".", $_IP);
            for ($i_i = 0;$i_i < 4;$i_i++) {
                if ($this_ip_split[$i_i] == $db_ip_split[$i_i] or $db_ip_split[$i_i] == '*') {
                    $ip_check_matches+= 1;
                }
            }
            if ($ip_check_matches == 4) {
                $blockip = $ip_line['ip'];
                break;
            }
        }
    }
    return $blockip;
}
function word_filter($source, $encode = true) {
    global $config;
    $safe_mode = false;
    if ($encode) {
        $all_words = @file(ENGINE_DIR . '/data/wordfilter.db.php');
        $find = array();
        $replace = array();
        if (!$all_words or !count($all_words)) return $source;
        foreach ($all_words as $word_line) {
            $word_arr = explode("|", $word_line);
            if (function_exists("get_magic_quotes_gpc") AND get_magic_quotes_gpc()) {
                $word_arr[1] = addslashes($word_arr[1]);
            }
            if ($word_arr[4]) {
                $register = "";
            } else $register = "i";
            if ($config['charset'] == "utf-8") $register.= "u";
            $allow_find = true;
            if ($word_arr[5] == 1 AND $safe_mode) $allow_find = false;
            if ($word_arr[5] == 2 AND !$safe_mode) $allow_find = false;
            if ($allow_find) {
                if ($word_arr[3]) {
                    $find_text = "#(^|\b|\s|\<br \/\>)" . preg_quote($word_arr[1], "#") . "(\b|\s|!|\?|\.|,|$)#" . $register;
                    if ($word_arr[2] == "") $replace_text = "\\1";
                    else $replace_text = "\\1<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->\\2";
                } else {
                    $find_text = "#(" . preg_quote($word_arr[1], "#") . ")#" . $register;
                    if ($word_arr[2] == "") $replace_text = "";
                    else $replace_text = $word_arr[2];
                }
                if ($word_arr[6]) {
                    if (preg_match($find_text, $source)) {
                        return $source;
                    }
                } else {
                    $find[] = $find_text;
                    $replace[] = $replace_text;
                }
            }
        }
        if (!count($find)) return $source;
        $source = preg_split('((>)|(<))', $source, -1, PREG_SPLIT_DELIM_CAPTURE);
        $count = count($source);
        for ($i = 0;$i < $count;$i++) {
            if ($source[$i] == "<" or $source[$i] == "[") {
                $i++;
                continue;
            }
            if ($source[$i] != "") $source[$i] = preg_replace($find, $replace, $source[$i]);
        }
        $source = join("", $source);
    } else {
        $source = preg_replace("#<!--filter:(.+?)-->(.+?)<!--/filter-->#", "\\1", $source);
    }
    return $source;
}
function declOfNum($number, $titles) {
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($number % 100 > 4 AND $number % 100 < 20) ? 2 : $cases[min($number % 10, 5) ]];
}
function newGram($num, $a, $b, $c, $t = false) {
    if ($t) return declOfNum($num, array(sprintf($a, $num), sprintf($b, $num), sprintf($c, $num)));
    else return declOfNum($num, array(sprintf("%d {$a}", $num), sprintf("%d {$b}", $num), sprintf("%d {$c}", $num)));
}
//FOR MOBILE VERSION 1.0
if ($_GET['act'] == 'change_mobile') $_SESSION['mobile'] = 1;
if ($_GET['act'] == 'change_fullver') {
    $_SESSION['mobile'] = 2;
    header('Location: /');
}
if (check_smartphone()) {
    if ($_SESSION['mobile'] != 2) $config['temp'] = "mobile";
    $check_smartphone = true;
}
if ($_SESSION['mobile'] == 1) {
    $config['temp'] = "mobile";
}
function AntiSpam($act, $text = false) {
    global $db, $user_info, $server_time;
    if ($text) $text = md5($text);
    /* Типы
    1 - Друзья
    2 - Сообщения не друзьям
    3 - Записей на стену
    4 - Проверка на одинаковый текст
    5 - Комментарии к записям (стены групп/людей)
    */
    //Антиспам дата
    $antiDate = date('Y-m-d', $server_time);
    $antiDate = strtotime($antiDate);
    //Лимиты на день
    $max_frieds = 40; #макс. заявок в друзья
    $max_msg = 40; #макс. сообщений не друзьям
    $max_wall = 500; #макс. записей на стену
    $max_identical = 100; #макс. одинаковых текстовых данных
    $max_comm = 2000; #макс. комментариев к записям на стенах людей и сообществ
    $max_groups = 5; #макс. сообществ за день
    //Если антиспам на друзей
    if ($act == 'friends') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '1' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_frieds) {
            die('antispam_err');
        }
    }
    //Если антиспам на сообщения
    elseif ($act == 'messages') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '2' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_msg) {
            die('antispam_err');
        }
    }
    //Если антиспам на проверку стены
    elseif ($act == 'wall') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '3' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_wall) {
            die('antispam_err');
        }
    }
    //Если антиспам на одинаковые тестовые данные
    elseif ($act == 'identical') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '4' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}' AND txt = '{$text}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_identical) {
            die('antispam_err');
        }
    }
    //Если антиспам на проверку комментов
    elseif ($act == 'comments') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '5' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_comm) {
            die('antispam_err');
        }
    }
    //Если антиспам на проверку сообществ
    elseif ($act == 'groups') {
        //Проверяем в таблице
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_antispam` WHERE act = '6' AND user_id = '{$user_info['user_id']}' AND date = '{$antiDate}'");
        //Если кол-во, логов больше, то ставим блок
        if ($check['cnt'] >= $max_groups) {
            die('antispam_err');
        }
    }
}
function AntiSpamLogInsert($act, $text = false) {
    global $db, $user_info, $server_time;
    if ($text) $text = md5($text);
    //Антиспам дата
    $antiDate = date('Y-m-d', $server_time);
    $antiDate = strtotime($antiDate);
    //Если антиспам на друзей
    if ($act == 'friends') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '1', user_id = '{$user_info['user_id']}', date = '{$antiDate}'");
        //Если антиспам на сообщения не друзьям
        
    } elseif ($act == 'messages') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '2', user_id = '{$user_info['user_id']}', date = '{$antiDate}'");
        //Если антиспам на стену
        
    } elseif ($act == 'wall') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '3', user_id = '{$user_info['user_id']}', date = '{$antiDate}'");
        //Если антиспам на одинаковых текстов
        
    } elseif ($act == 'identical') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '4', user_id = '{$user_info['user_id']}', date = '{$antiDate}', txt = '{$text}'");
        //Если антиспам комменты
        
    } elseif ($act == 'comments') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '5', user_id = '{$user_info['user_id']}', date = '{$antiDate}'");
        //Если антиспам комменты
        
    } elseif ($act == 'groups') {
        $db->query("INSERT INTO `" . PREFIX . "_antispam` SET act = '6', user_id = '{$user_info['user_id']}', date = '{$antiDate}'");
    }
}
?>