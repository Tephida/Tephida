<?php

namespace Mozg\modules;

class News extends \Mozg\classes\Module
{
    final public function main()
    {
        $params = [];


        return view('news.news', $params);
    }
}