<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

declare(strict_types=1);

use FluffyDollop\Support\Registry;

$module = isset($_GET['go']) ? htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['go']))))) : 'main';

Registry::set('go', $module);

//FOR MOBILE VERSION 1.0
$config = $config ?? settings_get();
$lang['online'] = $config['temp'] === 'mobile' ? '<img src="/images/monline.gif"  alt="online"/>' : '';

if ($module === 'albums') {
    /** Альбомы */
    if ($config['album_mod'] === 'yes') {
        include ENGINE_DIR . '/modules/albums.php';
    } else {
        $user_speedbar = 'Информация';
        msgbox('', 'Сервис отключен.', 'info');
        compile($tpl);
    }
} elseif ($module === 'videos') {
    /** Видео */
    if ($config['video_mod'] === 'yes') {
        include ENGINE_DIR . '/modules/videos.php';
    } else {
        $user_speedbar = 'Информация';
        msgbox('', 'Сервис отключен.', 'info');
        compile($tpl);
    }
} elseif ($module === 'audio') {
    /** Музыка */
    if ($config['audio_mod'] === 'yes') {
        include ENGINE_DIR . '/modules/audio.php';
    } else {
        $spBar = true;
        $user_speedbar = 'Информация';
        msgbox('', 'Сервис отключен.', 'info');
        compile($tpl);
    }
} elseif ($module === 'happy_friends_block_hide') {
    /** Скрываем блок Дни рожденья друзей */
    $_SESSION['happy_friends_block_hide'] = 1;
} elseif ($module === 'away') {
    /** Редирект */
    $url = (new \FluffyDollop\Http\Request)->filter('url');
    header("Location: {$url}");
} elseif (!include ENGINE_DIR . '/modules/' . $module . '.php') {
    $params = [
        'title' => $lang['no_str_bar'],
        'text' => $lang['no_str_bar'],
    ];
    view('info.info', $params);
}
