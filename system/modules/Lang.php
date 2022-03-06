<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use Mozg\classes\Cookie;
use Mozg\classes\Module;
use Mozg\classes\TpLSite;

class Lang extends Module
{
    public static function getLang(): string
    {
        $lang_list = require ENGINE_DIR . '/data/langs.php';
        $lang_count = (count($lang_list) - 1);

        $lang_Id = isset($_COOKIE['lang']) ? (int)$_COOKIE['lang'] : 0;
        if ($lang_Id > $lang_count) {
            Cookie::append("lang", 0, 365);
            $useLang = 0;
        } elseif ((!empty($lang_Id)) && $lang_Id > 0) {
            $useLang = (int)$_COOKIE['lang'];
        } else {
            $useLang = 0;
        }
        return $lang_list[$useLang]['key'];
    }

    /**
     * Смена языка
     * @return void
     */
    final public function change(): void
    {
        $lang_Id = intFilter('id');
        $lang_list = require ENGINE_DIR . '/data/langs.php';
        $lang_count = (count($lang_list) - 1);
        if ($lang_Id > $lang_count) {
            Cookie::append("lang", 0, 365);
        } elseif ($lang_Id > 0) {
            //Меняем язык
            Cookie::append("lang", $lang_Id, 365);
        } else {
            Cookie::append("lang", 0, 365);
        }
        $langReferer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        header("Location: {$langReferer}");

    }

    final public function main(): void
    {
        $meta_tags['title'] = 'Langs';
        $tpl = new TpLSite($this->tpl_dir_name, $meta_tags);
        $tpl->load_template('lang/main.tpl');
        $user_lang = isset($_COOKIE['lang']) ? (int)$_COOKIE['lang'] : 0;
        $lang_list = require ENGINE_DIR . '/data/langs.php';
        $langs = '';
        foreach ($lang_list as $languages => $value) {
            if ($languages === $user_lang) {
                $langs .= "<div class=\"lang_but lang_selected\">{$value['name']}</div>";
            } else {
                $langs .= "<a href=\"/langs/change?id={$languages}\" style=\"text-decoration:none\"><div class=\"lang_but\">{$value['name']}</div></a>";
            }
        }
        $tpl->set('{langs}', $langs);
        $tpl->compile('content');
        AjaxTpl($tpl);
    }
}