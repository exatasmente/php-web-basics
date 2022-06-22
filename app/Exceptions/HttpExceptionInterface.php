<?php

namespace App\Exceptions;

interface HttpExceptionInterface extends \Throwable
{
    public function getStatusCode();

}