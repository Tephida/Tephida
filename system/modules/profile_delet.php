<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die("Hacking attempt!");
$user_info = $user_info ?? Registry::get('user_info');
if($user_info['user_group'] != '1'){
	$tpl->load_template('profile_deleted.tpl');
	$tpl->compile('main');
    $config = settings_get();
	echo str_replace('{theme}', '/templates/'.$config['temp'], $tpl->result['main']);
}