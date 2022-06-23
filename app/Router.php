<?php

namespace App;

use App\Exceptions\NotFoundHttpException;
use App\Requests\Request;
use Exception;

class Router
{
    /**
     * Regex from PhpRouter package
     * @see https://github.com/mrjgreen/phroute/
     */
    const VARIABLE_REGEX = "~\{\s* ([a-zA-Z0-9_]*) \s*?}\??~x";
    public array $routes = [];
    public array $globalMiddleware = ['before' => [], 'after' => []];

    public function get($path, $handler)
    {
        return $this->addRoute($path, $handler, 'GET');
    }

    public function addRoute($path, $handler, $method)
    {
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

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

        $routeInstance = new Route($handler, $this->globalMiddleware);

        $route = [
            'path' => $path,
            'params' => $params,
            'handler' => $routeInstance,
        ];

        if (!$hasPath) {
            $this->routes[$path] = [strtoupper($method) => $route];
        } else {
            $this->routes[$path][strtoupper($method)] = $route;
        }

        return $routeInstance;
    }

    private function getRouteVariables($path)
    {
        if (preg_match_all(self::VARIABLE_REGEX, $path, $matches, PREG_SET_ORDER)) {
            return $matches;
        }

        return null;
    }

    public function post($path, $handler)
    {
        return $this->addRoute($path, $handler, 'POST');
    }

    public function put($path, $handler)
    {
        return $this->addRoute($path, $handler, 'PUT');
    }

    public function delete($path, $handler)
    {
        return $this->addRoute($path, $handler, 'DELETE');
    }

    public function middleware($middleware, $before = true)
    {
        $this->globalMiddleware[$before ? 'before' : 'after'] [] = $middleware;
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
                        $parsedRoute [] = $pathParts[$index];
                    } else if (array_key_exists($routePart, $currentRouteParams)) {
                        $param = $currentRouteParams[$routePart];

                        if ($param === $index) {
                            $parsedRoute [] = $routePart;
                            $routePart = str_replace('{', '', $routePart);
                            $routePart = str_replace('}', '', $routePart);
                            $params[$routePart] = $pathParts[$index];
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

        if (!($handler instanceof Route)) {
            throw new NotFoundHttpException('unable to find route ' . strtoupper($method) . ' : ' . $path, 404);
        }


        return $handler->handle($request, $params);
    }

}