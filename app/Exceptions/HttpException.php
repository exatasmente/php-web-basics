<?php

namespace App\Exceptions;

class HttpException extends \Exception implements HttpExceptionInterface
{

    public function getStatusCode()
    {
        return $this->getCode();
    }
}