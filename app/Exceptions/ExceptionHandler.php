<?php

namespace App\Exceptions;

use App\Response;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $e)
    {
        $statusCode = intval($e->getCode());

        $statusCode = $statusCode >= 100 && $statusCode <= 600
            ? $statusCode
            : 500;

        return Response::json([
            'message' => $e->getMessage(),
        ], $statusCode)->send();
    }
}