<?php
namespace App;

use App\Controllers\Contracts\ControllerInterface;
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
        } else {
            $numberOfParts = substr_count($path, '/');
            $routes = array_filter($routes, function ($route) use ($numberOfParts) {
                return substr_count($route, '/') === $numberOfParts;
            });

            if (empty($routes)) {
                throw new Exception();
            }

            $pathParts = explode('/', $path);

            foreach ($routes as $possibleRoute) {
                $params = [];
                $routeParts = explode('/', $possibleRoute);
                $currentRoute = $this->routes[$possibleRoute];

                if (!array_key_exists($method, $currentRoute)) {
                    continue;
                }

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
            throw new Exception('No route found');
        }

        return $this->callHandler($route['handler'], $request, $params);
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
            throw new Exception('Unable to call handler');
        }

        if (class_exists($action) && in_array(ControllerInterface::class, class_implements($action) ?: [])) {
            $handlerInstance = new $action();
            if ($method && method_exists($handlerInstance, $method)) {
                return $handlerInstance->$method($request, ...$params);
            } else if (is_callable($handlerInstance)) {
                return $handlerInstance($request, ...$params);
            }
        }

        throw new Exception('Unable to call handler ' . $action);
    }
}