<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Mozg\modules;

use FluffyDollop\Http\Request;
use Mozg\classes\I18n;
use Mozg\classes\Module;
use FluffyDollop\Support\Cookie;

class Lang extends Module
{
    public const EN = '1';
    public const RU = '2';

    /**
     * Смена языка
     * @return void
     */
    final public function change(): void
    {
        $lang_Id = (new Request)->int('id', 1);
        $lang_list = I18n::langList();
        $lang_count = \count($lang_list);
        if ($lang_Id > $lang_count) {
            Cookie::append('lang', self::EN, 365);
        } else{
            Cookie::append('lang', (string)$lang_Id, 365);
        }
        $lang_referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        header("Location: {$lang_referer}");

    }

    /**
     * language box
     * @throws \ErrorException|\JsonException
     */
    final public function main(): void
    {
        $user_lang = isset($_COOKIE['lang']) ? (int)$_COOKIE['lang'] : 0;
        $lang_list = I18n::langList();
        $params = [
            'title' => 'Langs',//todo
            'user_lang' => $user_lang,
            'lang_list' => $lang_list,
        ];
        view('lang.lang', $params);
    }
}