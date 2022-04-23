<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

class News extends \Mozg\classes\Module
{
    final public function main()
    {
        $params = [];
        return view('news.news', $params);
    }
}