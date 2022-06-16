<?php

namespace App\Controllers\Contracts;

use App\Requests\Request;
use Exception;

class BaseController implements ControllerInterface
{

    /**
     * @throws Exception
     */
    public function handle(Request $request)
    {
        $method = strtolower($request->getMethod());

        if (!method_exists($this,$method )) {
            throw new Exception();
        }

        return $this->$method($request);
    }
}