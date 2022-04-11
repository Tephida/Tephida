<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use Mozg\Security\AntiSpam;

class Flood extends AntiSpam
{
    /** @var int Лимиты новых друзей на день */
    private static int $max_friends = 40;
    /** @var int Максимум сообщений не друзьям */
    private static int $max_msg = 40;
    /** @var int Максимум записей на стену */
    private static int $max_wall = 10;
    /** @var int Максимум одинаковых текстовых данных */
    private static int $max_identical = 10;
    /** @var int Максимум комментариев к записям на стенах людей и сообществ */
    private static int $max_comm = 100;
    /** @var int Максимум сообществ за день */
    private static int $max_groups = 5;

    /** @var int Максимум альбомов за день */
    private static int $max_albums = 5;
    private static int $max_album_photos = 40;
    private static int $max_music = 5;
    private static int $max_doc = 5;
    private static int $max_group_forum = 5;
    private static int $max_group_forum_msg = 40;
    private static int $max_notes = 5;
    private static int $max_videos = 5;
    private static int $max_support = 1;
}