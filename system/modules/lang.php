<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

NoAjaxQuery();

$tpl->load_template('lang/main.tpl');

$user_lang = isset($_COOKIE['lang']) ? (int)$_COOKIE['lang'] : 0;
$lang_list = require ENGINE_DIR . '/data/langs.php';
$langs = '';
foreach ($lang_list as $languages => $value) {
    if ($languages == $user_lang) {
        $langs .= "<div class=\"lang_but lang_selected\">{$value['name']}</div>";
    } else {
        $langs .= "<a href=\"/index.php?act=chage_lang&id={$languages}\" style=\"text-decoration:none\"><div class=\"lang_but\">{$value['name']}</div></a>";
    }
}
$tpl->set('{langs}', $langs);
$tpl->compile('content');
AjaxTpl($tpl);