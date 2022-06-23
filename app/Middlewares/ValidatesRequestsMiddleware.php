<?php

namespace App\Middlewares;

use App\Middlewares\Contracts\MiddlewareInterface;
use App\Requests\Request;

class ValidatesRequestsMiddleware implements MiddlewareInterface
{

    public function handle(Request $request, $next = null)
    {
        $request->validateRequest();

        return $request;
    }
}