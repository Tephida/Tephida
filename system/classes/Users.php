<?php
declare(strict_types=1);

namespace Mozg\classes;

use FluffyDollop\Support\Registry;
use JetBrains\PhpStorm\ArrayShape;

/**
 *
 */
class Users
{
    /**
     * Get user data from login
     * @param int $id
     * @return array
     */
    #[ArrayShape([
        'user_id' => 'int',
        'user_email' => 'string',
        'user_group' => 'int',
        'user_friends_demands ' => 'int',
        'user_pm_num ' => 'int',
        'user_support ' => 'int',
        'user_lastupdate ' => 'int',
        'user_photo ' => 'string',
        'user_msg_type ' => 'int',
        'user_delet ' => 'int',
        'user_ban_date ' => 'int',
        'user_new_mark_photos ' => 'int',
        'user_search_pref ' => 'string',
        'user_status ' => 'string',
        'user_last_visit ' => 'int',
        'user_hid ' => 'string',
        'invties_pub_num ' => 'int',
        'user_password ' => 'string'
    ])]
    public static function profile(int $id): array
    {
        return (Registry::get('db'))->super_query("SELECT user_id, user_email, user_group, user_friends_demands, 
       user_pm_num, user_support, user_lastupdate, user_photo, user_msg_type, user_delet, user_ban_date, 
       user_new_mark_photos, user_search_pref, user_status, user_last_visit, invties_pub_num, user_hid, user_password
        FROM `users` WHERE user_id = '{$id}'");
    }

    /**
     * @param int $id
     * @return array
     */
    #[ArrayShape([
        'user_id' => 'int',
        'user_email' => 'string',
        'user_group' => 'int',
        'user_hid ' => 'string',
        'user_password ' => 'string'
    ])]
    public static function admin(int $id): array
    {
        return (Registry::get('db'))->super_query("SELECT user_id, user_email, user_group, user_hid, user_password 
        FROM `users` WHERE user_id = '{$id}' AND user_group = '1'");
    }

    /**
     * @param int $id
     * @param string|false $type
     * @return array
     */
    public static function login(int $id , string|false $type): array
    {
        if ($type === 'site') {
            $user_info = self::profile($id);
            $user_info['user_id'] = (int)$user_info['user_id'];
            $user_info['user_group'] = (int)$user_info['user_group'];
            $user_info['user_lastupdate'] = (int)$user_info['user_lastupdate'];
            $user_info['user_delet'] = (int)$user_info['user_delet'];
            $user_info['user_ban_date'] = (int)$user_info['user_ban_date'];
            $user_info['user_last_visit'] = (int)$user_info['user_last_visit'];
            $user_info['invties_pub_num'] = (int)$user_info['invties_pub_num'];
            return $user_info;
        }
        if ($type === 'control_panel') {
            $user_info = self::admin($id);
            $user_info['user_id'] = (int)$user_info['user_id'];
            $user_info['user_group'] = (int)$user_info['user_group'];
            return $user_info;
        }
        return [];
    }
}