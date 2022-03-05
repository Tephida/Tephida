<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Filesystem;
use FluffyDollop\Support\Registry;
use FluffyDollop\Support\Templates;

$domain_cookie = explode(".", clean_url($_SERVER['HTTP_HOST']));
$domain_cookie_count = count($domain_cookie);
$domain_allow_count = -2;

if ($domain_cookie_count > 2) {

    if (in_array($domain_cookie[$domain_cookie_count - 2], array('com', 'net', 'org'))) {
        $domain_allow_count = -3;
    }

    if ($domain_cookie[$domain_cookie_count - 1] == 'ua') {
        $domain_allow_count = -3;
    }

    $domain_cookie = array_slice($domain_cookie, $domain_allow_count);
}

$domain_cookie = ".".implode(".", $domain_cookie);

define('DOMAIN', $domain_cookie);


function system_mozg_clear_cache_file($prefix): void
{
    Filesystem::delete(ENGINE_DIR . '/cache/system/' . $prefix . '.php');
}

/**
 * @throws JsonException
 */
function compileAdmin($tpl): void
{
    $tpl->load_template('main.tpl');
    $config = settings_load();
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

function initAdminTpl(): Templates
{
    $tpl = new Templates();
    $tpl->dir = ADMIN_DIR . '/tpl/';
    define('TEMPLATE_DIR', $tpl->dir);
    return $tpl;
}
