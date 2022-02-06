<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die('Hacking attempt!');

NoAjaxQuery();

$tpl->load_template('lang/main.tpl');

$useLang = isset($_COOKIE['lang']) ? intval($_COOKIE['lang']) : null;
if($useLang <= 0) $useLang = 1;
$config['lang_list'] = nl2br($config['lang_list']);
$expLangList = explode('<br />', $config['lang_list']);
$numLangs = count($expLangList);
$cil = 0;
$langs = '';
foreach($expLangList as $expLangData){
	
	$expLangName = explode(' | ', $expLangData);
	
	if($expLangName[0]){
		
		$cil++;
		
		if($cil == $useLang OR $numLangs == 1)
		
			$langs .= "<div class=\"lang_but lang_selected\">{$expLangName[0]}</div>";
			
		else
		
			$langs .= "<a href=\"/index.php?act=chage_lang&id={$cil}\" style=\"text-decoration:none\"><div class=\"lang_but\">{$expLangName[0]}</div></a>";
		
	}
	
}

if(!$checkLang) $checkLang = 'Russian';

$tpl->set('{langs}', $langs);

$tpl->compile('content');

AjaxTpl();

exit();
