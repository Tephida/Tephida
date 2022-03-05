<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    throw new \RuntimeException("Please change php version");
}
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}
session_start();
ob_start();
ob_implicit_flush(0);
const ROOT_DIR = __DIR__;
const ENGINE_DIR = ROOT_DIR . '/system';
try {
    require_once './vendor/autoload.php';
} catch (Error) {
    throw new \RuntimeException("Please install composer");
}

$array = array(
//    '0',
    '1',
    '2',
    '5',
//    '100',
);
echo declOfNum(5, $array);
exit();

/** Initialize */
include_once ENGINE_DIR . '/init.php';
