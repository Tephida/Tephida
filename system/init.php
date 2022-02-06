<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG')) die('Hacking attempt!');
@include ENGINE_DIR . '/data/config.php';

if (!isset($config['home_url']))
    die("Vii Engine not installed. Please run install.php");
include ENGINE_DIR . '/classes/mysql.php';
include ENGINE_DIR . '/data/db.php';

function normalize_name(string $value, bool $part = true): array|null|string
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

function clearfilepath($file, $ext = array()): string
{

    $file = trim(str_replace(chr(0), '', (string)$file));
    $file = str_replace(array('/', '\\'), '/', $file);

    $path_parts = pathinfo($file);

    if (count($ext)) {
        if (!in_array($path_parts['extension'], $ext)) return '';
    }

    $filename = normalize_name($path_parts['basename'], true);

    if (!$filename) return '';

    $parts = array_filter(explode('/', $path_parts['dirname']), 'strlen');

    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = normalize_name($part, false);
        }
    }

    $path = implode('/', $absolutes);

    if ($path)
        return implode('/', $absolutes) . '/' . $filename;
    else
        return '';

}

function cleanpath($path): string
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

include ENGINE_DIR . '/classes/templates.php';
if ($config['gzip'] == 'yes') include ENGINE_DIR . '/modules/gzip.php';
//FUNC. COOKIES
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

$domain_cookie = explode(".", clean_url($_SERVER['HTTP_HOST']));
$domain_cookie_count = count($domain_cookie);
$domain_allow_count = -2;
if ($domain_cookie_count > 2) {
    if (in_array($domain_cookie[$domain_cookie_count - 2], array('com', 'net', 'org'))) $domain_allow_count = -3;
    if ($domain_cookie[$domain_cookie_count - 1] == 'ua') $domain_allow_count = -3;
    $domain_cookie = array_slice($domain_cookie, $domain_allow_count);
}
$domain_cookie = "." . implode(".", $domain_cookie);
define('DOMAIN', $domain_cookie);
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

//Смена языка
if (!empty($_GET['act']) and $_GET['act'] == 'chage_lang') {
    $langId = intval($_GET['id']);
    $config['lang_list'] = nl2br($config['lang_list']);
    $expLangList = explode('<br />', $config['lang_list']);
    $numLangs = count($expLangList);
    if ($langId > 0 and $langId <= $numLangs) {
        //Меняем язык
        set_cookie("lang", $langId, 365);
    }
    $langReferer = $_SERVER['HTTP_REFERER'];
    header("Location: {$langReferer}");
}
//lang
$config['lang_list'] = nl2br($config['lang_list']);
$expLangList = explode('<br />', $config['lang_list']);
$numLangs = count($expLangList);
$useLang = !empty($_COOKIE['lang']) > 0 ? intval($_COOKIE['lang']) : 0;
if ($useLang <= 0)
    $useLang = 1;
$cil = 0;
foreach ($expLangList as $expLangData) {
    $cil++;
    $expLangName = explode(' | ', $expLangData);
    if ($cil == $useLang and $expLangName[0]) {
        $rMyLang = $expLangName[0];
        $checkLang = $expLangName[1];
    }
}
if (!$checkLang) {
    $rMyLang = 'Русский';
    $checkLang = 'Russian';
}
include ROOT_DIR . '/lang/' . $checkLang . '/site.lng';
include ENGINE_DIR . '/functions.php';
$tpl = new mozg_template;
$tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];
define('TEMPLATE_DIR', $tpl->dir);
$_DOCUMENT_DATE = false;
$server_time = time();

include ENGINE_DIR . '/modules/login.php';

if ($config['offline'] == "yes") include ENGINE_DIR . '/modules/offline.php';

if (isset($user_info['user_delet']) and $user_info['user_delet'] > 0)
    include ENGINE_DIR . '/modules/profile_delet.php';
$sql_banned = $db->super_query("SELECT * FROM " . PREFIX . "_banned", true, "banned", true);
if (isset($sql_banned))
    $blockip = check_ip($sql_banned);
else
    $blockip = false;
if (isset($user_info['user_ban_date']) and $user_info['user_ban_date'] >= $server_time or isset($user_info['user_ban_date']) and $user_info['user_ban_date'] == '0' or $blockip)
    include ENGINE_DIR . '/modules/profile_ban.php';
//Елси юзер залогинен то обновляем последнюю дату посещения в таблице друзей и на личной стр
if (isset($logged)) {
    //Начисления 1 убм.
    if (empty($user_info['user_lastupdate'])) {
        $user_info['user_lastupdate'] = 1;
    }

    if (date('Y-m-d', $user_info['user_lastupdate']) < date('Y-m-d', $server_time)) {
        $sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
    } else {
        $sql_balance = '';
    }
    //Определяем устройство
    $device_user = isset($check_smartphone) ? 1 : 0;
//    echo $user_info['user_last_visit'];
    if (empty($user_info['user_last_visit']))
        $user_info['user_last_visit'] = $server_time;

    if (($user_info['user_last_visit'] + 60) <= $server_time) {
        $db->query("UPDATE LOW_PRIORITY `" . PREFIX . "_users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
    }
}

//Время онлайна
$online_time = $server_time - $config['online_time'];
include ENGINE_DIR . '/mod.php';
