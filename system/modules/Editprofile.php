<?php

namespace Mozg\modules;

use FluffyDollop\Support\Filesystem;
use FluffyDollop\Support\Status;
use Mozg\classes\Cache;
use Mozg\classes\Module;

class Editprofile extends Module
{
    final public function deletePhoto(): void
    {
        NoAjaxQuery();
        $user_info = $this->user_info;
        $db = $this->db;
        $user_id = $user_info['user_id'];
        $upload_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
        $row = $db->super_query("SELECT user_photo, user_wall_id FROM `users` WHERE user_id = '{$user_id}'");
        if ($row['user_photo']) {
            $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE id = '{$row['user_wall_id']}'");
            if ($check_wall_rec['cnt']) {
                $update_wall = ", user_wall_num = user_wall_num-1";
                $db->query("DELETE FROM `wall` WHERE id = '{$row['user_wall_id']}'");
                $db->query("DELETE FROM `news` WHERE obj_id = '{$row['user_wall_id']}'");
            } else {
                $update_wall = null;
            }
            $db->query("UPDATE `users` SET user_photo = '', user_wall_id = '' {$update_wall} WHERE user_id = '{$user_id}'");
            Filesystem::delete($upload_dir . $row['user_photo']);
            Filesystem::delete($upload_dir . '50_' . $row['user_photo']);
            Filesystem::delete($upload_dir . '100_' . $row['user_photo']);
            Filesystem::delete($upload_dir . 'o_' . $row['user_photo']);
            Filesystem::delete($upload_dir . 'c_' . $row['user_photo']);
            //TODO удалить из альбома
            Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
            Cache::mozgClearCache();
            $response = array(
                'status' => Status::OK,
            );
        } else {
            $response = array(
                'status' => Status::BAD,
            );
        }
        _e_json($response);
    }
}