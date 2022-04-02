<?php
declare(strict_types=1);

namespace Mozg\classes;

use FluffyDollop\Support\Registry;

/**
 * Friends tools
 */
class Friends
{
    /**
     * @param $userId
     * @return bool
     */
    public static function checkBlackList(int $for_user_id): bool
    {
        /** @var array $user_info */
        $user_info = Registry::get('user_info');
        $user_id = $user_info['user_id'];
        $open_my_list = Cache::mozgCache("user_{$for_user_id}/blacklist");
        if (!$open_my_list){
            $db = Registry::get('db');
            /** @var array $row */
            $row = $db->super_query("SELECT user_blacklist FROM `users` WHERE user_id = '{$for_user_id}'");
            $open_my_list = $row['user_blacklist'];
        }
        return stripos($open_my_list, "|{$user_id}|") !== false;
    }
}