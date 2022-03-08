<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class Gzip
{
    private bool $debug = false;

    function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function CheckCanGzip(): false|string
    {
        if (headers_sent() || connection_aborted() || !function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) {
            return false;
        }
        if (str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
            return "x-gzip";
        }
        if (str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            return "gzip";
        }
        return false;
    }

    public function GzipOut(): int
    {
        $db = Registry::get('db');

        if ($this->debug) {
            $s = "!-- Общее количество MySQL запросов " . $db->query_num . " --!<br />";
            $s .= "\n!-- Затрачено оперативной памяти " . round(memory_get_peak_usage() / (1024 * 1024), 2) . " MB --!<br />";
        }

        $ENCODING = $this->CheckCanGzip();

        if ($ENCODING) {
            $Contents = ob_get_clean();

            if ($this->debug) {
                $s = $s ?? '';
                $s .= "\n!-- Для вывода использовалось сжатие $ENCODING --!\n<br />";
                $s .= "!-- Общий размер файла: " . strlen($Contents) . " байт ";
                $s .= "После сжатия: " .
                    strlen(gzencode($Contents, 1, FORCE_GZIP)) .
                    " байт -->";
                $Contents .= $s;
            }
            header("Content-Encoding: $ENCODING");
            $Contents = gzencode($Contents, 1, FORCE_GZIP);
            return print($Contents);
        }
        ob_end_flush();
        return print('');
    }
}