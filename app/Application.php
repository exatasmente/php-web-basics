<?php
namespace App;
use App\Exceptions\ExceptionHandler;
use App\Models\AbstractModel;
use App\Requests\Request;
use App\Utils\DotEnv;

class Application
{
    private Router $router;

    public function __construct()
    {
        DotEnv::load(__DIR__. '/../.env');
        $dns = 'mysql:host='. getenv('DB_HOST') .';dbname='. getenv('DB_TABLE');
        $connection = new \PDO($dns,getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        AbstractModel::useConnection($connection);
    }

    public static function make()
    {
        return new self();
    }


    public function handleRequest(Request $request)
    {
        try {
            return $this->router->dispatch($request);
        } catch (\Exception $e) {
            return (new ExceptionHandler())->handle($e);
        }
    }

    public function registerRoutes()
    {
        $this->router = include_once (__DIR__ . '/../routes/routes.php');
    }

}