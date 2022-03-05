<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;
use FluffyDollop\Support\Router;
use FluffyDollop\Support\Templates;

try {
    $config = settings_load();
    Registry::set('config', $config);
} catch (Exception $e) {
    throw new InvalidArgumentException("Invalid config. Please run install.php");
}


$db = require ENGINE_DIR . '/data/db.php';
Registry::set('db', $db);

//FUNC. COOKIES
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

//Смена языка
if (requestFilter('act') == 'chage_lang') {
    $langId = intFilter('id');
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
$useLang = (!empty($_COOKIE['lang'])) > 0 ? (int)$_COOKIE['lang'] : 0;
if ($useLang <= 0) {
    $useLang = 1;
}
$cil = 0;
foreach ($expLangList as $expLangData) {
    $cil++;
    $expLangName = explode(' | ', $expLangData);
    if ($cil == $useLang && $expLangName[0]) {
        $rMyLang = $expLangName[0];
        $checkLang = $expLangName[1];
        Registry::set('rMyLang', $rMyLang);
        Registry::set('checkLang', $checkLang);
    }
}
if (!isset($checkLang)) {
    $rMyLang = 'Русский';
    $checkLang = 'Russian';
}
$lang = include ROOT_DIR . '/lang/' . $checkLang . '/site.php';
Registry::set('lang', $lang);
$langdate = include ROOT_DIR . '/lang/' . $checkLang . '/date.php';

$tpl = new Templates();
$tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];
define('TEMPLATE_DIR', $tpl->dir);

Registry::set('server_time', time());

include_once ENGINE_DIR . '/login.php';

if ($config['offline'] == "yes") {
    include ENGINE_DIR . '/modules/offline.php';
}

if (isset($user_info['user_delet']) && $user_info['user_delet'] > 0) {
    include_once ENGINE_DIR . '/modules/profile_delet.php';
}
$sql_banned = $db->super_query("SELECT * FROM banned", true);
if (isset($sql_banned)) {
    $blockip = check_ip($sql_banned);
} else {
    $blockip = false;
}
if ((isset($user_info['user_ban_date']) && $user_info['user_ban_date'] >= Registry::get('server_time')) ||
    (isset($user_info['user_ban_date']) && $user_info['user_ban_date'] == '0') || $blockip) {
    include_once ENGINE_DIR . '/modules/profile_ban.php';
}
//Если юзер авторизован, то обновляем последнюю дату посещения в таблице друзей и на личной стр
if (Registry::get('logged')) {
    //Начисления 1 убм.
    if (empty($user_info['user_lastupdate'])) {
        $user_info['user_lastupdate'] = 1;
    }
    $server_time = Registry::get('server_time');
    if (date('Y-m-d', $user_info['user_lastupdate']) < date('Y-m-d', $server_time)) {
        $sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
    } else {
        $sql_balance = '';
    }
    //Определяем устройство
    $device_user = isset($check_smartphone) ? 1 : 0;
    if (empty($user_info['user_last_visit']))
        $user_info['user_last_visit'] = $server_time;

    if (($user_info['user_last_visit'] + 60) <= $server_time) {
        $db->query("UPDATE LOW_PRIORITY `users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
    }
}

//Время онлайн
$online_time = Registry::get('server_time') - $config['online_time'];

try {

    $router = Router::fromGlobals();
    $params = [];
    $routers = array(
        '/' => 'Register@main',

        '/register/send' => 'Register@send',
        '/register/rules' => 'Register@rules',
        '/register/step2' => 'Register@step2',
        '/register/step3' => 'Register@step3',
        '/register/activate' => 'Register@activate',
        '/register/finish' => 'Register@finish',
        '/login' => 'Register@login',

        '/u:num' => 'Profile@main',
        '/u:numafter' => 'Profile@main',
        '/public:num' => 'Communities@main',
        //restore
        '/restore' => 'Restore@main',
        '/restore/next' => 'Restore@next',
        '/restore/next/' => 'Restore@next',
        '/restore/send' => 'Restore@send',
        '/restore/prefinish' => 'Restore@preFinish',

        '/wall:num' => 'WallPage@main',
        '/wall:num_:num' => 'WallPage@main',

        '/security/img' => 'Captcha@captcha',
        '/security/code' => 'Captcha@code',
    );
    $router->add($routers);
    try {
        if ($router->isFound()) {
            $router->executeHandler($router::getRequestHandler(), $params);
        } else {
            $go = isset($_GET['go']) ? htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['go']))))) : "main";
            $action = requestFilter('act');
            $class = ucfirst($go);
            if (!class_exists($class) || $action == '' || $class == 'Wall') {
                include_once ENGINE_DIR . '/mod.php';
            } else {
                $controller = new $class();
                $params['params'] = '';
                $params = [$params];
                return call_user_func_array([$controller, $action], $params);
            }
        }
    } catch (Exception) {
        include_once ENGINE_DIR . '/mod.php';
    }
} catch (Exception $e) {
    include_once ENGINE_DIR . '/mod.php';
}
