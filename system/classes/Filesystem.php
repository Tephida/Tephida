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
        if (!is_dir($dir) && !mkdir($dir, $mode, true) && !is_dir($dir)) { // @ - dir may already exist
//            throw new InvalidArgumentException("Unable to create directory '$dir' with mode ");
            return false;
        } else
            return true;
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
            } else
                return false;
        } elseif (is_file($file)) {
            unlink($file);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ceck file or dir
     * @param string $file
     * @return bool
     */
    public static function check(string $file): bool
    {
        if (is_file($file))
            return true;
        elseif (is_dir($file))
            return true;
        else
            return false;
    }
}