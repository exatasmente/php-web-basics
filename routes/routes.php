<?php

use App\Router;

$router = new Router();

$router->middleware(new App\Middlewares\ValidatesRequestsMiddleware());
$router->middleware(new App\Middlewares\JsonRequest());
$router->get('/tenant', 'App\Controllers\TenantController@all');
$router->get('/tenant/{id}', 'App\Controllers\TenantController@show');
$router->post('/tenant', 'App\Controllers\TenantController@store');
$router->put('/tenant/{id}', 'App\Controllers\TenantController@update');
$router->delete('/tenant/{id}', 'App\Controllers\TenantController@delete');

$router->get('/property-owner', 'App\Controllers\PropertyOwnerController@all');
$router->get('/property-owner/{id}', 'App\Controllers\PropertyOwnerController@show');
$router->post('/property-owner', 'App\Controllers\PropertyOwnerController@store');
$router->put('/property-owner/{id}', 'App\Controllers\PropertyOwnerController@update');
$router->delete('/property-owner/{id}', 'App\Controllers\PropertyOwnerController@delete');

$router->get('/property', 'App\Controllers\PropertyController@all');
$router->get('/property/{id}', 'App\Controllers\PropertyController@show');
$router->post('/property', 'App\Controllers\PropertyController@store');
$router->put('/property/{id}', 'App\Controllers\PropertyController@update');
$router->delete('/property/{id}', 'App\Controllers\PropertyController@delete');

$router->get('/property/{id}/contract', 'App\Controllers\PropertyController@getContracts');
$router->post('/property/{id}/contract', 'App\Controllers\PropertyController@storeContract');
$router->get('/property/{id}/contract/{contract_id}', 'App\Controllers\PropertyController@getContract');
$router->get('/property/{id}/contract/{contract_id}/payment', 'App\Controllers\PropertyController@getContractPayments');
$router->get('/property/{id}/contract/{contract_id}/payment/{payment_id}', 'App\Controllers\PropertyController@getContractPayment');
$router->put('/property/{id}/contract/{contract_id}/payment/{payment_id}', 'App\Controllers\PropertyController@updateContractPayment');
$router->get('/property/{id}/contract/{contract_id}/payment/{payment_id}/transfer', 'App\Controllers\PropertyController@getContractPaymentTransfers');
$router->get('/property/{id}/contract/{contract_id}/payment/{payment_id}/transfer/{transfer_id}', 'App\Controllers\PropertyController@getContractPaymentTransfer');
$router->put('/property/{id}/contract/{contract_id}/payment/{payment_id}/transfer/{transfer_id}', 'App\Controllers\PropertyController@updateContractPaymentTransfer');

$router->get('/property-contract', 'App\Controllers\PropertyContractController@all');
$router->get('/property-contract/{id}', 'App\Controllers\PropertyContractController@show');
$router->post('/property-contract', 'App\Controllers\PropertyContractController@store');
$router->put('/property-contract/{id}', 'App\Controllers\PropertyContractController@update');
$router->delete('/property-contract/{id}', 'App\Controllers\PropertyContractController@delete');

$router->get('/contract-payment', 'App\Controllers\ContractPaymentController@all');
$router->get('/contract-payment/{id}', 'App\Controllers\ContractPaymentController@show');
$router->put('/contract-payment/{id}', 'App\Controllers\ContractPaymentController@update');

$router->get('/contract-payment-transfer', 'App\Controllers\ContractPaymentTransferController@all');
$router->get('/contract-payment-transfer/{id}', 'App\Controllers\ContractPaymentTransferController@show');
$router->put('/contract-payment-transfer/{id}', 'App\Controllers\ContractPaymentTransferController@update');


return $router;