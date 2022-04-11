<?php

namespace Mozg\modules;

use FluffyDollop\Support\Registry;
use Mozg\classes\Cache;
use Mozg\classes\Module;

class Updates extends Module
{
    /**
     * todo update to json
     * @return void
     */
    final public function main(): void
    {
/*        if (Registry::get('logged')) {
//            $db = $this->db;
            $server_time = time();
            $user_info = $this->user_info;
            $user_id = $user_info['user_id'];
            $cntCacheUp = Cache::mozgCache("user_{$user_id}/updates");
            if ($cntCacheUp) {
                $update_time = $server_time - 70;
                $row = $db->super_query("SELECT id, type, from_user_id, text, lnk, user_search_pref, user_photo FROM `updates` WHERE for_user_id = '{$user_id}' AND date > '{$update_time}' ORDER by `date` ASC");
                if ($row) {
                    if ($row['user_photo']) {
                        $ava = "/uploads/users/{$row['from_user_id']}/50_{$row['user_photo']}";
                    } else {
                        $ava = "/images/no_ava_50.png";
                    }
                    $row['text'] = str_replace("|", "&#124;", $row['text']);
                    echo $row['type'] . '|' . $row['user_search_pref'] . '|' . $row['from_user_id'] . '|' . stripslashes($row['text']) . '|' . $server_time . '|' . $ava . '|' . $row['lnk'];
                    $db->query("DELETE FROM `updates` WHERE id = '{$row['id']}'");
                } else {
                    Cache::mozgCreateCache("user_{$user_id}/updates", '');
                }
            }
        }*/
    }
}