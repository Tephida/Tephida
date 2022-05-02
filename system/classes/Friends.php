<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

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
            /** @var array $row */
            $row = DB::getDB()->row('SELECT user_blacklist FROM `users` WHERE user_id =  ?', $for_user_id);
            $open_my_list = $row['user_blacklist'];
        }
        return stripos($open_my_list, "|{$user_id}|") !== false;
    }
}