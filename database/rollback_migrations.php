<?php

use App\Application;

require __DIR__ . '/../vendor/autoload.php';
$app = Application::make();

$app->executeMigrations(false);