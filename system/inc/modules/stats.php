<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Filesystem;
use Mozg\classes\TplCp;

$users = $db->super_query("SELECT COUNT(*) AS cnt FROM `users`");
$albums = $db->super_query("SELECT COUNT(*) AS cnt FROM `albums`");
$attach = $db->super_query("SELECT COUNT(*) AS cnt FROM `attach`");
$audio = $db->super_query("SELECT COUNT(*) AS cnt FROM `audio`");
$groups = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities`");
$groups_wall = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_wall`");
$invites = $db->super_query("SELECT COUNT(*) AS cnt FROM `invites`");
$notes = $db->super_query("SELECT COUNT(*) AS cnt FROM `notes`");
$videos = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos`");

$db->query("SHOW TABLE STATUS FROM `" . DBNAME . "`");
$mysql_size = 0;
while ($r = $db->get_array()) {
    if (str_contains($r['Name'], "")) {
        $mysql_size += $r['Data_length'] + $r['Index_length'];
    }
}
//$db->free();
$mysql_size = Filesystem::formatsize($mysql_size);

$cache_size = Filesystem::formatsize(Filesystem::dirSize("uploads"));

$tpl = new TplCp(ADMIN_DIR . '/tpl/');
$tpl->load_template('stats/main.tpl');
$tpl->set('{cache_size}', $cache_size);
$tpl->set('{mysql_size}', $mysql_size);
$tpl->set('{users_cnt}', $users['cnt']);
$tpl->set('{albums_cnt}', $albums['cnt']);
$tpl->set('{attach_cnt}', $attach['cnt']);
$tpl->set('{audio_cnt}', $audio['cnt']);
$tpl->set('{groups_cnt}', $groups['cnt']);
$tpl->set('{groups_wall_cnt}', $groups_wall['cnt']);
$tpl->set('{invites_cnt}', $invites['cnt']);
$tpl->set('{notes_cnt}', $notes['cnt']);
$tpl->set('{videos_cnt}', $users['cnt']);
$tpl->compile('content');
$tpl->render();