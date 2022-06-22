<?php

namespace App\Exceptions;

class NotFoundHttpException extends HttpException
{

    public function getStatusCode()
    {
        return 404;
    }
}