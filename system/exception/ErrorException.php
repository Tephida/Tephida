<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\exception;

use Tephida\Corner\Error;

class ErrorException extends Error
{
    public function __construct(string|false $message = false, $code = false)
    {
        if (!$message) {
            $message = "We encountered an internal error. Please try again.";
        }
        parent::__construct($message, $code);
    }
}