<?php

namespace App\Middlewares;

use App\Exceptions\HttpException;
use App\Middlewares\Contracts\MiddlewareInterface;
use App\Requests\Request;

class JsonRequest implements MiddlewareInterface
{

    public function handle(Request $request, $next = null)
    {
        if ($request->getContentType() !== "application/json") { 
            throw new HttpException('invalid content-type header, accepted type is "application/json"', 422);
        }

        return $request;
    }
}