<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'Escotilla to the Rescue';
});

$router->get('/paypal/consent/read', 'PayPalController@getConsent');
$router->get('/question/read', 'QuestionController@read');
$router->post('/payment/create', 'PayPalController@create');
$router->post('/payment/execute', 'PayPalController@execute');
$router->post('/offer/create', 'PayPalController@createOffer');
$router->post('/payout/create', 'PayPalController@createPayout');
$router->post('/payment/profile/create', 'PayPalController@createProfile');

$router->group(['middleware' => 'json'], function () use ($router) {
    $router->post('/user/create', 'UserController@create');
    $router->post('/user/login', 'UserController@login');
    $router->post('/application/create', 'ApplicationController@create');
    $router->post('/application/read', 'ApplicationController@get');
    $router->post('/application/update', 'ApplicationController@update');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/document/create', 'DocumentController@upload');
    $router->post('/document/read', 'DocumentController@download');
    $router->post('/user/read', 'UserController@read');
});

$router->group(['middleware' => ['auth', 'admin']], function () use ($router) {
});