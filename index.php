<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

declare(strict_types=1);

use Mozg\Mozg;

if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    throw new \RuntimeException('Please change php version');
}
if (isset($_POST['PHPSESSID'])) {
    session_id($_POST['PHPSESSID']);
}
session_start();
ob_start();
ob_implicit_flush(false);
const ROOT_DIR = __DIR__;
const ENGINE_DIR = ROOT_DIR . '/system';
try {
    require __DIR__ . '/vendor/autoload.php';
} catch (Error) {
    throw new \RuntimeException('Please install composer');
}

/** Initialize */
(new Mozg())::initialize();
//include_once ENGINE_DIR . '/init.php';


