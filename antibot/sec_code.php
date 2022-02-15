<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
@session_start();

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

function clean_url(string $url)
{
    $url = str_replace("http://", "", strtolower($url));
    $url = str_replace("https://", "", $url);
    if (str_starts_with($url, 'www.'))
        $url = substr($url, 4);
    $url = explode('/', $url);
    $url = reset($url);
    $url = explode(':', $url);
    return reset($url);

}

if(clean_url($_SERVER['HTTP_REFERER']) != clean_url($_SERVER['HTTP_HOST'])) 
	die("Hacking attempt!");

$user_code = $_GET['user_code'];

if($user_code == $_SESSION['sec_code']){
	echo 'ok';
} else {
	echo 'no';
}
