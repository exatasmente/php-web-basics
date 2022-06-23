<?php

namespace App\Exceptions;

use App\Response;
use Error;
use ErrorException;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $e)
    {

        $statusCode = intval($e->getCode());

        $statusCode = $statusCode >= 100 && $statusCode <= 600
            ? $statusCode
            : 500;

        if ($e instanceof ErrorException || $e instanceof Error) {
            if (getenv('APP_ENV') === 'production') {
                $message = ['unexpected error'];
            } else {
                $message = [$e->getMessage(), $e->getFile(), $e->getLine()];
            }
            $statusCode = 500;
        } else {
            $message = [$e->getMessage()];
        }

        if ($e instanceof Error) {
            return Response::json([
                'message' => implode(', ', $message),
            ], $statusCode)->send();
        }

        return Response::json([
            'message' => implode(', ', $message),
        ], $statusCode);
    }

    public function init()
    {
        error_reporting(-1);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handle']);

        if (getenv('APP_ENV') !== 'production') {
            ini_set('display_errors', 1);
        }
    }

    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }
}