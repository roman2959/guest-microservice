<?php

use App\Http\Controllers\GuestController;

$router->group(['prefix' => 'guests'], function () use ($router) {
    $router->get('/', 'GuestController@index');
    $router->get('{id}', 'GuestController@show');
    $router->post('/', 'GuestController@store');
    $router->put('{id}', 'GuestController@update');
    $router->delete('{id}', 'GuestController@destroy');
});
