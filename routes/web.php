<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Http\Request;
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

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'UserController@register');
    $router->post('login', 'UserController@login');
});


$router->group(['middleware' => 'jwt'], function () use ($router) { 
    $router->get('/products', 'ProductController@getAllProduct');
    $router->get('/product/detail/{id}', 'ProductController@getProductById');
    $router->group(['middleware' => 'check_admin'], function () use ($router) {
        $router->post('/product/create', 'ProductController@create');
        $router->put('/product/update/{id}', 'ProductController@update');
        $router->delete('/product/delete/{id}', 'ProductController@delete');
    });
    $router->get('/orders', 'OrderController@getAllOrderByUser');
    $router->post('/order/create', 'OrderController@create');
    $router->get('/order/detail/{id}', 'OrderController@getOrderById');
});

