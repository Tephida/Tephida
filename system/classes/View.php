<?php

declare(strict_types=1);

namespace Mozg\classes;

use Exception;
use FluffyDollop\Http\Request;
use FluffyDollop\Support\Registry;
use Mozg\modules\Lang;
use Tephida\View\myView;

/**
 * Template engine
 */
class View
{
    public string $title = 'Social network';

    public array $notify = [
        'user_pm_num' => '',
        'new_news' => '',
        'news_link' => '',
        'new_ubm' => '',
        'gifts_link' => '/balance',
        'support' => '',
        'demands' => '',
        'requests_link' => '/requests',
        'new_photos' => '',
        'new_photos_link' => 'newphotos',
        'new_groups_lnk' => '/groups',
        'new_groups' => '',
    ];

    public function __construct()
    {

    }

    /**
     * @param string|null $view
     * @param array $variables
     * @return string
     * @throws Exception
     */
    final public function render(?string $view, array $variables = []): string
    {
        $config = settings_get();
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        //Если юзер перешел по реферальной ссылке, то добавляем реферал_ид в сессию
        if (isset($_GET['reg'])) {
            $_SESSION['ref_id'] = (new Request)->int('reg');
        }
        $variables['title'] = $variables['title'] ?? 'No title';
        $variables['home'] = $config['home'] ?? 'Logo';
        $dictionary = I18n::dictionary();
        $variables['lang'] = $dictionary['lang'];
        $variables['available'] = $variables['available'] ?? false;
        $version = 14;
        $variables['js'] = '<script type="text/javascript" src="/js/jquery.lib.js?v=' . $version . '"></script>
<script type="text/javascript" src="/js/' . I18n::getLang() . '/lang.js?v=' . $version . '"></script>
<script type="text/javascript" src="/js/main.js?v=' . $version . '"></script>
<script type="text/javascript" src="/js/audio.js?v=' . $version . '"></script>
<script type="text/javascript" src="/js/payment.js?v=' . $version . '"></script>
<script type="text/javascript" src="/js/profile.js?v=' . $version . '"></script>';
        $variables['logged'] = Registry::get('logged');
        if (isset($user_info['user_id'])) {
            //Загружаем кол-во новых новостей
            $cache_news = Cache::mozgCache('user_' . $user_info['user_id'] . '/new_news');
            if ($cache_news) {
                $this->notify['new_news'] = "<div class=\"ic_newAct\">{$cache_news}</div>";
                $this->notify['news_link'] = '/notifications';
            }
            /** Загружаем кол-во новых подарков */
            $cache_gift = Cache::mozgCache("user_{$user_info['user_id']}/new_gift");
            if ($cache_gift) {
                $this->notify['new_ubm'] = "<div class=\"ic_newAct\">{$cache_gift}</div>";
                $this->notify['gifts_link'] = "/gifts{$user_info['user_id']}?new=1";
            }

            /** Новые сообщения */
            $user_pm_num = $user_info['user_pm_num'];
            if ($user_pm_num) {
                $this->notify['user_pm_num'] = "<div class=\"ic_newAct\">{$user_pm_num}</div>";
            }

            /** Новые друзья */
            $friends_demands = $user_info['user_friends_demands'];
            if ($friends_demands) {
                $this->notify['demands'] = "<div class=\"ic_newAct\">{$friends_demands}</div>";
                $this->notify['requests_link'] = '/requests';
            }

            /** ТП */
            $user_support = $user_info['user_support'];
            if ($user_support) {
                $this->notify['support'] = "<div class=\"ic_newAct\">{$user_support}</div>";
            }

            /** Отметки на фото */
            if ($user_info['user_new_mark_photos']) {
                $this->notify['new_photos_link'] = 'newphotos';
                $this->notify['new_photos'] = "<div class=\"ic_newAct\">" .
                    $user_info['user_new_mark_photos'] . "</div>";
            } else {
                $this->notify['new_photos_link'] = $user_info['user_id'];
            }

            /** Приглашения в сообщества */
            if ($user_info['invties_pub_num']) {
                $this->notify['new_groups'] = "<div class=\"ic_newAct\">" . $user_info['invties_pub_num'] . "</div>";
                $this->notify['new_groups_lnk'] = '/groups?act=invites';
            }
            if ((new Request)->filter('ajax') !== 'yes') {
                $variables['my_page_link'] = '/u' . $user_info['user_id'];
                $variables['msg'] = $this->notify['user_pm_num'];
                $variables['demands'] = $this->notify['demands'];
                $variables['new_photos'] = $this->notify['new_photos'];
                $variables['groups_link'] = $this->notify['new_groups_lnk'];
                $variables['new_groups'] = $this->notify['new_groups'];
                $variables['news_link'] = $this->notify['news_link'];
                $variables['new_support'] = $this->notify['support'];
                $variables['ubm_link'] = $this->notify['gifts_link'];
                $variables['new_ubm'] = $this->notify['new_ubm'];
                $variables['new_news'] = $this->notify['new_news'];
            }
        }
        $views = ROOT_DIR . '/templates/' . $config['temp'];
        $cache = ENGINE_DIR . '/cache/views';
        /** MODE_DEBUG allows pinpointing troubles. */
        $blade = new myView($views, $cache, \Tephida\View\View::MODE_AUTO);
        $blade::$dictionary = I18n::dictionary();
        if ((new Request)->checkAjax() === true) {
            $json_content = $blade->run($view, $variables);
            $title = $variables['title'] ?? $this->title;
            if (Registry::get('logged')) {
                $result = [
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
                    'content' => $json_content
                ];
            } else {
                $result = [
                    'title' => $title,
                    'content' => $json_content
                ];
            }

            header('Content-Type: application/json');
            $json_data = json_encode($result, JSON_THROW_ON_ERROR);
            return $blade->run('main.json', ['json' => $json_data]);
        }
        header('Access-Control-Allow-Origin: *');
        return $blade->run($view, $variables);
    }
}