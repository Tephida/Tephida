<?php

namespace Mozg\classes;

class DB
{
    private static ?\Tephida\Database\Database $database = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static function getDB(): null|\Tephida\Database\Database
    {
        if (self::$database === null) {
            if (!\is_file(ENGINE_DIR . '/data/db_config.php')) {
                echo 'err';
                exit();
            }
            $db_config = require ENGINE_DIR . '/data/db_config.php';
            self::$database = \Tephida\Database\Factory::fromArray([
                'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['name'],
                $db_config['user'],
                $db_config['pass']
            ]);
        }
        return self::$database;
    }
}