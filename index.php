<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    throw new InvalidArgumentException("Please change php version");
}
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}
session_start();
ob_start();
ob_implicit_flush(0);
const MOZG = true;
define("ROOT_DIR", dirname(__FILE__));
const ENGINE_DIR = ROOT_DIR . '/system';
require_once './vendor/autoload.php';
include_once ENGINE_DIR . '/functions.php';

//include_once ENGINE_DIR . '/classes/Registry.php';
//include_once ENGINE_DIR . '/classes/AntiSpam.php';
//include_once ENGINE_DIR . '/classes/Filesystem.php';
//include_once ENGINE_DIR . '/classes/Templates.php';
//include_once ENGINE_DIR . '/classes/mysql.php';
//include_once ENGINE_DIR . '/classes/Gzip.php';

/** Initialize */
include_once ENGINE_DIR . '/init.php';
