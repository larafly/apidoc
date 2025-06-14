<?php

namespace Larafly\Apidoc\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct($message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
