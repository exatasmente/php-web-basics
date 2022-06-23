<?php
use App\Requests\Request;

$app = require_once __DIR__.'/../setup/init.php';
$request = Request::capture();
$response = $app->handleRequest($request);
$response->send();
