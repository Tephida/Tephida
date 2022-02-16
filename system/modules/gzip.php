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

function CheckCanGzip(): int|string
{
	if(headers_sent() OR connection_aborted() OR !function_exists('ob_gzhandler') OR ini_get('zlib.output_compression')) return 0; 
	if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) return "x-gzip"; 
	if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) return "gzip"; 
	return 0; 
}

function GzipOut(){
	global $Timer, $db, $tpl, $_DOCUMENT_DATE, $user_info;
    $db = Registry::get('db');
    $debug = 0;

	if($debug)
		$s = "!-- Время выполнения скрипта ".$Timer->stop()." секунд --!<br />
!-- Время затраченное на компиляцию шаблонов ".round($tpl->template_parse_time, 5)." секунд --!<br />
!-- Время затраченное на выполнение MySQL запросов: ".round($db->MySQL_time_taken, 5)." секунд --!<br />
!-- Общее количество MySQL запросов ".$db->query_num." --!<br />";

	if($debug AND function_exists("memory_get_peak_usage")) 
		$s .="\n!-- Затрачено оперативной памяти ".round(memory_get_peak_usage()/(1024*1024),2)." MB --!<br />";

	if($_DOCUMENT_DATE){
		@header ("Last-Modified: " . date('r', $_DOCUMENT_DATE) ." GMT");
	}

    $ENCODING = CheckCanGzip(); 

    if($ENCODING){
	
		if($debug)
			$s .= "\n!-- Для вывода использовалось сжатие $ENCODING --!\n<br />"; 
		
        $Contents = ob_get_contents(); 
        ob_end_clean(); 

        if($debug){
            $s .= "!-- Общий размер файла: ".strlen($Contents)." байт "; 
            $s .= "После сжатия: ".
                   strlen(gzencode($Contents, 1, FORCE_GZIP)).
                   " байт -->"; 
            $Contents .= $s; 
        }

        header("Content-Encoding: $ENCODING"); 

		$Contents = gzencode($Contents, 1, FORCE_GZIP);
		echo $Contents;
        exit; 

    } else {
        ob_end_flush(); 
        exit; 
    }
}