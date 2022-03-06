<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use ErrorException;
use FluffyDollop\Support\Gzip;
use FluffyDollop\Support\Registry;
use FluffyDollop\Support\Templates;
use JsonException;
use Mozg\modules\Lang;

class TpLSite extends Templates
{
    public array $meta_tags = array(
        'title' => 'Social network',
        'generator' => '<meta name="generator" content="VII ENGINE" />',
        'charset' => '<meta http-equiv="content-type" content="text/html; charset=utf-8" />',
    );

    public array $notify = array(
        'user_pm_num' => '',
        'new_news' => '',
        'news_link' => '',
        'new_ubm' => '',
        'gifts_link' => '/balance',
        'support' => '',
        'demands' => '',
        'requests_link' => '/requests',
        'new_photos' => '',
        'new_photos_link' => '',
        'new_groups_lnk' => '/groups',
        'new_groups' => '',
    );

    public function __construct(string $dir = '.', array $meta_tags = array())
    {
        $this->dir = $dir;
        if (!empty($meta_tags['title'])) {
            $this->meta_tags['title'] = $meta_tags['title'];
        }
        if (!defined('TEMPLATE_DIR')) {
            define('TEMPLATE_DIR', $dir);
        }
    }

    /**
     * @return int
     * @throws ErrorException
     * @throws \JsonException
     */
    final public function render(): int
    {
        $config = settings_get();
        $params = array();
        $metatags['title'] = $params['metatags']['title'] ?? $config['home'];
//        $lang = require ROOT_DIR . '/lang/' . getLangNew() . '/site.php';
//        $params['speedbar'] = $user_speedbar ?? $lang['welcome'];
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
            $CacheNews = mozg_cache('user_' . $user_info['user_id'] . '/new_news');
            if ($CacheNews) {
                $this->notify['new_news'] = "<div class=\"headm_newac\" style=\"margin-left:18px\">{$CacheNews}</div>";
                $this->notify['news_link'] = '/notifications';
            }
            /** Загружаем кол-во новых подарков */
            $CacheGift = mozg_cache("user_{$user_info['user_id']}/new_gift");
            if ($CacheGift) {
                $this->notify['new_ubm'] = "<div class=\"headm_newac\" style=\"margin-left:20px\">{$CacheGift}</div>";
                $this->notify['gifts_link'] = "/gifts{$user_info['user_id']}?new=1";
            }

            /** Новые сообщения */
            $user_pm_num = $user_info['user_pm_num'];
            if ($user_pm_num) {
                $this->notify['user_pm_num'] = "<div class=\"headm_newac\" style=\"margin-left:37px\">{$user_pm_num}</div>";
            }

            /** Новые друзья */
            $user_friends_demands = $user_info['user_friends_demands'];
            if ($user_friends_demands) {
                $this->notify['demands'] = "<div class=\"headm_newac\">{$user_friends_demands}</div>";
                $this->notify['requests_link'] = '/requests';
            }

            /** ТП */
            $user_support = $user_info['user_support'];
            if ($user_support) {
                $this->notify['support'] = "<div class=\"headm_newac\" style=\"margin-left:26px\">{$user_support}</div>";
            }

            /** Отметки на фото */
            if ($user_info['user_new_mark_photos']) {
                $this->notify['new_photos_link'] = 'newphotos';
                $this->notify['new_photos'] = "<div class=\"headm_newac\" style=\"margin-left:22px\">" . $user_info['user_new_mark_photos'] . "</div>";
            } else {
                $this->notify['new_photos_link'] = $user_info['user_id'];
            }

            /** Приглашения в сообщества */
            if ($user_info['invties_pub_num']) {
                $this->notify['new_groups'] = "<div class=\"headm_newac\" style=\"margin-left:26px\">" . $user_info['invties_pub_num'] . "</div>";
                $this->notify['new_groups_lnk'] = '/groups?act=invites';
            } else {
                $this->notify['new_groups_lnk'] = '/groups';
            }
        }

        if (requestFilter('ajax') === 'yes') {
            return $this->compileAjax();
        }

        return $this->compileNoAjax();
    }

    /**
     * @return int
     * @throws JsonException
     */
    private function compileAjax(): int
    {
        $config = settings_get();

        //FIXME
//    if ($_SERVER['REQUEST_METHOD'] == 'POST')
//        throw new Exception('Неизвестная ошибка');

        $speedbar = $spBar = null;//FIXME

//        $metatags['title'] = $metatags['title'] ?? $config['home'];
        $title = $this->meta_tags['title'];//TODO default to config

//    if (isset($spBar) and $spBar)
//        $ajaxSpBar = "$('#speedbar').show().html('{$speedbar}')";
//    else
//        $ajaxSpBar = "$('#speedbar').hide()";

        $this->result['info'] = $this->result['info'] ?? '';
        if (Registry::get('logged')) {
            $result_ajax = array(
                'title' => $title,
                'user_pm_num' => $this->notify['user_pm_num'],
                'new_news' => $this->notify['new_news'],
                'new_ubm' => $this->notify['new_ubm'],
                'gifts_link' => $this->notify['gifts_link'],
                'support' => $this->notify['support'],
                'news_link' => $this->notify['news_link'],
                'demands' => $this->notify['demands'],
                'new_photos' => $this->notify['new_photos'],
                'new_photos_link' => $this->notify['new_photos_link'],
                'requests_link' => $this->notify['requests_link'],
                'new_groups' => $this->notify['new_groups'],
                'new_groups_lnk' => $this->notify['new_groups_lnk'],
                'content' => $this->result['info'] . $this->result['content']
            );

        } else {
            $result_ajax = array(
                'title' => $title,
                'content' => $this->result['info'] . $this->result['content']
            );
        }
        $res = str_replace('{theme}', '/templates/' . $config['temp'], $result_ajax);

        _e_json($res);
        return $this->renderEnd();
    }

    /**
     *
     * delete if $this->notify todo
     * @return int
     * @throws JsonException|ErrorException
     */
    private function compileNoAjax(): int
    {
        $this->load_template('main.tpl');
//Если юзер авторизован
        if (Registry::get('logged')) {
            $user_info = Registry::get('user_info');
            $this->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "");
            $this->set('[logged]', '');
            $this->set('[/logged]', '');
            $this->set('{my-page-link}', '/u' . $user_info['user_id']);
            $this->set('{my-id}', $user_info['user_id']);
            //Заявки в друзья
            $this->set('{demands}', $this->notify['demands']);
            $this->set('{requests-link}', $this->notify['requests_link']);

            //Новости
            $this->set('{new-news}', $this->notify['new_news']);
            $this->set('{news-link}', $this->notify['news_link']);

            //Сообщения
            $this->set('{msg}', $this->notify['user_pm_num']);

            //Поддержка
            $this->set('{new-support}', $this->notify['support']);

            //Отметки на фото
            if ($user_info['user_new_mark_photos']) {
                $this->set('{my-id}', 'newphotos');
            } else {
                $this->set('{my-id}', '');
            }
            $this->set('{new_photos}', $this->notify['new_photos']);

            //UBM
            $this->set('{new-ubm}', $this->notify['new_ubm']);

            $this->set('{ubm-link}', $this->notify['gifts_link']);

            //Приглашения в сообщества
            $this->set('{new_groups}', $this->notify['new_groups']);
            $this->set('{groups-link}', $this->notify['new_groups_lnk']);

            if ($user_info['user_photo']) {
                $config = settings_get();
                $ava = '<img src="' . $config['home_url'] . 'uploads/users/' . $user_info['user_id'] . '/100_' . $user_info['user_photo'] . '"   style="width: 40px;height: 40px;" />';
            } else {
                $ava = '<img src="/images/no_ava_50.png" />';
            }
            $this->set('{user_photo}', $ava);

        } else {
            $this->set_block("'\\[logged\\](.*?)\\[/logged\\]'si", "");
            $this->set('[not-logged]', '');
            $this->set('[/not-logged]', '');
            $this->set('{my-page-link}', '');
        }
        $mobile_speedbar = '';//fixme
        $this->meta_tags['title'] = '<title>' . $this->meta_tags['title'] . '</title>';
        $headers = implode('', $this->meta_tags);

//        $speedbar = '';//fixme

        $this->set('{header}', $headers);
//        $this->set('{speedbar}', $speedbar);

        $this->set('{mobile-speedbar}', $mobile_speedbar);
        $this->set('{info}', $this->result['info'] ?? '');
// FOR MOBILE VERSION 1.0
        $config = settings_get();
        if ($config['temp'] === 'mobile') {
            $this->result['content'] = str_replace('onClick="Page.Go(this.href); return false"', '', $this->result['content']);
            if ($user_info['user_status']) {
                $this->set('{status-mobile}', '<span style="font-size:11px;color:#000">' . $user_info['user_status'] . '</span>');
            } else {
                $this->set('{status-mobile}', '<span style="font-size:11px;color:#999">установить статус</span>');
            }

            $user_friends_demands = $user_info['user_friends_demands'] ?? 0;
            $user_support = $user_info['user_support'] ?? 0;
            $CacheNews = mozg_cache('user_' . $user_info['user_id'] . '/new_news') ?? 0;
            $CacheGift = mozg_cache("user_{$user_info['user_id']}/new_gift") ?? 0;

            $new_actions = $user_friends_demands + $user_support + $CacheNews + $CacheGift + $user_info['user_pm_num'];
            if ($new_actions) {
                $this->set('{new-actions}', "<div class=\"headm_newac\" style=\"margin-top:5px;margin-left:30px\">+{$new_actions}</div>");
            } else {
                $this->set('{new-actions}', "");
            }
        }
        $this->set('{content}', $this->result['content']);


        if (isset($spBar) && $spBar) {
            $this->set_block("'\\[speedbar\\](.*?)\\[/speedbar\\]'si", "");
        } else {
            $this->set('[speedbar]', '');
            $this->set('[/speedbar]', '');
        }
//BUILD JS
        $this->set('{js}', '<script type="text/javascript" src="{theme}/js/jquery.lib.js"></script>
<script type="text/javascript" src="{theme}/js/' . Lang::getLang() . '/lang.js"></script>
<script type="text/javascript" src="{theme}/js/main.js"></script>
<script type="text/javascript" src="{theme}/js/profile.js"></script>');

// FOR MOBILE VERSION 1.0
        if (isset($user_info['user_photo']) && $user_info['user_photo']) {
            $this->set('{my-ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
        } else {
            $this->set('{my-ava}', "{theme}/images/no_ava_50.png");
        }

        if (isset($user_info['user_search_pref'])) {
            $this->set('{my-name}', $user_info['user_search_pref']);
        } else {
            $this->set('{my-name}', '');
        }

        if (isset($check_smartphone) && $check_smartphone) {
            $this->set('{mobile-link}', '<a href="/index.php?act=change_mobile">мобильная версия</a>');
        } else {
            $this->set('{mobile-link}', '');
        }

        $this->set('{lang}', getLangName());
        $this->compile('main');
        header('Content-type: text/html; charset=utf-8');
        $result = str_replace('{theme}', '/templates/' . $config['temp'], $this->result['main']);
        print $result;
        return $this->renderEnd();
    }

    private function renderEnd(): int
    {
        $config = settings_get();
        $this->global_clear();
//    $db->close();
        if ($config['gzip'] === 'yes') {
            (new Gzip(false))->GzipOut();
        }
        return 1;
    }

    public function renderAjax(): string
    {
        $config = settings_get();
        return print(str_replace('{theme}', '/templates/' . $config['temp'], $this->result['info'] . $this->result['content']));
    }
}