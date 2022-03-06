<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

class Cookie
{
    public static function remove(string $name): void
    {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        $expires = time() + 100;
        setcookie($name, '', $expires, "/", $domain, true, true);
    }

    public static function append(string $name, string $value, false|int $expires): void
    {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        if ($expires > 0) {
            $expires = time() + ($expires * 86400);
        } else {
            $expires = 0;
        }
        setcookie($name, $value, $expires, "/", $domain, true, true);
    }
}