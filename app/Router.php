<?php
namespace App;

use App\Controllers\Contracts\ControllerInterface;
use App\Exceptions\HttpException;
use App\Exceptions\NotFoundHttpException;
use App\Requests\Request;
use Exception;

class Router
{
    public array $routes = [];

    /**
     * Regex from PhpRouter package
     * @see https://github.com/mrjgreen/phroute/
     */
    const VARIABLE_REGEX = "~\{\s* ([a-zA-Z0-9_]*) \s*?}\??~x";

    private function getRouteVariables($path)
    {
        if(preg_match_all(self::VARIABLE_REGEX, $path, $matches, PREG_SET_ORDER))
        {
            return $matches;
        }

        return null;
    }

    public function get($path, $handler)
    {
        $this->addRoute($path, $handler, 'GET');
    }

    public function post($path, $handler)
    {
        $this->addRoute($path, $handler, 'POST');
    }

    public function put($path, $handler)
    {
        $this->addRoute($path, $handler, 'PUT');
    }

    public function delete($path, $handler)
    {
        $this->addRoute($path, $handler, 'DELETE');
    }


    public function addRoute($path, $handler, $method)
    {
        $hasPath = array_key_exists($path, $this->routes);
        $variables = $this->getRouteVariables($path);
        $params = [];

        if ($variables) {
            $exploded = explode('/', $path);
            foreach ($variables as $variable) {
                $param = $variable[0];
                $params[$param] = array_search($param, $exploded);
            }

        }

        $route = [
            'path' => $path,
            'params' => $params,
            'handler' => $handler,
        ];

        if (!$hasPath) {
            $this->routes[$path] = [strtoupper($method) => $route];
        } else {
            $this->routes[$path][strtoupper($method)] = $route;
        }
    }



    /**
     * @throws Exception
     */
    public function dispatch(Request $request)
    {
        $path = $request->getRequestUri();
        $method = strtoupper($request->getMethod());
        $routes = array_keys($this->routes);
        $route = null;
        $params = [];

        if (in_array($path, $routes)) {
            $route = $this->routes[$path];
            if (!array_key_exists($method, $route)) {
              throw new NotFoundHttpException('unable to find route ' . strtoupper($method) . ' : ' . $path, 404);
            }

            $route = $route[$method];
        } else {
            $numberOfParts = substr_count($path, '/');
            $routes = array_filter($routes, function ($route) use ($numberOfParts, $method) {
                return substr_count($route, '/') === $numberOfParts && array_key_exists($method, $this->routes[$route]);
            });

            if (empty($routes)) {
                throw new NotFoundHttpException('unable to find route ' . strtoupper($method) . ' : ' . $path, 404);
            }

            $pathParts = explode('/', $path);

            foreach ($routes as $possibleRoute) {
                $params = [];
                $routeParts = explode('/', $possibleRoute);
                $currentRoute = $this->routes[$possibleRoute];

                $currentRoute = $currentRoute[$method];
                $currentRouteParams = $currentRoute['params'];
                $parsedRoute = [];

                foreach ($routeParts as $index => $routePart) {

                    if ($routePart === $pathParts[$index]) {
                        $parsedRoute []= $pathParts[$index];
                    } else if (array_key_exists($routePart, $currentRouteParams)) {
                        $param = $currentRouteParams[$routePart];

                        if ($param === $index) {
                            $parsedRoute [] = $routePart;
                            $params[$index] = $pathParts[$index];
                        }
                    }
                }

                if (count($parsedRoute) === count($pathParts)) {
                    $route = $currentRoute;
                    break;
                }
            }
        }

        if (!$route) {
            throw new NotFoundHttpException('unable to find route ' . strtoupper($method) . ' : ' . $path, 404);
        }

        $handler = $route['handler'];

        return $this->callHandler($handler, $request, $params);
    }

    /**
     * @throws Exception
     */
    private function callHandler($handler, $request, $params)
    {
        if (is_string($handler)) {
            [$action, $method] = explode('@', $handler);
        } else if (is_array($handler) && count($handler)  == 2) {
            $action = $handler[0];
            $method = $handler[1];
        } else if (is_callable($handler)) {
            return call_user_func($handler, $request, ...$params);
        } else {
            throw new HttpException('fail to call handler', 500);
        }

        if (class_exists($action) && in_array(ControllerInterface::class, class_implements($action) ?: [])) {
            $handlerInstance = new $action();
            if ($method && method_exists($handlerInstance, $method)) {
                return $handlerInstance->$method($request, ...$params);
            } else if (is_callable($handlerInstance)) {
                return $handlerInstance($request, ...$params);
            }
        }

        throw new HttpException('fail to call handler', 500);
    }
}