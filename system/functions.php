<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;
use Mozg\classes\View;
use FluffyDollop\Http\{Request, Response};
use JetBrains\PhpStorm\ArrayShape;
use Mozg\classes\Cache;
use Mozg\classes\I18n;

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
//    global $tpl, $page;
//    $gcount = $gc;
//    $cnt = $num;
//    $items_count = $cnt;
//    $items_per_page = $gcount;
//    $page_refers_per_page = 5;
//    $pages = '';
//    $pages_count = (($items_count % $items_per_page !== 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
//    $start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
//    $page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
//    if ($page > 1) $pages.= '<a href="' . $type . ($page - 1) . '" onClick="Page.Go(this.href); return false">&laquo;</a>';
//    else $pages.= '';
//    if ($start_page > 1) {
//        $pages.= '<a href="' . $type . '1" onClick="Page.Go(this.href); return false">1</a>';
//        $pages.= '<a href="' . $type . ($start_page - 1) . '" onClick="Page.Go(this.href); return false">...</a>';
//    }
//    for ($index = - 1;++$index <= $page_refers_per_page_count - 1;) {
//        if ($index + $start_page == $page) $pages.= '<span>' . ($start_page + $index) . '</span>';
//        else $pages .= '<a href="' . $type . ($start_page + $index) . '" onClick="Page.Go(this.href); return false">' . ($start_page + $index) . '</a>';
//    }
//    if ($page + $page_refers_per_page <= $pages_count) {
//        $pages .= '<a href="' . $type . ($start_page + $page_refers_per_page_count) . '" onClick="Page.Go(this.href); return false">...</a>';
//        $pages .= '<a href="' . $type . $pages_count . '" onClick="Page.Go(this.href); return false">' . $pages_count . '</a>';
//    }
//    $resif = $cnt / $gcount;
//    if (ceil($resif) == $page) $pages .= '';
//    else $pages .= '<a href="' . $type . ($page + 1) . '" onClick="Page.Go(this.href); return false">&raquo;</a>';
//    if ($pages_count <= 1) $pages = '';
//
//    $content = <<<HTML
//<div class="nav" id="nav">{$pages}</div>
//HTML;
//    $tpl->result['content'] .= $content;
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
    $page = (new Request)->int('page', 1);
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
 * @deprecated
 */
function box_navigation($gc, $num, $id, $function, $act)
{
//    global $tpl, $page;
//    $gcount = $gc;
//    $cnt = $num;
//    $items_count = $cnt;
//    $items_per_page = $gcount;
//    $page_refers_per_page = 5;
//    $pages = '';
//    $pages_count = (($items_count % $items_per_page != 0)) ? floor($items_count / $items_per_page) + 1 : floor($items_count / $items_per_page);
//    $start_page = ($page - $page_refers_per_page <= 0) ? 1 : $page - $page_refers_per_page + 1;
//    $page_refers_per_page_count = (($page - $page_refers_per_page < 0) ? $page : $page_refers_per_page) + (($page + $page_refers_per_page > $pages_count) ? ($pages_count - $page) : $page_refers_per_page - 1);
//    if (!$act) $act = "''";
//    else $act = "'{$act}'";
//    if ($page > 1) $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($page - 1) . ', ' . $act . '); return false">&laquo;</a>';
//    else $pages.= '';
//    if ($start_page > 1) {
//        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', 1, ' . $act . '); return false">1</a>';
//        $pages.= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page - 1) . ', ' . $act . '); return false">...</a>';
//    }
//    for ($index = - 1;++$index <= $page_refers_per_page_count - 1;) {
//        if ($index + $start_page == $page) $pages.= '<span>' . ($start_page + $index) . '</span>';
//        else $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $index) . ', ' . $act . '); return false">' . ($start_page + $index) . '</a>';
//    }
//    if ($page + $page_refers_per_page <= $pages_count) {
//        $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . ($start_page + $page_refers_per_page_count) . ', ' . $act . '); return false">...</a>';
//        $pages .= '<a href="" onClick="' . $function . '(' . $id . ', ' . $pages_count . ', ' . $act . '); return false">' . $pages_count . '</a>';
//    }
//    $resif = $cnt / $gcount;
//    if (ceil($resif) == $page) $pages .= '';
//    else $pages .= '<a href="/" onClick="' . $function . '(' . $id . ', ' . ($page + 1) . ', ' . $act . '); return false">&raquo;</a>';
//    if ($pages_count <= 1) $pages = '';
//    $navigation = "<div class=\"nav\" id=\"nav\">{$pages}</div>";
//    $tpl->result['content'] .= $navigation;
}

/**
 * @param $title
 * @param $text
 * @param $tpl_name
 * @return void
 * @deprecated
 */
function msgbox($title, $text, $tpl_name)
{
//    global $tpl;
//    $tpl_2 = new Templates();
//    $config = settings_get();
//    $tpl_2->dir = ROOT_DIR . '/templates/' . $config['temp'];
//    $tpl_2->load_template($tpl_name . '.tpl');
//    $tpl_2->set('{error}', $text);
//    $tpl_2->set('{title}', $title);
//    $tpl_2->compile('info');
//    $tpl_2->clear();
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
//    $tpl->load_template($tpl_name);
//    $tpl->set('{error}', $text);
//    $tpl->set('{title}', $title);
//    $tpl->compile('content');
//    return $tpl->render();
    return 0;
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
    $lang = I18n::getLang();
    $decl_list = require ROOT_DIR . "/lang/{$lang}/declensions.php";
    return (new \FluffyDollop\Support\Declensions($decl_list))->makeWord($num, $type);
}

/**
 * @param $source
 * @return string
 */
function grammaticalName($source): string
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
    $ajax = (new Request)->checkAjax();
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
function OnlineTpl($time, $mobile = false)
{
    global $tpl, $online_time, $lang;
    $config = settings_get();
    $online_time = time() - $config['online_time'];
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
    try {
        $config = require __DIR__ . '/data/config.php';
        Registry::set('config', $config);
        return $config;
    } catch (Error) {
        echo 'Please install system';
        exit();
    }
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
    if ((new Request)->filter('ajax') === 'yes') {
        $metatags['title'] = $metatags['title'] ?? 'Панель управления';
        $result_ajax = array(
            'title' => $metatags['title'],
            'content' => $tpl->result['info'] . $tpl->result['content']
        );
        (new Response)->_e_json($result_ajax);
    } else {
        echo $tpl->result['main'];
    }
}

/**
 * @param string|null $view
 * @param array $variables
 * @return bool
 * @throws ErrorException
 * @throws JsonException|Exception
 */
function view(?string $view, array $variables = []): bool
{
    try {
        echo (new View())->render($view, $variables);
        return true;
    } catch (Error) {
        return false;
    }
}

function view_json(?string $view, array $variables = []): string
{
    try {
        return (new View())->render($view, $variables);
    } catch (Error|Exception) {
        return 'err 500';
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