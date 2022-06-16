<?php
namespace App;
use App\Requests\Request;

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public static function make()
    {
        return new self();
    }

    public function get($path, $handler)
    {
        $this->router->addRoute($path, $handler, 'GET');
    }

    public function post($path, $handler)
    {
        $this->router->addRoute($path, $handler, 'POST');
    }

    public function put($path, $handler)
    {
        $this->router->addRoute($path, $handler, 'PUT');
    }

    public function delete($path, $handler)
    {
        $this->router->addRoute($path, $handler, 'DELETE');
    }

    public function handleRequest(Request $request)
    {
        return $this->router->dispatch($request);
    }

}