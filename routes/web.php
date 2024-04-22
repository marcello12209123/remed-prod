<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', 'userController@login');
$router->get('/logout', 'userController@logout');

$router->group(['prefix' => 'stuff', 'middleware'=>'auth'], function() use ($router) {

    $router->get('/data', 'stuffController@index'); 
    $router->post('/', 'stuffController@store');
    $router->get('/trash', 'stuffController@trash');

    $router->get('{id}', 'stuffController@show');
    $router->patch('/{id}', 'stuffController@update');
    $router->delete('/{id}', 'stuffController@destroy');
    $router->get('/restore/{id}', 'stuffController@restore');
    $router->delete('/permanent/{id}', 'stuffController@deletePermanent');

});

$router->group(['prefix' => 'user'], function() use ($router) {
    // static routes : tetap
    $router->get('/data', 'UserController@index');
    $router->post('/store', 'UserController@store');
    $router->get('/trash', 'UserController@trash');

    //dunamic routes : berubah - rubah
    $router->get('{id}', 'UserController@show');
    $router->patch('/{id}', 'UserController@update');
    $router->delete('/{id}','UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->delete('/permanent/{id}', 'UserController@deletePermanent');
});

$router->group(['prefix' => 'inbound-stuff/', 'middleware' => 'auth'],
function () use ($router) {
    $router->get('/', 'inboundstuffController@index');
    $router->post('store', 'inboundstuffController@store');
    $router->get('detail/{id}', 'inboundstuffController@show');
    $router->patch('update/{id}', 'inboundstuffController@update');
    $router->delete('delete/{id}', 'inboundstuffController@destroy');
    $router->get('recycle bin', 'inboundstuffController@recycleBin');
    $router->get('restore/{id}', 'inboundstuffController@restore');
    $router->get('force-delete/{id}', 'inboundstuffController@forceDestroy');    
});

$router->group(['prefix' => 'stuff-stock/', 'middleware' => 'auth'],
function () use ($router) {
    $router->get('/', 'stuffstockController@index');
    $router->post('store', 'stuffstockController@store');
    $router->get('detail/{id}', 'stuffstockController@show');
    $router->patch('update/{id}', 'stuffstockController@update');
    $router->delete('delete/{id}', 'stuffstockController@destroy');
    $router->get('recycle bin', 'stuffstockController@recycleBin');
    $router->get('restore/{id}', 'stuffstockController@restore');
    $router->get('/permanent/{id}', 'stuffstockController@restore');
    $router->post('add_stock/{id}', 'stuffstockController@addstock');
    $router->post('sub-stock/{id}', 'stuffstockController@substock');

    
});





