<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (version_compare(PHP_VERSION, '8.0.0') < 0){
    echo '<div style="color: red">Error 500</div>';
}
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}
@session_start();
@ob_start();
@ob_implicit_flush(0);
const MOZG = true;
define("ROOT_DIR", dirname(__FILE__));
const ENGINE_DIR = ROOT_DIR . '/system';
header('Content-type: text/html; charset=utf-8');
//AJAX
$ajax = $_POST['ajax'] ?? null;
$logged = false;
$user_info = false;
include_once ENGINE_DIR . '/classes/Registry.php';
include ENGINE_DIR . '/classes/Filesystem.php';
include ENGINE_DIR . '/init.php';
