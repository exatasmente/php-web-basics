<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception implements HttpExceptionInterface
{

    public function getStatusCode()
    {
        return $this->getCode();
    }
}