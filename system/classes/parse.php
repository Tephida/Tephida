<?php
/*
	Appointment: Парсинг данных
	File: parse.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
class parse {
    function BBparse($source, $preview = false) {
        global $config;
        $source = preg_replace("#<iframe#i", "&lt;iframe", $source);
        $source = preg_replace("#<script#i", "&lt;script", $source);
        $source = str_ireplace("{", "&#123;", $source);
        $source = str_ireplace("`", "&#96;", $source);
        $source = str_ireplace("{theme}", "&#123;theme}", $source);
        $source = str_ireplace("[b]", "<b>", str_ireplace("[/b]", "</b>", $source));
        $source = str_ireplace("[i]", "<i>", str_ireplace("[/i]", "</i>", $source));
        $source = str_ireplace("[u]", "<u>", str_ireplace("[/u]", "</u>", $source));
        $source = preg_replace("#\[(left|right|center)\](.+?)\[/\\1\]#is", "<div align=\"\\1\">\\2</div>", $source);
        $source = preg_replace("#\[quote\](.+?)\[/quote\]#is", "<blockquote>\\1</blockquote>", $source);
        if (stripos($source, "[video]") !== false || stripos($source, "[photo]") !== false || stripos($source, "[link]") !== false) {
            $source = preg_replace_callback("#\\[video\\](.*?)\\[/video\\]#is", function ($mathes) {
                return $this->BBvideo($mathes[1], $preview);
            }, $source);
            $source = preg_replace_callback("#\\[photo\\](.*?)\\[/photo\\]#is", function ($mathes) {
                return $this->BBphoto($mathes[1]);
            }, $source);
            $source = preg_replace_callback("#\\[link\\](.*?)\\[/link\\]#is", function ($mathes) {
                return $this->BBlink($mathes[1]);
            }, $source);
        }
        return $source;
    }
    function BBvideo($source, $preview = false) {
        global $config;
        $exp = explode('|', $source);
        $home_url = $config['home_url'];
        if (stripos($source, "{$exp[0]}|{$exp[1]}|{$home_url}") !== false) {
            if ($exp[3]) if ($exp[3] > 175) $width = "width=\'175\'";
            else $width = "width=\'{$exp[3]}\'";
            if ($exp[4]) if ($exp[4] > 131) $height = "height=\'131\'";
            else $height = "height=\'{$exp[4]}\'";
            if ($exp[5]) $border = 'notes_videoborder';
            if ($exp[6]) $blank = 'target="_blank"';
            else $blank = "onClick=\"videos.show({$exp[1]}, this.href, \'/notes/view/{note-id}\'); return false\"";
            if ($exp[7] == 1) $pos = "align=\"left\"";
            elseif ($exp[7] == 2) $pos = "align=\"right\"";
            else $pos = "";
            if (!$preview) {
                $link = "<a href=\"/video{$exp[0]}_{$exp[1]}_sec=notes/id={note-id}\" {$blank}>";
                $slink = "</a>";
            }
            $source = "<!--video:{$source}-->{$link}<img src=\"{$exp[2]}\" {$width} {$height} {$pos} class=\"notes_videopad {$border}\" />{$slink}<!--/video-->";
        }
        return $source;
    }
    function BBphoto($source, $preview = false) {
        global $config;
        $exp = explode('|', $source);
        $home_url = $config['home_url'];
        if (stripos($source, "{$exp[0]}|{$exp[1]}|{$home_url}") !== false) {
            if ($exp[3] > 160) $exp[2] = str_replace('/c_', '/', $exp[2]);
            if ($exp[4] > 120) $exp[2] = str_replace('/c_', '/', $exp[2]);
            if ($exp[3]) if ($exp[3] > 740) $width = "width=\'740\'";
            else $width = "width=\'{$exp[3]}\'";
            if ($exp[4]) if ($exp[4] > 547) $height = "height=\'547\'";
            else $height = "height=\'{$exp[4]}\'";
            if ($exp[5]) $border = 'notes_videoborder';
            if ($exp[6]) $blank = 'target="_blank"';
            else $blank = "onClick=\"Photo.Show(this.href); return false\"";
            if ($exp[7] == 1) $pos = "align=\"left\"";
            elseif ($exp[7] == 2) $pos = "align=\"right\"";
            else $pos = "";
            if ($exp[8] AND !$preview AND $exp[0] AND $exp[1]) {
                $link = "<a href=\"/photo{$exp[0]}_{$exp[1]}_sec=notes/id={note-id}\" {$blank}>";
                $elink = "</a>";
            } elseif ($exp[8]) {
                $link = "<a href=\"{$exp[2]}\" target=\"_blank\">";
                $elink = "</a>";
            } else {
                $link = '';
                $elink = '';
            }
            if ($exp[0] AND $exp[1]) $source = "<!--photo:{$source}-->{$link}<img class=\"notes_videopad {$border}\" src=\"{$exp[2]}\" {$width} {$height} {$pos} />{$elink}<!--/photo-->";
            else $source = "<!--photo:{$source}-->{$link}<img class=\"notes_videopad {$border}\" src=\"{$exp[2]}\" {$width} {$height} {$pos} />{$elink}<!--/photo-->";
        }
        return $source;
    }
    function BBlink($source) {
        $exp = explode('|', $source);
        if ($exp[0]) {
            if (!$exp[1]) $exp[1] = $exp[0];
            $exp[0] = str_replace(':', '', $exp[0]);
            $source = "<!--link:{$source}--><a href=\"{$exp[0]}\" target=\"_blank\">{$exp[1]}</a><!--/link-->";
        }
        return $source;
    }
    function BBdecode($source) {
        $source = str_ireplace("&#123;", "{", $source);
        $source = str_ireplace("&#96;", "`", $source);
        $source = str_ireplace("&#123;theme}", "{theme}", $source);
        $source = str_ireplace("<b>", "[b]", str_ireplace("</b>", "[/b]", $source));
        $source = str_ireplace("<i>", "[i]", str_ireplace("</i>", "[/i]", $source));
        $source = str_ireplace("<u>", "[u]", str_ireplace("</u>", "[/u]", $source));
        $source = preg_replace("#<div align=\"(left|right|center)\">(.+?)</div>#is", "[\\1]\\2[/\\1]", $source);
        $source = preg_replace("#\[quote\](.+?)\[/quote\]#is", "<blockquote>\\1</blockquote>", $source);
        $source = preg_replace("#<blockquote>(.+?)</blockquote>#is", "[quote]\\1[/quote]", $source);
        if (stripos($source, "<!--photo:") !== false || stripos($source, "<!--video:") !== false || stripos($source, "<!--link:") !== false) {
            $source = preg_replace_callback("#\\<!--video:(.*?)\\<!--/video-->#is", function ($mathes) {
                return $this->BBdecodeVideo($mathes[1]);
            }, $source);
            $source = preg_replace_callback("#\\<!--photo:(.*?)\\<!--/photo-->#is", function ($mathes) {
                return $this->BBdecodePhoto($mathes[1]);
            }, $source);
            $source = preg_replace_callback("#\\<!--link:(.*?)\\<!--/link-->#is", function ($mathes) {
                return $this->BBdecodeLink($mathes[1]);
            }, $source);
        }
        return $source;
    }
    function BBdecodePhoto($source) {
        $start = explode('-->', $source);
        $source = "[photo]{$start[0]}[/photo]";
        return $source;
    }
    function BBdecodeVideo($source) {
        $start = explode('-->', $source);
        $source = "[video]{$start[0]}[/video]";
        return $source;
    }
    function BBdecodeLink($source) {
        $start = explode('-->', $source);
        $source = "[link]{$start[0]}[/link]";
        return $source;
    }
}
?>