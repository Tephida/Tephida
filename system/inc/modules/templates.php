<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use Mozg\classes\TplCp;

$tpl = new TplCp(ADMIN_DIR . '/tpl/');
$directory = './templates';
$scanned_directory = array_diff(scandir($directory), array('..', '.', '.htaccess'));
$tpl->load_template('tpl/tpl.tpl');
foreach ($scanned_directory as $directory) {
    $tpl->set('{dir}', $directory);
    if (file_exists('./templates/' . $directory . '/images/tpl.png')) {
        $img = '/templates/' . $directory . '/images/tpl.png';
    } else {
        $img = '/templates/' . $directory . '/images/100_no_ava.png';
    }
    $tpl->set('{img}', $img);

    $tpl->compile('tpl');
}
$tpl->load_template('/tpl/home.tpl');
$tpl->set('{tpl}', $tpl->result['tpl']);
$tpl->compile('content');
$tpl->render();