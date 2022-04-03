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

use Mozg\classes\Module;
use Sinergi\BrowserDetector\Language;
use FluffyDollop\Support\Cookie;

class Lang extends Module
{
    public const EN = '1';
    public const RU = '2';

    /**
     * get lang number
     * @return string
     */
    public static function getLang(): string
    {
        $lang_list = self::langList();
        $lang_count = \count($lang_list);
        $lang_Id = (int)(Cookie::get('lang'));
        if ($lang_Id > $lang_count) {
            Cookie::append('lang', self::EN, 365);
            $use_lang = self::EN;
        } elseif (!empty($lang_Id)) {
            $use_lang = $lang_Id;
        } else {
            $language = new Language();
            if ($language->getLanguage() === 'en') {
                Cookie::append('lang', self::EN, 365);
                $use_lang = self::EN;
            }elseif($language->getLanguage() === 'ru'){
                Cookie::append('lang', self::RU, 365);
                $use_lang = self::RU;
            }else{
                Cookie::append('lang', self::EN, 365);
                $use_lang = self::EN;
            }
        }
        return $lang_list[$use_lang]['key'];
    }

    /**
     * Language dictionary
     * @return array dictionary list
     */
    public static function dictionary(): array
    {
        return require ROOT_DIR . '/lang/'.self::getLang().'/site.php';
    }

    /**
     * languages list
     * @return array
     */
    public static function langList(): array
    {
        return require ENGINE_DIR . '/data/langs.php';
    }

    /**
     * Смена языка
     * @return void
     */
    final public function change(): void
    {
        $lang_Id = intFilter('id', 1);
        $lang_list = self::langList();
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
        $lang_list = self::langList();
        $params = [
            'title' => 'Langs',//todo
            'user_lang' => $user_lang,
            'lang_list' => $lang_list,
        ];
        view('lang.lang', $params);
    }
}