<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (version_compare(PHP_VERSION, '8.0.0') < 0){
    echo '<div style="color: red">Error 500</div>';
}
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}
@session_start();
@ob_start();
@ob_implicit_flush(0);
const MOZG = true;
define("ROOT_DIR", dirname(__FILE__));
const ENGINE_DIR = ROOT_DIR . '/system';
header('Content-type: text/html; charset=utf-8');
//AJAX
$ajax = !empty($_POST['ajax']) ? $_POST['ajax'] : null;
$logged = false;
$user_info = false;
include ENGINE_DIR . '/init.php';
//Если юзер перешел по реф ссылке, то добавляем ид реферала в сессию
if ( isset($_GET['reg']) ) $_SESSION['ref_id'] = intval($_GET['reg']);

if (isset($user_info['user_id'])){
    //Загружаем кол-во новых новостей
    $CacheNews = mozg_cache('user_' . $user_info['user_id'] . '/new_news');
    if ($CacheNews) {
        $new_news = "<div class=\"headm_newac\" style=\"margin-left:18px\">{$CacheNews}</div>";
        $news_link = '/notifications';
    }else{
        $new_news = '';
        $news_link = '';
    }
//Загружаем кол-во новых подарков
    $CacheGift = mozg_cache("user_{$user_info['user_id']}/new_gift");
    if ($CacheGift) {
        $new_ubm = "<div class=\"headm_newac\" style=\"margin-left:20px\">{$CacheGift}</div>";
        $gifts_link = "/gifts{$user_info['user_id']}?new=1";
    } else {
        $new_ubm = '';
        $gifts_link = '/balance';
    }
//Новые сообщения
    $user_pm_num = $user_info['user_pm_num'];
    if ($user_pm_num) $user_pm_num = "<div class=\"headm_newac\" style=\"margin-left:37px\">{$user_pm_num}</div>";
    else $user_pm_num = '';
//Новые друзья
    $user_friends_demands = $user_info['user_friends_demands'];
    if ($user_friends_demands) {
        $demands = "<div class=\"headm_newac\">{$user_friends_demands}</div>";
        $requests_link = '/requests';
    } else $demands = '';
//ТП
    $user_support = $user_info['user_support'];
    if ($user_support) $support = "<div class=\"headm_newac\" style=\"margin-left:26px\">{$user_support}</div>";
    else $support = '';
//Отметки на фото
    if ($user_info['user_new_mark_photos']) {
        $new_photos_link = 'newphotos';
        $new_photos = "<div class=\"headm_newac\" style=\"margin-left:22px\">" . $user_info['user_new_mark_photos'] . "</div>";
    } else {
        $new_photos = '';
        $new_photos_link = $user_info['user_id'];
    }
//Приглашения в сообщества
    if ($user_info['invties_pub_num']) {
        $new_groups = "<div class=\"headm_newac\" style=\"margin-left:26px\">" . $user_info['invties_pub_num'] . "</div>";
        $new_groups_lnk = '/groups?act=invites';
    } else {
        $new_groups = '';
        $new_groups_lnk = '/groups';
    }

}else{
    $user_pm_num = '';
    $new_news = '';
    $new_ubm = '';
    $gifts_link = '/balance';
    $support = '';
    $news_link = '';
    $demands = '';
    $new_photos = '';
    $new_photos_link = 0;
    $requests_link = '/requests';
    $new_groups_lnk = '/groups';
    $new_groups = '';

}

//Если включен AJAX, то загружаем стр.
if (!empty($_POST['ajax']) AND $_POST['ajax'] == 'yes') {
    //Если есть POST Запрос и значение AJAX, а $ajax не равняется "yes", то не пропускаем
    if ($_SERVER['REQUEST_METHOD'] == 'POST' and $ajax != 'yes') die('Неизвестная ошибка');
    if (isset($spBar) AND $spBar)
        $ajaxSpBar = "$('#speedbar').show().html('{$speedbar}')";
    else
        $ajaxSpBar = "$('#speedbar').hide()";

    $requests_link = $requests_link ?? '';

    $result_ajax = <<<HTML
<script type="text/javascript">
document.title = '{$metatags['title']}';
{$ajaxSpBar};
document.getElementById('new_msg').innerHTML = '{$user_pm_num}';
document.getElementById('new_news').innerHTML = '{$new_news}';
document.getElementById('new_ubm').innerHTML = '{$new_ubm}';
document.getElementById('ubm_link').setAttribute('href', '{$gifts_link}');
document.getElementById('new_support').innerHTML = '{$support}';
document.getElementById('news_link').setAttribute('href', '/news{$news_link}');
document.getElementById('new_requests').innerHTML = '{$demands}';
document.getElementById('new_photos').innerHTML = '{$new_photos}';
document.getElementById('requests_link_new_photos').setAttribute('href', '/albums/{$new_photos_link}');
document.getElementById('requests_link').setAttribute('href', '/friends{$requests_link}');
$('#new_groups').html('{$new_groups}');
$('#new_groups_lnk').attr('href', '{$new_groups_lnk}');
</script>
{$tpl->result['info']}{$tpl->result['content']}
HTML;
    echo str_replace('{theme}', '/templates/' . $config['temp'], $result_ajax);
    $tpl->global_clear();
    $db->close();
    if ($config['gzip'] == 'yes') GzipOut();
    die();
}
//Если обращение к модулю регистрации или главной и юзер не авторизован то показываем регистрацию
if ($go == 'register' or $go == 'main' and !$logged) include ENGINE_DIR . '/modules/register_main.php';
$tpl->load_template('main.tpl');
//Если юзер залогинен
if ($logged) {
    $tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "");
    $tpl->set('[logged]', '');
    $tpl->set('[/logged]', '');
    $tpl->set('{my-page-link}', '/u' . $user_info['user_id']);
    $tpl->set('{my-id}', $user_info['user_id']);
    //Заявки в друзья
    $user_friends_demands = $user_info['user_friends_demands'];
    if ($user_friends_demands) {
        $tpl->set('{demands}', $demands);
        $tpl->set('{requests-link}', $requests_link);
    } else {
        $tpl->set('{demands}', '');
        $tpl->set('{requests-link}', '');
    }
    //Новости
    if ($CacheNews) {
        $tpl->set('{new-news}', $new_news);
        $tpl->set('{news-link}', $news_link);
    } else {
        $tpl->set('{new-news}', '');
        $tpl->set('{news-link}', '');
    }
    //Сообщения
    if ($user_pm_num) $tpl->set('{msg}', $user_pm_num);
    else $tpl->set('{msg}', '');
    //Поддержка
    if ($user_support) $tpl->set('{new-support}', $support);
    else $tpl->set('{new-support}', '');
    //Отметки на фото
    if ($user_info['user_new_mark_photos']) {
        $tpl->set('{my-id}', 'newphotos');
        $tpl->set('{new_photos}', $new_photos);
    } else $tpl->set('{new_photos}', '');
    //UBM
    if ($CacheGift) {
        $tpl->set('{new-ubm}', $new_ubm);
        $tpl->set('{ubm-link}', $gifts_link);
    } else {
        $tpl->set('{new-ubm}', '');
        $tpl->set('{ubm-link}', $gifts_link);
    }
    //Приглашения в сообщества
    if ($user_info['invties_pub_num']) {
        $tpl->set('{groups-link}', $new_groups_lnk);
        $tpl->set('{new_groups}', $new_groups);
    } else {
        $tpl->set('{groups-link}', $new_groups_lnk);
        $tpl->set('{new_groups}', '');
    }
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
$tpl->set('{info}', $tpl->result['info']);
// FOR MOBILE VERSION 1.0
if ($config['temp'] == 'mobile') {
    $tpl->result['content'] = str_replace('onClick="Page.Go(this.href); return false"', '', $tpl->result['content']);
    if ($user_info['user_status']) $tpl->set('{status-mobile}', '<span style="font-size:11px;color:#000">' . $user_info['user_status'] . '</span>');
    else $tpl->set('{status-mobile}', '<span style="font-size:11px;color:#999">установить статус</span>');
    $new_actions = $user_friends_demands + $user_support + $CacheNews + $CacheGift + $user_info['user_pm_num'];
    if ($new_actions) $tpl->set('{new-actions}', "<div class=\"headm_newac\" style=\"margin-top:5px;margin-left:30px\">+{$new_actions}</div>");
    else $tpl->set('{new-actions}', "");
}
$tpl->set('{content}', $tpl->result['content']);
if (isset($spBar) AND $spBar)
    $tpl->set_block("'\\[speedbar\\](.*?)\\[/speedbar\\]'si", "");
else {
    $tpl->set('[speedbar]', '');
    $tpl->set('[/speedbar]', '');
}
//BUILD JS
if ($logged) $tpl->set('{js}', '<script type="text/javascript" src="{theme}/js/jquery.lib.js"></script>
<script type="text/javascript" src="{theme}/js/' . $checkLang . '/lang.js"></script>
<script type="text/javascript" src="{theme}/js/main.js"></script>
<script type="text/javascript" src="{theme}/js/profile.js"></script>');
else $tpl->set('{js}', '<script type="text/javascript" src="{theme}/js/jquery.lib.js"></script>
<script type="text/javascript" src="{theme}/js/' . $checkLang . '/lang.js"></script>
<script type="text/javascript" src="{theme}/js/main.js"></script>');

// FOR MOBILE VERSION 1.0
if (isset($user_info['user_photo']) AND $user_info['user_photo']){
    $tpl->set('{my-ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
}else
    $tpl->set('{my-ava}', "{theme}/images/no_ava_50.png");

if (isset($user_info['user_search_pref'])){
    $tpl->set('{my-name}', $user_info['user_search_pref']);
}else{
    $tpl->set('{my-name}', '');
}

if (isset($check_smartphone))
    $tpl->set('{mobile-link}', '<a href="/index.php?act=change_mobile">мобильная версия</a>');
else
    $tpl->set('{mobile-link}', '');
$tpl->set('{lang}', $rMyLang);
$tpl->compile('main');
echo str_replace('{theme}', '/templates/' . $config['temp'], $tpl->result['main']);
$tpl->global_clear();
$db->close();
if ($config['gzip'] == 'yes')
    GzipOut();
