<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG')) die('Hacking attempt!');

/**
 * @param string $source
 * @param int $substr_num
 * @param bool $strip_tags
 * @return array|string|null
 */
function textFilter(string $source, int $substr_num = 25000, bool $strip_tags = false): array|string|null
{
    return htmlspecialchars(stripslashes(trim($source)), 0, $substr_num);
}

function requestFilter(string $source, int $substr_num = 25000, bool $strip_tags = false): array|string|null
{
    if(isset($_POST[$source])){
        $source = $_POST[$source];
    }elseif(isset($_GET[$source])){
        $source = $_GET[$source];
    }else{
        return null;
    }
    return textFilter($source, $substr_num, $strip_tags);
}

function informationText($array): string
{
    global $db;
    $array = json_decode($array, 1);
    $row = $db->super_query("SELECT user_search_pref FROM  " . PREFIX . "_users WHERE user_id = '" . ($array['type'] == 1 ? $array['oid2'] : $array['oid']) . "'");
    if ($array['type'] == 5) $row2 = $db->super_query("SELECT user_search_pref FROM  " . PREFIX . "_users WHERE user_id = '" . $array['oid2'] . "'");
    $text = array(0 => $row['user_search_pref'] . ' создал(а) беседу', 1 => $row['user_search_pref'] . ' приглашен(а) в беседу', 2 => $row['user_search_pref'] . ' покинул(а) беседу', 3 => $row['user_search_pref'] . ' обновил(а) название беседы', 4 => $row['user_search_pref'] . ' обновил(а) фотографию беседы', 5 => $row['user_search_pref'] . ' исключил(а) участника "' . $row2['user_search_pref'] . '"',);
    return $text[$array['type']];
}

/**
 * @param string $value
 * @param bool $lower
 * @param bool $part
 * @return array|string|null
 */
function to_translit(string $value, bool $lower = true, bool $part = true): array|string|null
{
        $lang_translit = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            "ї" => "yi", "є" => "ye",

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            "Ї" => "yi", "Є" => "ye",
        );
    $value = str_replace(".php", "", $value);
    $value = trim(strip_tags($value));
    $value = preg_replace("/\s+/ms", "-", $value);
    $value = strtr($value, $lang_translit);
    if ($part)
        $value = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $value);
    else
        $value = preg_replace("/[^a-z0-9\_\-]+/mi", "", $value);
    $value = preg_replace('#[\-]+#i', '-', $value);
    if ($lower)
        $value = strtolower($value);
    if (strlen($value) > 200) {
        $value = substr($value, 0, 200);
        if (($temp_max = strrpos($value, '-'))) $value = substr($value, 0, $temp_max);
    }
    return $value;
}

/**
 * @param string $v
 * @return string
 */
function GetVar(string $v): string
{
    if (ini_get('magic_quotes_gpc'))
        return stripslashes($v);
    return $v;
}

/**
 * @return void
 */
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

/**
 * @param $format
 * @param $stamp
 * @return string
 */
function langdate($format, $stamp): string
{
    global $langdate;
    return strtr(date($format, intval($stamp)), $langdate);
}

/**
 * @param $gc
 * @param $num
 * @param $type
 * @return void
 */
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

/**
 * @param $gc
 * @param $num
 * @param $id
 * @param $function
 * @param $act
 * @return void
 */
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

/**
 * @param $title
 * @param $text
 * @param $tpl_name
 * @return void
 */
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

/**
 * @return bool
 */
function check_smartphone(): bool
{
    if ( isset($_SESSION['mobile_enable']))
        return true;
    $phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'mobile windows', 'cellphone', 'opera mobi', 'operamobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'symbos', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser', 'android');
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($phone_array as $value) {
        if (str_contains($agent, $value))
            return true;
    }
    return false;
}

/**
 * @param $prefix
 * @param $cache_text
 * @return void
 */
function creat_system_cache($prefix, $cache_text): void
{
    $filename = ENGINE_DIR . '/cache/system/' . $prefix . '.php';
    $fp = @fopen($filename, 'wb+');
    fwrite($fp, $cache_text);
    fclose($fp);
    @chmod($filename, 0666);
}
function system_cache($prefix): false|string
{
    return file_get_contents(ENGINE_DIR . '/cache/system/' . $prefix . '.php');
}
function mozg_clear_cache(): void
{
    $folder = '';
    $fdir = opendir(ENGINE_DIR . '/cache/' . $folder);
    while ($file = readdir($fdir))
        if ($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system')
            @unlink(ENGINE_DIR . '/cache/' . $file);
}
function mozg_clear_cache_folder($folder): void
{
    $fdir = opendir(ENGINE_DIR . '/cache/' . $folder);
    while ($file = readdir($fdir)) @unlink(ENGINE_DIR . '/cache/' . $folder . '/' . $file);
}
function mozg_clear_cache_file($prefix): bool
{
    return unlink(ENGINE_DIR . '/cache/' . $prefix . '.tmp');
}
function mozg_mass_clear_cache_file($prefix): void
{
    $arr_prefix = explode('|', $prefix);
    foreach ($arr_prefix as $file) @unlink(ENGINE_DIR . '/cache/' . $file . '.tmp');
}

/**
 * @throws Exception
 */
function mozg_create_folder_cache($prefix): void
{
//    if (!is_dir(ROOT_DIR . '/system/cache/' . $prefix)) {
//        @mkdir(ROOT_DIR . '/system/cache/' . $prefix, 0777);
//        @chmod(ROOT_DIR . '/system/cache/' . $prefix, 0777);
//    }
    createDir(ROOT_DIR . '/system/cache/' . $prefix);
}
function mozg_create_cache($prefix, $cache_text): false|int
{
    $filename = ENGINE_DIR . '/cache/' . $prefix . '.tmp';
    if (file_exists($filename)) {
        $fp = fopen($filename, 'wb+');
        fwrite($fp, $cache_text);
        fclose($fp);
        @chmod($filename, 0666);
        return 1;
    } else
        return file_put_contents($filename, $cache_text);
}
function mozg_cache($prefix): false|string
{
    $filename = ENGINE_DIR . '/cache/' . $prefix . '.tmp';
    return @file_get_contents($filename);
}

/**
 * @param $text
 * @return array|string
 */
function strip_data($text): array|string
{
    $quotes = array("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"');
    $goodquotes = array("-", "+", "#");
    $repquotes = array("\-", "\+", "\#");
    $text = stripslashes($text);
    $text = trim(strip_tags($text));
    $text = str_replace($quotes, '', $text);
    return str_replace($goodquotes, $repquotes, $text);
}

/**
 * @param $id
 * @param $options
 * @return array|string
 */
function installationSelected($id, $options): array|string
{
    return str_replace('value="' . $id . '"', 'value="' . $id . '" selected', $options);
}

/**
 * @param $id
 * @return array
 */
function xfieldsdataload(string $id) : array
{
    $x_fields_data = explode( "||", $id );
    $end = array_key_last($x_fields_data);
    if ($x_fields_data[$end] == false)
        unset($x_fields_data[$end]);

    $data = array();
    foreach ( $x_fields_data as $x_field_data ) {
        list ( $x_field_data_name, $x_field_data_value ) = explode( "|", $x_field_data );
        $x_field_data_name = str_replace( "&#124;", "|", $x_field_data_name );
        $x_field_data_name = str_replace( "__NEWL__", "\r\n", $x_field_data_name );
        $x_field_data_value = str_replace( "&#124;", "|", $x_field_data_value );
        $x_field_data_value = str_replace( "__NEWL__", "\r\n", $x_field_data_value );
        $data[$x_field_data_name] = trim($x_field_data_value);
    }
    return $data;
}

/**
 * @return array|false|void
 */
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

/**
 * @return void
 */
function NoAjaxQuery() : void
{
    if(!empty($_POST['ajax']) AND $_POST['ajax'] == 'yes')
        if (clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST']) AND $_SERVER['REQUEST_METHOD'] != 'POST')
            header('Location: /index.php?go=none');
}

/**
 * @param array|string $source
 * @return array|string|null
 */
function replace_rn(array|string $source): array|string|null
{
    $find[] = "'\r'";
    $replace[] = "";
    $find[] = "'\n'";
    $replace[] = "";
    return preg_replace($find, $replace, $source);
}

/**
 * @param array|string $source
 * @return array|string
 */
function myBr(array|string $source): array|string
{
    $find[] = "'\r'";
    $replace[] = "<br />";
    $find[] = "'\n'";
    $replace[] = "<br />";
    return preg_replace($find, $replace, $source);
}

/**
 * @param array|string $source
 * @return array|string
 */
function myBrRn(array|string $source): array|string
{
    $find[] = "<br />";
    $replace[] = "\r";
    $find[] = "<br />";
    $replace[] = "\n";
    return str_replace($find, $replace, $source);
}

/**
 * @param array|string $source
 * @return array|string
 */
function rn_replace(array|string $source): array|string
{
    $find[] = "'\r'";
    $replace[] = "";
    $find[] = "'\n'";
    $replace[] = "";
    return preg_replace($find, $replace, $source);
}

/**
 * @param $num
 * @param $type
 * @return string
 */
function gram_record($num, $type): string
{
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

/**
 * @param $source
 * @return string
 */
function gramatikName($source): string
{
    $name_u_gram = $source;
    $str_1_name = strlen($name_u_gram);
    $str_2_name = $str_1_name - 2;
    $str_3_name = substr($name_u_gram, $str_2_name, $str_1_name);
    $str_5_name = substr($name_u_gram, 0, $str_2_name);
    $str_4_name = strtr($str_3_name, array('ай' => 'ая', 'ил' => 'ила', 'др' => 'дра', 'ей' => 'ея', 'кс' => 'кса', 'ша' => 'ши', 'на' => 'ны', 'ка' => 'ки', 'ад' => 'ада', 'ма' => 'мы', 'ля' => 'ли', 'ня' => 'ни', 'ин' => 'ина', 'ик' => 'ика', 'ор' => 'ора', 'им' => 'има', 'ём' => 'ёма', 'ий' => 'ия', 'рь' => 'ря', 'тя' => 'ти', 'ся' => 'си', 'из' => 'иза', 'га' => 'ги', 'ур' => 'ура', 'са' => 'сы', 'ис' => 'иса', 'ст' => 'ста', 'ел' => 'ла', 'ав' => 'ава', 'он' => 'она', 'ра' => 'ры', 'ан' => 'ана', 'ир' => 'ира', 'рд' => 'рда', 'ян' => 'яна', 'ов' => 'ова', 'ла' => 'лы', 'ия' => 'ии', 'ва' => 'вой', 'ыч' => 'ыча', 'ич' => 'ича'));
    return $str_5_name . $str_4_name;
}

/**
 * @return void
 */
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
    } else
        return header('Location: /index.php?go=none');
}


/**
 * @param $user_year
 * @param $user_month
 * @param $user_day
 * @return false|string|void
 */
function user_age($user_year, $user_month, $user_day) {
    global $server_time;
    if ($user_year) {
        $current_year = date('Y', $server_time);
        $current_month = date('n', $server_time);
        $current_day = date('j', $server_time);
        $current_str = strtotime($current_year . '-' . $current_month . '-' . $current_day);
        $current_user = strtotime($current_year . '-' . $user_month . '-' . $user_day);
        if ($current_str >= $current_user)
            $user_age = $current_year - $user_year;
        else
            $user_age = $current_year - $user_year - 1;
        if ($user_month AND $user_month AND $user_day)
            return $user_age . ' ' . gram_record($user_age, 'user_age');
        else
            return false;
    }
}

/**
 * @param $date
 * @param $func
 * @param $full
 * @return void
 */
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
    if (date('Y-m-d', $date) == date('Y-m-d', $server_time))
        return $date = langdate('сегодня в H:i', $date);
    elseif (date('Y-m-d', $date) == date('Y-m-d', ($server_time - 84600)))
        return $date = langdate('вчера в H:i', $date);
    else if ($func == 'no_year')
        return $date = langdate('j M в H:i', $date);
    else if ($full)
        return $date = langdate('j F Y в H:i', $date);
    else
        return $date = langdate('j M Y в H:i', $date);
}
function OnlineTpl($time, $mobile = false) {
    global $tpl, $online_time, $lang;
    //Если человек сидит с мобильнйо версии
    if ($mobile)
        $mobile_icon = '<img src="{theme}/images/spacer.gif" class="mobile_online" />';
    else
        $mobile_icon = '';
    if ($time >= $online_time)
        return $tpl->set('{online}', $lang['online'] . $mobile_icon);
    else
        return $tpl->set('{online}', '');
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
        $photo_info = '';
        foreach ($sql_ as $row) {
            $db->query("UPDATE LOW_PRIORITY `" . PREFIX . "_photos` SET position = '{$count}' WHERE id = '{$row['id']}'");
            $photo_info.= $count . '|' . $row['id'] . '||';
            $count++;
        }
        mozg_create_cache('user_' . $uid . '/position_photos_album_' . $aid, $photo_info);
    }
}
function CheckFriends($friendId): bool
{
    global $user_info;
    $openMyList = mozg_cache("user_{$user_info['user_id']}/friends");
    if (stripos($openMyList, "u{$friendId}|") !== false)
        return true;
    else
        return false;
}
function CheckBlackList($userId): bool
{
    global $user_info;
    $openMyList = mozg_cache("user_{$userId}/blacklist");
    if (stripos($openMyList, "|{$user_info['user_id']}|") !== false)
        return true;
    else
        return false;
}
function MyCheckBlackList($userId): bool
{
    global $user_info;
    $openMyList = mozg_cache("user_{$user_info['user_id']}/blacklist");
    if (stripos($openMyList, "|{$userId}|") !== false)
        return true;
    else
        return false;
}

/**
 * @param $ips
 * @return false|mixed
 */
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

/**
 * @param $source
 * @param $encode
 * @return array|mixed|string|string[]|null
 */
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

/**
 * @param int $number
 * @param array $titles
 * @return mixed
 */
function declOfNum(int $number, array $titles): string
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($number % 100 > 4 AND $number % 100 < 20) ? 2 : $cases[min($number % 10, 5) ]];
}

/**
 * @param $num
 * @param $a
 * @param $b
 * @param $c
 * @param bool $t
 * @return mixed
 */
function newGram($num, $a, $b, $c, bool $t = false): string
{
    if ($t)
        return declOfNum($num, array(sprintf($a, $num), sprintf($b, $num), sprintf($c, $num)));
    else
        return declOfNum($num, array(sprintf("%d {$a}", $num), sprintf("%d {$b}", $num), sprintf("%d {$c}", $num)));
}
//FOR MOBILE VERSION 1.0
if (isset($_GET['act']) AND $_GET['act'] == 'change_mobile') $_SESSION['mobile'] = 1;
if (isset($_GET['act']) AND $_GET['act'] == 'change_fullver') {
    $_SESSION['mobile'] = 2;
    header('Location: /');
}
if (check_smartphone()) {
    if ($_SESSION['mobile'] != 2) $config['temp'] = "mobile";
    $check_smartphone = true;
}
if (isset($_SESSION['mobile']) AND $_SESSION['mobile'] == 1) {
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

/**
 * @param string $act
 * @param bool $text
 * @return void
 */
function AntiSpamLogInsert(string $act, bool|string $text = false): void
{
    global $db, $user_info, $server_time;
    if ($text)
        $text = md5($text);
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

/**
 * @throws Exception
 */
function createDir(string $dir, int $mode = 0777): void
{
    if (!is_dir($dir) && !mkdir($dir, $mode, true) && !is_dir($dir)) { // @ - dir may already exist
        throw new Exception("Unable to create directory '$dir' with mode " );
    }
}

function normalizeName(string $value, bool $part = true): array|null|string
{
    $value = str_replace(chr(0), '', $value);

    $value = trim(strip_tags($value));
    $value = preg_replace("/\s+/u", "-", $value);
    $value = str_replace("/", "-", $value);

    if ($part)
        $value = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $value);
    else
        $value = preg_replace("/[^a-z0-9\_\-]+/mi", "", $value);

    $value = preg_replace('#[\-]+#i', '-', $value);
    return preg_replace('#[.]+#i', '.', $value);
}

function clearFilePath($file, $ext = array()): string
{

    $file = trim(str_replace(chr(0), '', (string)$file));
    $file = str_replace(array('/', '\\'), '/', $file);

    $path_parts = pathinfo($file);

    if (count($ext)) {
        if (!in_array($path_parts['extension'], $ext)) return '';
    }

    $filename = normalizeName($path_parts['basename'], true);

    if (!$filename) return '';

    $parts = array_filter(explode('/', $path_parts['dirname']), 'strlen');

    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = normalizeName($part, false);
        }
    }

    $path = implode('/', $absolutes);

    if ($path)
        return implode('/', $absolutes) . '/' . $filename;
    else
        return '';

}

function cleanPath($path): string
{
    $path = trim(str_replace(chr(0), '', (string)$path));
    $path = str_replace(array('/', '\\'), '/', $path);
    $parts = array_filter(explode('/', $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = to_translit($part, false, false);
        }
    }

    return implode('/', $absolutes);
}

function clean_url($url)
{
    $url = str_replace("http://", "", strtolower($url));
    $url = str_replace("https://", "", $url);
    if (str_starts_with($url, 'www.'))
        $url = substr($url, 4);
    $url = explode('/', $url);
    $url = reset($url);
    $url = explode(':', $url);
    return reset($url);
}

function set_cookie($name, $value, $expires)
{
    if ($expires) {
        $expires = time() + ($expires * 86400);
    } else {
        $expires = FALSE;
    }
    if (PHP_VERSION < 5.2) {
        setcookie($name, $value, $expires, "/", DOMAIN . "; HttpOnly");
    } else {
        setcookie($name, $value, $expires, "/", DOMAIN, NULL, TRUE);
    }
}