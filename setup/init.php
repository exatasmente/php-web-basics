<?php
/**
 * For development proposes
 * TODO: Response class
 * TODO: Request Validation
 * TODO: ORM
 * TODO: Middleware
 * TODO: Exception Handler, to generate user friendly response.
 * TODO: Vista Challenge
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use App\Application;
use App\Requests\Request;

require __DIR__.'/../vendor/autoload.php';
$app = Application::make();
$app->registerRoutes();

return $app;