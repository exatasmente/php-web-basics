<?php

$app = require_once __DIR__ . '/../setup/init.php';
$app->executeMigrations(true);