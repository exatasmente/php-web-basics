<?php

namespace App;

use App\DatabaseMigrations\ExecuteMigrations;
use App\Exceptions\ExceptionHandler;
use App\Models\AbstractModel;
use App\Requests\Request;
use PDO;

class Application
{
    private Router $router;
    private PDO $db;

    public function __construct()
    {
        $dns = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_TABLE');
        $connection = new \PDO($dns, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        $this->db = $connection;

        AbstractModel::useConnection($connection);
    }

    public static function make()
    {
        return new self();
    }

    public function getDBConnection()
    {
        return $this->db;
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
        $this->router = include_once(__DIR__ . '/../routes/routes.php');
    }


    public function executeMigrations($up = true)
    {
        $executeMigrations = new ExecuteMigrations();

        $executeMigrations->run($up);
    }
}