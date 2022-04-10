<?php

namespace Mozg\modules;

class Home extends \Mozg\classes\Module
{
    /**
     * @throws \JsonException|\ErrorException
     */
    public function main()
    {
        $logged = $this->logged;
        if ($logged === true) {
//            (new Profile)->main();
            (new News)->main();
        } else {
            (new Register)->main();
        }
    }
}