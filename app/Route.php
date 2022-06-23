<?php

namespace App;

use App\Controllers\Contracts\ControllerInterface;
use App\Exceptions\HttpException;
use App\Requests\Contracts\RequestInterface;
use App\Requests\Request;
use App\Requests\StorePropertyContractRequest;
use ReflectionClass;
use ReflectionParameter;

class Route
{
    private $action;
    protected array $middleware;

    public function __construct($action, array $middleware = ['before' => [], 'after' => []])
    {
        $this->action = $action;
        $this->middleware = $middleware;
    }

    public function middleware($middleware, $before = true)
    {
        $this->middleware[$before ? 'before' : 'after'] []= $middleware;
    }

    /**
     * @throws HttpException
     */
    public function handle(Request $request, $params)
    {
        return $this->callHandler($request, $params);
    }

    /**
     * @throws HttpException
     */
    private function callHandler(Request $request, $params)
    {

        $handler = $this->action;

        if (is_string($handler)) {
            [$action, $method] = explode('@', $handler);
        } else if (is_array($handler) && count($handler)  == 2) {
            $action = $handler[0];
            $method = $handler[1];
        } else {
            throw new HttpException('fail to call handler', 500);
        }

        if (class_exists($action) && in_array(ControllerInterface::class, class_implements($action) ?: [])) {
            $handlerInstance = new $action();
            $class = new ReflectionClass($handlerInstance);
            $classMethod = $class->getMethod($method);
            $classParams = $classMethod->getParameters();

            $requestClass = $classParams[0]->getType()->getName();


            if (class_exists($requestClass)) {
                /** @var RequestInterface $requestClass */
                $request = (new $requestClass())->initializeFromRequest($request);
                $request->setRouteParams($params);
            }

            $beforeMiddleware = $this->middleware['before'];

            foreach ($beforeMiddleware as $middleware) {
                $request = $middleware->handle($request);
            }


            if ($method && method_exists($handlerInstance, $method)) {
                $response = $handlerInstance->$method($request, ...$params);
            } else if (is_callable($handlerInstance)) {
                $response =  $handlerInstance($request, ...$params);
            }

            if (isset($response)) {
                $afterMiddleware = $this->middleware['after'];

                $next = function () use ($response) {
                    return $response;
                };

                foreach ($afterMiddleware as $middleware) {
                    $response = $middleware->handle($request, $next);
                }

                return $response;
            }

        }

        throw new HttpException('fail to call handler', 500);
    }
}