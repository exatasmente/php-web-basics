<?php
namespace App\Middlewares\Contracts;

use App\Requests\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, $next = null);
}