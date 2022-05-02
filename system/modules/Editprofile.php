<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use FluffyDollop\Filesystem\Filesystem;
use FluffyDollop\Support\Status;
use Mozg\classes\Cache;
use Mozg\classes\DB;
use Mozg\classes\Module;
use Mozg\Models\Users;

class Editprofile extends Module
{
    /**
     * @throws \JsonException
     */
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
        (new \FluffyDollop\Http\Response)->_e_json($response);
    }

    public function main()
    {
        $user_info = $this->user_info;
        $lang = $this->lang;

        $params['title'] = $lang['editmyprofile'] . ' &raquo; ' . $lang['editmyprofile_genereal'];

//        $tpl->load_template('editprofile.tpl');

        $row = DB::getDB()->row('SELECT user_name, user_lastname, user_sex, user_day, 
       user_month, user_year, 
       user_country, user_city, user_sp FROM `users` WHERE user_id = ?', $user_info['user_id']);

        $params['name'] = $row['user_name'];
        $params['lastname'] = $row['user_lastname'];
        $params['sex'] = addToList($row['user_sex'], array(
            1 => 'мужской',
            2 => 'женский'
        ));

        $days = [];
        for ($i = 1; $i <= 31; ++$i) {
            $days[$i] = $i;
        }

        $params['user_day'] = addToList($row['user_day'], $days);
        $params['user_month'] = addToList($row['user_sex'], array(
            1 => 'Января',
            2 => 'Февраля',
            3 => 'Марта',
            4 => 'Апреля',
            5 => 'Мая',
            6 => 'Июня',
            7 => 'Июля',
            8 => 'Августа',
            9 => 'Сентября',
            10 => 'Октября',
            11 => 'Ноября',
            12 => 'Декабря'
        ));
        $years = [];
        for ($i = 1950; $i <= 2022; ++$i) {
            $years[$i] = $i;
        }
        $params['user_year'] = addToList($row['user_day'], $years);

        //################## Загружаем Страны ##################//
        $sql_country = DB::getDB()->run('SELECT * FROM `country` ORDER by `name` ASC');
        $all_country = [];
        foreach ($sql_country as $row_country) {
            $all_country[$row_country['id']] = stripslashes($row_country['name']);
        }
        $params['country'] = addToList($row['user_country'], $all_country);

        //################## Загружаем Города ##################//
        $sql_city = DB::getDB()->run("SELECT id, name FROM `city` WHERE id_country = ? ORDER by `name` ASC", $row['user_country']);
        $all_city = [];
        foreach ($sql_city as $row2) {
            $all_city[$row2['id']] = stripslashes($row2['name']);
        }
        $params['city'] = addToList($row['user_city'], $all_city);

        $user_sp = explode('|', $row['user_sp']);
        if (!empty($user_sp[1])) {
            $rowSp = DB::getDB()->run("SELECT user_search_pref FROM `users` WHERE user_id = ?", $user_sp[1]);
            $params['sp_name'] = $rowSp['user_search_pref'];
//            $tpl->set_block("'\\[sp\\](.*?)\\[/sp\\]'si", "");
//            $params['ttt'] = ;

            $params['sp_text'] = Users::relationships($row['user_sex'], $user_sp[0]);
        } else {
            $params['sp_text'] = '';
            $params['sp_name'] = '';
        }

//        else {
//            $tpl->set('[sp]', '');
//            $tpl->set('[/sp]', '');
//        }

        if ($row['user_sex'] == 2) {
            $params['gender'] = 'male';
//            $tpl->set('[user-m]', '');
//            $tpl->set('[/user-m]', '');
//            $tpl->set_block("'\\[user-w\\](.*?)\\[/user-w\\]'si", "");
        } elseif ($row['user_sex'] == 1) {
            $params['gender'] = 'female';
//            $tpl->set('[user-w]', '');
//            $tpl->set('[/user-w]', '');
//            $tpl->set_block("'\\[user-m\\](.*?)\\[/user-m\\]'si", "");
        } else {
//            $tpl->set('[sp-all]', '');
//            $tpl->set('[/sp-all]', '');
//            $tpl->set('[user-m]', '');
//            $tpl->set('[/user-m]', '');
//            $tpl->set('[user-w]', '');
//            $tpl->set('[/user-w]', '');
        }

//        $tpl->copy_template = str_replace("[instSelect-sp-{$user_sp[0]}]", 'selected', $tpl->copy_template);
//        $tpl->set_block("'\\[instSelect-(.*?)\\]'si", "");

//        $tpl->set_block("'\\[contact\\](.*?)\\[/contact\\]'si", "");
//        $tpl->set_block("'\\[interests\\](.*?)\\[/interests\\]'si", "");
//        $tpl->set_block("'\\[xfields\\](.*?)\\[/xfields\\]'si", "");
//        $tpl->set('[general]', '');
//        $tpl->set('[/general]', '');
//        $tpl->compile('content');
//        $tpl->clear();

        return view('settings.profile', $params);
    }
}