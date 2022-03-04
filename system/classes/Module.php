<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
namespace Mozg\classes;

use FluffyDollop\Support\Registry;
use FluffyDollop\Support\Mysql;

class Module
{
    public Mysql $db;
    public Templates $tpl;
    public string|Mysql|array|bool|null $user_info;
//    private string|Mysql|array|bool|null $lang;
    protected string $tpl_dir_name;


    public function __construct()
    {
        $this->db = Registry::get('db');
        $this->user_info = Registry::get('user_info');
        $this->lang = Registry::get('lang');
        $this->tpl = new Templates();
        $config = settings_get();
        $this->tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];

        $this->tpl_dir_name = ROOT_DIR . '/templates/' . $config['temp'];
    }
}