<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Filesystem, Gzip, Registry, Templates};
use JetBrains\PhpStorm\ArrayShape;
use FluffyDollop\Support\Cookie;
use Mozg\classes\Cache;
use Mozg\modules\Lang;

/**
 * @throws JsonException
 */
function informationText($array): string
{
    $db = Registry::get('db');
    $array = json_decode($array, 1, 512, JSON_THROW_ON_ERROR);
    $row = $db->super_query("SELECT user_search_pref FROM  users WHERE user_id = '" . ($array['type'] == 1 ? $array['oid2'] : $array['oid']) . "'");
    if ($array['type'] == 5) {
        $row2 = $db->super_query("SELECT user_search_pref FROM  users WHERE user_id = '" . $array['oid2'] . "'");
    } else {
        $row2['user_search_pref'] = null;
    }

    $text = array(
        0 => $row['user_search_pref'] . ' создал(а) беседу',
        1 => $row['user_search_pref'] . ' приглашен(а) в беседу',
        2 => $row['user_search_pref'] . ' покинул(а) беседу',
        3 => $row['user_search_pref'] . ' обновил(а) название беседы',
        4 => $row['user_search_pref'] . ' обновил(а) фотографию беседы',
        5 => $row['user_search_pref'] . ' исключил(а) участника "' . $row2['user_search_pref'] . '"',);
    return $text[$array['type']];
}

/**
 * @param $gc
 * @param $num
 * @param $type
 * @return void
 * @deprecated
 */
function navigation($gc, $num, $type): void
{
    global $tpl, $page;
    $gcount = $gc;
    $cnt = $num;
    $items_count = $cnt;
    $items_per_page = $gcount;
    $page_refers_per_page = 5;
    $pages = '';
    $pages_count = (($items_count % $items_per_page !== 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
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
        else $pages .= '<a href="' . $type . ($start_page + $index) . '" onClick="Page.Go(this.href); return false">' . ($start_page + $index) . '</a>';
    }
    if ($page + $page_refers_per_page <= $pages_count) {
        $pages .= '<a href="' . $type . ($start_page + $page_refers_per_page_count) . '" onClick="Page.Go(this.href); return false">...</a>';
        $pages .= '<a href="' . $type . $pages_count . '" onClick="Page.Go(this.href); return false">' . $pages_count . '</a>';
    }
    $resif = $cnt / $gcount;
    if (ceil($resif) == $page) $pages .= '';
    else $pages .= '<a href="' . $type . ($page + 1) . '" onClick="Page.Go(this.href); return false">&raquo;</a>';
    if ($pages_count <= 1) $pages = '';

    $content = <<<HTML
<div class="nav" id="nav">{$pages}</div>
HTML;
    $tpl->result['content'] .= $content;
}

/**
 * TODO !!!UPDATE
 * @param $items_per_page
 * @param $items_count
 * @param $type
 * @return string
 */
function navigationNew($items_per_page, $items_count, $type): string
{
    $page = intFilter('page', 1);
    $page_refers_per_page = 5;
    $pages = '';
    $pages_count = (($items_count % $items_per_page !== 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
    $start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
    $page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
    if ($page > 1) {
        $pages .= '<a href="' . $type . ($page - 1) . '" onClick="Page.Go(this.href); return false">&laquo;</a>';
    }
    if ($start_page > 1) {
        $pages .= '<a href="' . $type . '1" onClick="Page.Go(this.href); return false">1</a>';
        $pages .= '<a href="' . $type . ($start_page - 1) . '" onClick="Page.Go(this.href); return false">...</a>';
    }
    for ($index = -1; ++$index <= $page_refers_per_page_count - 1;) {
        if ($index + $start_page === $page) {
            $pages .= '<span>' . ($start_page + $index) . '</span>';
        } else {
            $pages .= '<a href="' . $type . ($start_page + $index) . '" onClick="Page.Go(this.href); return false">' . ($start_page + $index) . '</a>';
        }
    }
    if ($page + $page_refers_per_page <= $pages_count) {
        $pages .= '<a href="' . $type . ($start_page + $page_refers_per_page_count) . '" onClick="Page.Go(this.href); return false">...</a>';
        $pages .= '<a href="' . $type . $pages_count . '" onClick="Page.Go(this.href); return false">' . $pages_count . '</a>';
    }
    $res_if = $items_count / $items_per_page;
    if (ceil($res_if) === $page) {
        $pages .= '';
    } else {
        $pages .= '<a href="' . $type . ($page + 1) . '" onClick="Page.Go(this.href); return false">&raquo;</a>';
    }
    if ($pages_count <= 1) {
        $pages = '';
    }
    return "<div class=\"nav\" id=\"nav\">{$pages}</div>";
}

/**
 * @param $gc
 * @param $num
 * @param $id
 * @param $function
 * @param $act
 * @return void
 * @throws ErrorException
 * @deprecated
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
        else $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $index) . ', ' . $act . '); return false">' . ($start_page + $index) . '</a>';
    }
    if ($page + $page_refers_per_page <= $pages_count) {
        $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $page_refers_per_page_count) . ', ' . $act . '); return false">...</a>';
        $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . $pages_count . ', ' . $act . '); return false">' . $pages_count . '</a>';
    }
    $resif = $cnt / $gcount;
    if (ceil($resif) == $page) $pages .= '';
    else $pages .= '<a href="/" onClick="' . $function . '(' . $id . ', ' . ($page + 1) . ', ' . $act . '); return false">&raquo;</a>';
    if ($pages_count <= 1) $pages = '';
    $navigation = "<div class=\"nav\" id=\"nav\">{$pages}</div>";
    $tpl->result['content'] .= $navigation;
}

/**
 * @param $title
 * @param $text
 * @param $tpl_name
 * @return void
 * @throws ErrorException
 * @deprecated
 */
function msgbox($title, $text, $tpl_name) {
    global $tpl;
    $tpl_2 = new Templates();
    $config = settings_get();
    $tpl_2->dir = ROOT_DIR . '/templates/' . $config['temp'];
    $tpl_2->load_template($tpl_name . '.tpl');
    $tpl_2->set('{error}', $text);
    $tpl_2->set('{title}', $title);
    $tpl_2->compile('info');
    $tpl_2->clear();
    $tpl->result['info'] .= $tpl_2->result['info'];
}

/**
 * @deprecated
 * @param $tpl
 * @param $title
 * @param $text
 * @param $tpl_name
 * @return int
 */
function msgBoxNew($tpl, $title, $text, $tpl_name): int
{
    $tpl->load_template($tpl_name);
    $tpl->set('{error}', $text);
    $tpl->set('{title}', $title);
    $tpl->compile('content');
    return $tpl->render();
}

/**
 * @deprecated
 * @return bool
 */
function check_smartphone(): bool
{
    if (isset($_SESSION['mobile_enable']))
        return true;
    $phone_array = array('iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'mobile windows', 'cellphone', 'opera mobi', 'operamobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'symbos', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser', 'android');
    $agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    foreach ($phone_array as $value) {
        if (str_contains($agent, $value)) {
            return true;
        }
    }
    return false;
}

/**
 * TODO update
 * @return void
 */
function NoAjaxQuery() : void
{
    if (!empty($_POST['ajax']) && $_POST['ajax'] == 'yes' && $_SERVER['HTTP_REFERER'] !== $_SERVER['HTTP_HOST'] && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /index.php?go=none');
    }
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
 * @param $user_year
 * @param $user_month
 * @param $user_day
 * @return false|string|void
 */
function user_age($user_year, $user_month, $user_day)
{
    $server_time = Registry::get('server_time');
    if ($user_year) {
        $current_year = date('Y', $server_time);
        $current_month = date('n', $server_time);
        $current_day = date('j', $server_time);
        $current_str = strtotime($current_year . '-' . $current_month . '-' . $current_day);
        $current_user = strtotime($current_year . '-' . $user_month . '-' . $user_day);
        if ($current_str >= $current_user) {
            $user_age = $current_year - $user_year;
        } else {
            $user_age = $current_year - $user_year - 1;
        }
        if ($user_month && $user_day) {

            return $user_age . ' ' . declWord($user_age, 'user_age');
        }

        return false;//fixme
    }
}

function declWord(int $num, string $type): string
{
    $lang = Lang::getLang();
    $decl_list = require ROOT_DIR . "/lang/{$lang}/declensions.php";
    return (new \FluffyDollop\Support\Declensions($decl_list))->makeWord($num, $type);
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
 * FIXME
 * @return void
 */
function Hacking()
{
    global $lang;
    $ajax = checkAjax();
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
    } else {
        header('Location: /index.php?go=none');
    }
}

/**
 * @deprecated
 * @param $time
 * @param $mobile
 * @return void
 */
function OnlineTpl($time, $mobile = false) {
    global $tpl, $online_time, $lang;
    //Если человек сидит с мобильнйо версии
    if ($mobile)
        $mobile_icon = '<img src="/images/spacer.gif" class="mobile_online" />';
    else
        $mobile_icon = '';
    if ($time >= $online_time)
        return $tpl->set('{online}', $lang['online'] . $mobile_icon);
    else
        return $tpl->set('{online}', '');
}

/**
 * @deprecated
 * @param $tpl
 * @return int
 */
function AjaxTpl($tpl): int
{
    $config = settings_get();
    return print(str_replace('{theme}', '/templates/' . $config['temp'], $tpl->result['info'] . $tpl->result['content']));
}

function GenerateAlbumPhotosPosition($uid, $aid = false)
{
    $db = Registry::get('db');
    //Выводим все фотографии из альбома и обновляем их позицию только для просмотра альбома
    if ($uid and $aid) {
        $sql_ = $db->super_query("SELECT id FROM `photos` WHERE album_id = '{$aid}' ORDER by `position` ASC", true);
        $count = 1;
        $photo_info = '';
        foreach ($sql_ as $row) {
            $db->query("UPDATE LOW_PRIORITY `photos` SET position = '{$count}' WHERE id = '{$row['id']}'");
            $photo_info.= $count . '|' . $row['id'] . '||';
            $count++;
        }
        Cache::mozgCreateCache('user_' . $uid . '/position_photos_album_' . $aid, $photo_info);
    }
}
function CheckFriends($friendId): bool
{
    $user_info = Registry::get('user_info');
    /** @var string $user_info['user_id'] */
    $open_my_list = Cache::mozgCache("user_{$user_info['user_id']}/friends");
    return stripos($open_my_list, "u{$friendId}|") !== false;
}
function CheckBlackList($userId): bool
{
    $user_info = Registry::get('user_info');
    $open_my_list = Cache::mozgCache("user_{$userId}/blacklist");
    /** @var string $user_info['user_id'] */
    return stripos($open_my_list, "|{$user_info['user_id']}|") !== false;
}
function MyCheckBlackList($userId): bool
{
    $user_info = Registry::get('user_info');
    /** @var string $user_info['user_id'] */
    $open_my_list = Cache::mozgCache("user_{$user_info['user_id']}/blacklist");
    return stripos($open_my_list, "|{$userId}|") !== false;
}

/**
 * @param $source
 * @param bool $encode
 * @return array|mixed|string|string[]|null
 */
function word_filter($source, bool $encode = true)
{
    global $config;
    $safe_mode = false;
    if ($encode) {
        $all_words = @file(ENGINE_DIR . '/data/wordfilter.db.php');
        $find = array();
        $replace = array();
        if (!$all_words or !count($all_words)) return $source;
        foreach ($all_words as $word_line) {
            $word_arr = explode("|", $word_line);
            if (function_exists("get_magic_quotes_gpc") and get_magic_quotes_gpc()) {
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


//FOR MOBILE VERSION 1.0
if (isset($_GET['act']) && $_GET['act'] == 'change_mobile') $_SESSION['mobile'] = 1;
if (isset($_GET['act']) && $_GET['act'] == 'change_fullver') {
    $_SESSION['mobile'] = 2;
    header('Location: /');
}
if (check_smartphone()) {
    if ($_SESSION['mobile'] != 2)
        $config['temp'] = "mobile";
    $check_smartphone = true;
}
if (isset($_SESSION['mobile']) && $_SESSION['mobile'] == 1) {
    $config['temp'] = "mobile";
}


function normalizeName(string $value, bool $part = true): array|null|string
{
    $value = str_replace(chr(0), '', $value);

    $value = trim(strip_tags($value));
    $value = preg_replace("/\s+/u", "-", $value);
    if (empty($value)) {
        return null;
    }
    $value = str_replace("/", "-", $value);
    if ($part) {
        $value = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $value);
    } else {
        $value = preg_replace("/[^a-z0-9\_\-]+/mi", "", $value);
    }
    if (empty($value)) {
        return null;
    }
    $value = preg_replace('#[\-]+#i', '-', $value);
    return preg_replace('#[.]+#i', '.', $value);
}

function clearFilePath($file, $ext = array()): string
{
    $file = trim(str_replace(chr(0), '', (string)$file));
    $file = str_replace(array('/', '\\'), '/', $file);

    $path_parts = pathinfo($file);

    if (count($ext) && !in_array($path_parts['extension'], $ext, true)) {
        return '';
    }

    $filename = normalizeName($path_parts['basename'], true);

    if (!$filename) {
        return '';
    }

    $parts = array_filter(explode('/', $path_parts['dirname']), 'strlen');

    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' === $part) {
            continue;
        }
        if ('..' === $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = normalizeName($part, false);
        }
    }
    //fixme
    $path = implode('/', $absolutes);

    if ($path) {
        return implode('/', $absolutes) . '/' . $filename;
    }

    return '';

}

function cleanPath($path): string
{
    $path = trim(str_replace(chr(0), '', (string)$path));
    $path = str_replace(array('/', '\\'), '/', $path);
    $parts = array_filter(explode('/', $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' === $part) {
            continue;
        }
        if ('..' === $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = to_translit($part, false, false);
        }
    }
    return implode('/', $absolutes);
}

/**
 * @return array
 */
function settings_get(): array
{
    if (Registry::exists('config')) {
        return Registry::get('config');
    }
    if (file_exists('./data/config.php')) {
        $config = require './data/config.php';
        Registry::set('config', $config);
        return $config;
    }
    return [];
//    die("Vii Engine not installed. Please run install.php");//todo
}

/**
 * @deprecated
 * @param $tpl
 * @param array $params
 * @return int
 * @throws JsonException
 * @throws Exception
 */
function compile($tpl, array $params = array()): int
{
    $config = settings_get();

    $metatags['title'] = $params['metatags']['title'] ?? $config['home'];
//    $checkLang = Registry::get('checkLang') ?? 'Russian';
//    $lang = require ROOT_DIR . '/lang/' . $checkLang . '/site.php';
    $params['speedbar'] = $config['home'];
    $params['headers'] = '<title>' . $metatags['title'] . '</title>
    <meta name="generator" content="VII ENGINE" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />';


    $user_info = Registry::get('user_info');
    //Если юзер перешел по реферальной ссылке, то добавляем ид реферала в сессию
    if (isset($_GET['reg'])) {
        $_SESSION['ref_id'] = intFilter('reg');
    }

    if (isset($user_info['user_id'])) {
        //Загружаем кол-во новых новостей
        $CacheNews = Cache::mozgCache('user_' . $user_info['user_id'] . '/new_news');
        if ($CacheNews) {
            $params['new_news'] = "<div class=\"ic_newAct\" style=\"margin-left:18px\">{$CacheNews}</div>";
            $params['news_link'] = '/notifications';
        } else {
            $params['new_news'] = '';
            $params['news_link'] = '';
        }
//Загружаем кол-во новых подарков
        $CacheGift = Cache::mozgCache("user_{$user_info['user_id']}/new_gift");
        if ($CacheGift) {
            $params['new_ubm'] = "<div class=\"ic_newAct\" style=\"margin-left:20px\">{$CacheGift}</div>";
            $params['gifts_link'] = "/gifts{$user_info['user_id']}?new=1";
        } else {
            $params['new_ubm'] = '';
            $params['gifts_link'] = '/balance';
        }
//Новые сообщения
        $user_pm_num = $user_info['user_pm_num'];
        if ($user_pm_num) {
            $params['user_pm_num'] = "<div class=\"ic_newAct\" style=\"margin-left:37px\">{$user_pm_num}</div>";
        } else $params['user_pm_num'] = '';
//Новые друзья
        $user_friends_demands = $user_info['user_friends_demands'];
        if ($user_friends_demands) {
            $params['demands'] = "<div class=\"ic_newAct\">{$user_friends_demands}</div>";
            $params['requests_link'] = '/requests';
        } else {
            $params['demands'] = '';
            $params['requests_link'] = '';
        }
//ТП
        $user_support = $user_info['user_support'];
        if ($user_support) {
            $params['support'] = "<div class=\"ic_newAct\" style=\"margin-left:26px\">{$user_support}</div>";
        } else {
            $params['support'] = '';
        }
//Отметки на фото
        if ($user_info['user_new_mark_photos']) {
            $params['new_photos_link'] = 'newphotos';
            $params['new_photos'] = "<div class=\"ic_newAct\" style=\"margin-left:22px\">" . $user_info['user_new_mark_photos'] . "</div>";
        } else {
            $params['new_photos'] = '';
            $params['new_photos_link'] = $user_info['user_id'];
        }
//Приглашения в сообщества
        if ($user_info['invties_pub_num']) {
            $params['new_groups'] = "<div class=\"ic_newAct\" style=\"margin-left:26px\">" . $user_info['invties_pub_num'] . "</div>";
            $params['new_groups_lnk'] = '/groups?act=invites';
        } else {
            $params['new_groups'] = '';
            $params['new_groups_lnk'] = '/groups';
        }
    } else {
        $params['user_pm_num'] = '';
        $params['new_news'] = '';
        $params['new_ubm'] = '';
        $params['gifts_link'] = '/balance';
        $params['support'] = '';
        $params['news_link'] = '';
        $params['demands'] = '';
        $params['new_photos'] = '';
        $params['new_photos_link'] = 0;
        $params['requests_link'] = '/requests';
        $params['new_groups_lnk'] = '/groups';
        $params['new_groups'] = '';
    }

    //Если включен AJAX, то загружаем стр.
    if (requestFilter('ajax') == 'yes') {
        return compileAjax($tpl, $params);
    } else {
        return compileNoAjax($tpl, $params);
    }
}

/**
 * @deprecated
 * @param $tpl
 * @param $params
 * @return int
 * @throws JsonException
 * @throws Exception
 */
function compileAjax($tpl, $params): int
{
    $config = settings_get();
    //Если есть POST Запрос и значение AJAX, а $ajax не равняется "yes", то не пропускаем
    //FIXME
//    if ($_SERVER['REQUEST_METHOD'] == 'POST')
//        throw new Exception('Неизвестная ошибка');

    $speedbar = $speedbar ?? null;
    $spBar = $spBar ?? null;
    $metatags = $params['metatags'] ?? null;

    $metatags['title'] = $metatags['title'] ?? $config['home'];

//    if (isset($spBar) and $spBar)
//        $ajaxSpBar = "$('#speedbar').show().html('{$speedbar}')";
//    else
//        $ajaxSpBar = "$('#speedbar').hide()";

    $params['requests_link'] = $requests_link ?? '';
    $tpl->result['info'] = $tpl->result['info'] ?? '';
    if (Registry::get('logged')) {
        $result_ajax = array(
            'title' => $metatags['title'],
            'user_pm_num' => $params['user_pm_num'],
            'new_news' => $params['new_news'],
            'new_ubm' => $params['new_ubm'],
            'gifts_link' => $params['gifts_link'],
            'support' => $params['support'],
            'news_link' => $params['news_link'],
            'demands' => $params['demands'],
            'new_photos' => $params['new_photos'],
            'new_photos_link' => $params['new_photos_link'],
            'requests_link' => $params['requests_link'],
            'new_groups' => $params['new_groups'],
            'new_groups_lnk' => $params['new_groups_lnk'],
            'sbar' => $spBar ? $speedbar : '',
            'content' => $tpl->result['info'] . $tpl->result['content']
        );

    } else {
        $result_ajax = array(
            'title' => $metatags['title'],
            'sbar' => $spBar ? $speedbar : '',
            'content' => $tpl->result['info'] . $tpl->result['content']
        );
    }
    $res = str_replace('{theme}', '/templates/' . $config['temp'], $result_ajax);

    _e_json($res);
    $tpl->global_clear();
//        $db->close();
    if ($config['gzip'] == 'yes') {
        (new Gzip(false))->GzipOut();
    }
    return print('');
}

/**
 * @deprecated
 * @param $tpl
 * @param $params
 * @return int
 * @throws Exception
 *
 */
function compileNoAjax($tpl, $params): int
{
    $tpl->load_template('main.tpl');
//Если юзер авторизован
    if (Registry::get('logged')) {
        $user_info = Registry::get('user_info');
        $tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "");
        $tpl->set('[logged]', '');
        $tpl->set('[/logged]', '');
        $tpl->set('{my-page-link}', '/u' . $user_info['user_id']);
        $tpl->set('{my-id}', $user_info['user_id']);
        //Заявки в друзья
        $user_friends_demands = $user_info['user_friends_demands'];
        if ($user_friends_demands) {
            $tpl->set('{demands}', $params['demands']);
            $requests_link = $requests_link ?? '';
            $tpl->set('{requests-link}', $requests_link);
        } else {
            $tpl->set('{demands}', '');
            $tpl->set('{requests-link}', '');
        }
        //Новости
        if (isset($CacheNews) and $CacheNews) {
            $tpl->set('{new-news}', $params['new_news']);
            $tpl->set('{news-link}', $params['news_link']);
        } else {
            $tpl->set('{new-news}', '');
            $tpl->set('{news-link}', '');
        }
        //Сообщения
        if (!empty($params['user_pm_num'])) {
            $tpl->set('{msg}', $params['user_pm_num']);
        } else {
            $tpl->set('{msg}', '');
        }

        $user_support = $user_support ?? null;
        //Поддержка
        if ($user_support) {
            $tpl->set('{new-support}', $params['support']);
        } else {
            $tpl->set('{new-support}', '');
        }
        //Отметки на фото
        if ($user_info['user_new_mark_photos']) {
            $tpl->set('{my-id}', 'newphotos');
            $tpl->set('{new_photos}', $params['new_photos']);
        } else {
            $tpl->set('{new_photos}', '');
        }
        //UBM

        $CacheGift = $CacheGift ?? null;
        if ($CacheGift) {
            $tpl->set('{new-ubm}', $params['new_ubm']);
        } else {
            $tpl->set('{new-ubm}', '');
        }
        $tpl->set('{ubm-link}', $params['gifts_link']);

        //Приглашения в сообщества
        if ($user_info['invties_pub_num']) {
            $tpl->set('{new_groups}', $params['new_groups']);
        } else {
            $tpl->set('{new_groups}', '');
        }
        $tpl->set('{groups-link}', $params['new_groups_lnk']);

        if ($user_info['user_photo']) {
            $config = settings_get();
            $ava = '<img src="' . $config['home_url'] . 'uploads/users/' . $user_info['user_id'] . '/100_' . $user_info['user_photo'] . '"   style="width: 40px;height: 40px;" />';
        } else {
            $ava = '<img src="/images/no_ava_50.png" />';
        }
        $tpl->set('{user_photo}', $ava);
    } else {
        $tpl->set_block("'\\[logged\\](.*?)\\[/logged\\]'si", "");
        $tpl->set('[not-logged]', '');
        $tpl->set('[/not-logged]', '');
        $tpl->set('{my-page-link}', '');
    }

    $mobile_speedbar = $mobile_speedbar ?? '';
    $headers = $headers ?? '';
    $speedbar = $speedbar ?? '';

    $tpl->set('{header}', $headers);
    $tpl->set('{speedbar}', $speedbar);

    $tpl->set('{mobile-speedbar}', $mobile_speedbar);
    $tpl->set('{info}', $tpl->result['info'] ?? '');
// FOR MOBILE VERSION 1.0
    $config = settings_get();
    if ($config['temp'] == 'mobile') {
        $tpl->result['content'] = str_replace('onClick="Page.Go(this.href); return false"', '', $tpl->result['content']);
        if ($user_info['user_status']) {
            $tpl->set('{status-mobile}', '<span style="font-size:11px;color:#000">' . $user_info['user_status'] . '</span>');
        } else {
            $tpl->set('{status-mobile}', '<span style="font-size:11px;color:#999">установить статус</span>');
        }

        $user_friends_demands = $user_friends_demands ?? null;
        $user_support = $user_support ?? null;
        $CacheNews = $CacheNews ?? null;
        $CacheGift = $CacheGift ?? null;

        $new_actions = $user_friends_demands + $user_support + $CacheNews + $CacheGift + $user_info['user_pm_num'];
        if ($new_actions) {
            $tpl->set('{new-actions}', "<div class=\"ic_newAct\" style=\"margin-top:5px;margin-left:30px\">+{$new_actions}</div>");
        } else {
            $tpl->set('{new-actions}', "");
        }
    }
    $tpl->set('{content}', $tpl->result['content']);

    if (isset($spBar) && $spBar) {
        $tpl->set_block("'\\[speedbar\\](.*?)\\[/speedbar\\]'si", "");
    } else {
        $tpl->set('[speedbar]', '');
        $tpl->set('[/speedbar]', '');
    }
//BUILD JS
//    $checkLang = Registry::get('checkLang');
    $tpl->set('{js}', '<script type="text/javascript" src="/js/jquery.lib.js"></script>
<script type="text/javascript" src="/js/' . Lang::getLang() . '/lang.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
<script type="text/javascript" src="/js/audio.js"></script>
<script type="text/javascript" src="/js/profile.js"></script>');

// FOR MOBILE VERSION 1.0
    if (isset($user_info['user_photo']) && $user_info['user_photo']) {
        $tpl->set('{my-ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
    } else {
        $tpl->set('{my-ava}', "/images/no_ava_50.png");
    }

    if (isset($user_info['user_search_pref'])) {
        $tpl->set('{my-name}', $user_info['user_search_pref']);
    } else {
        $tpl->set('{my-name}', '');
    }

    if (isset($check_smartphone)) {
        $tpl->set('{mobile-link}', '<a href="/index.php?act=change_mobile">мобильная версия</a>');
    } else {
        $tpl->set('{mobile-link}', '');
    }

    $tpl->set('{lang}', Lang::getLang());
    $tpl->compile('main');
    header('Content-type: text/html; charset=utf-8');
    $result = str_replace('{theme}', '/templates/' . $config['temp'], $tpl->result['main']);
    print $result;
    $tpl->global_clear();
//    $db->close();
    if ($config['gzip'] === 'yes') {
        (new Gzip(false))->GzipOut();
    }

    return print('');
}

/**
 * @deprecated
 * @return Templates
 */
function tpl_init(): Templates
{
    $tpl = new Templates();
    $config = settings_get();
    $tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];
    define('TEMPLATE_DIR', $tpl->dir);
    return $tpl;
}

/**
 * @deprecated
 * @throws JsonException
 */
function compileAdmin($tpl): void
{
    $tpl->load_template('main.tpl');
    $config = settings_get();
    $admin_index = $config['admin_index'];
    $admin_link = $config['home_url'] . $config['admin_index'];
    if (Registry::get('logged')) {
        $stat_lnk = "<a href=\"{$admin_index}?mod=stats\" onclick=\"Page.Go(this.href); return false;\" style=\"margin-right:10px\">статистика</a>";
        $exit_lnk = "<a href=\"#\" onclick=\"Logged.log_out()\">выйти</a>";
    } else {
        $stat_lnk = '';
        $exit_lnk = '';
    }

    $box_width = 800;

    $tpl->set('{admin_link}', $admin_link);
    $tpl->set('{admin_index}', $admin_index);
    $tpl->set('{box_width}', $box_width);
    $tpl->set('{stat_lnk}', $stat_lnk);
    $tpl->set('{exit_lnk}', $exit_lnk);
    $tpl->set('{content}', $tpl->result['content']);
    $tpl->compile('main');
    if (requestFilter('ajax') == 'yes') {
        $metatags['title'] = $metatags['title'] ?? 'Панель управления';
        $result_ajax = array(
            'title' => $metatags['title'],
            'content' => $tpl->result['info'] . $tpl->result['content']
        );
        _e_json($result_ajax);
    } else {
        echo $tpl->result['main'];
    }
}

/**
 * @param string|null $view
 * @param array $variables
 * @return bool
 * @throws ErrorException
 * @throws JsonException
 */
function view(?string $view, array $variables = []): bool
{
    try {
        echo  (new Mozg\classes\View())->render($view,$variables);
        return true;
    }catch (Error){
        return false;
    }
}

/**
 * Device info
 * @return array
 */
#[ArrayShape(['browser' => 'string',
        'browser_ver' => 'string',
        'operating_system' => 'string',
        'device ' => 'string',
        'language ' => 'string'])]
function get_device(): array
{
    $browser = new \Sinergi\BrowserDetector\Browser();
    $operating_system = new \Sinergi\BrowserDetector\Os();
    $user_device = new \Sinergi\BrowserDetector\Device();
    $language = new \Sinergi\BrowserDetector\Language();

    return [
        'browser' => $browser->getName(),
        'browser_ver' => $browser->getVersion(),
        'operating_system' => $operating_system->getName(),
        'device ' => $user_device->getName(),
        'language ' => $language->getLanguage(),
    ];
}

function notify_ico(): string
{
    return "<div class=\"ic_msg\" id=\"myprof2\" onmouseout=\"$('.js_titleRemove').remove();\">
         <div id=\"new_msg\">
            <div class=\"ic_newAct\">4</div>
         </div>
     </div>";
}