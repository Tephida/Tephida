<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
namespace Mozg\classes;

class Module
{
    public Mysql $db;
    public Templates $tpl;
    public string|Mysql|array|bool|null $user_info;

    function __construct()
    {
        $this->db = Registry::get('db');
        $this->user_info = Registry::get('user_info');
        $this->lang = Registry::get('lang');
        $this->tpl = new Templates();
        $config = settings_get();
        $this->tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];
    }
}