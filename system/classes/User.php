<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

class User
{
    public static function getUser($id): array
    {
        return DB::getDB()->row('SELECT user_id, user_real, user_search_pref, user_country_city_name, 
       user_birthday, user_xfields, user_xfields_all, user_city, user_country, user_photo, user_friends_num, 
       user_notes_num, user_subscriptions_num, user_wall_num, user_albums_num, user_last_visit, user_videos_num, 
       user_status, user_privacy, user_sp, user_sex, user_gifts, user_public_num, user_audio, user_delet, 
       user_ban_date, xfields, user_logged_mobile, user_rating FROM `users` WHERE user_id =  ?', $id);
    }
}