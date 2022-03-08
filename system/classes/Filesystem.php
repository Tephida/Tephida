<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class Filesystem
{
    /**
     * Create dir
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    public static function createDir(string $dir, int $mode = 0777): bool
    {
        return !(!is_dir($dir) && !mkdir($dir, $mode, true) && !is_dir($dir));
    }

    /**
     * Delete file OR directory
     * @param string $file
     * @return bool
     */
    public static function delete(string $file): bool
    {
        if (is_dir($file)) {
            if (!str_ends_with($file, '/')) {
                $file .= '/';
            }
            $files = glob($file . '*', GLOB_MARK);
            foreach ($files as $file_) {
                if (is_dir($file_)) {
                    self::delete($file_);
                } else {
                    unlink($file_);
                }
            }
            if (is_dir($file)) {
                rmdir($file);
                return true;
            }
            return false;
        }
        if (is_file($file)) {
            unlink($file);
            return true;
        }
        return false;
    }

    /**
     * Ceck file or dir
     * @param string $file
     * @return bool
     */
    public static function check(string $file): bool
    {
        if (is_file($file)) {
            return true;
        }
        if (is_dir($file)) {
            return true;
        }
        return false;
    }

    public static function copy($from, $to)
    {
        if (is_file($from) && !is_file($to)) {
            copy($from, $to);
            return true;
        }
        return false;
    }

    public static function dirSize($directory): bool|int
    {
        if (!is_dir($directory)) {
            return -1;
        }
        $size = 0;
        if ($DIR = opendir($directory)) {
            while (($dirfile = readdir($DIR)) !== false) {
                if (is_link($directory . '/' . $dirfile) || $dirfile == '.' || $dirfile == '..') {
                    continue;
                }
                if (is_file($directory . '/' . $dirfile)) {
                    $size += filesize($directory . '/' . $dirfile);
                } else if (is_dir($directory . '/' . $dirfile)) {
                    $dirSize = self::dirSize($directory . '/' . $dirfile);
                    if ($dirSize >= 0) {
                        $size += $dirSize;
                    } else {
                        return -1;
                    }
                }
            }
            closedir($DIR);
        }
        return $size;
    }
}