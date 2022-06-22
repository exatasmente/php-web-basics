<?php
use App\Requests\Request;

$app = require_once __DIR__.'/../setup/init.php';
$request = Request::capture();
$app->handleRequest($request);
