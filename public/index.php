<?php

use App\Application;
use App\Requests\Request;
require __DIR__.'/../vendor/autoload.php';

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


try {
    $app = Application::make();
    $app->get('/owner/{id}/property', function (Request $request, $id) {
        echo '{"id" : ' . $id . '}';
    });
    $app->get('/owner/{owner_id}/property/{property_id}', 'App\Controllers\PropertyController@getOwnerProperty');

    $request = Request::capture();
    $app->handleRequest($request);
} catch (\Exception $e) {
    var_dump($e);
}