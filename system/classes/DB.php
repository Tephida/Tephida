<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use Tephida\Database\Database;
use Tephida\Database\Factory;

class DB
{
    private static ?Database $database = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static function getDB(): null|Database
    {
        if (self::$database === null) {
            if (!\is_file(ENGINE_DIR . '/data/db_config.php')) {
                echo 'err';
                exit();
            }
            $db_config = require ENGINE_DIR . '/data/db_config.php';
            self::$database = Factory::fromArray([
                'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['name'],
                $db_config['user'],
                $db_config['pass']
            ]);
        }
        return self::$database;
    }
}