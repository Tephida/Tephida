<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

use FluffyDollop\Support\Registry;

use Tephida\Database\Database;

class Module
{
    public string|array|bool|null $user_info;
    protected array $lang;
    protected bool $logged;
    public null|Database $db;

    public function __construct()
    {
        $this->user_info = Registry::get('user_info');
        $this->lang = Registry::get('lang');
        $this->logged = Registry::get('logged');
        $this->db = DB::getDB();
    }
}
