<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

class Module
{
    public Templates $tpl;

    function __construct()
    {
        $this->tpl = new Templates();
        $config = settings_get();
        $this->tpl->dir = ROOT_DIR . '/templates/' . $config['temp'];
    }
}