<?php
/**
 * For development proposes
 * TODO: Vista Challenge
 */

use App\Application;
use App\Exceptions\ExceptionHandler;
use App\Utils\DotEnv;

require __DIR__.'/../vendor/autoload.php';

DotEnv::load(__DIR__. '/../.env');
$exceptionHandler = new ExceptionHandler();
$exceptionHandler->init();

$app = Application::make();

$app->registerRoutes();

return $app;