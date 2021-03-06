<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['cors']], function ($api) {
        $api->get('posts/recommend', 'PostController@recommend')->name('posts.recommend');
        $api->resource('posts', 'PostController', ['only' => ['index', 'show']]);

        $api->resource('categories', 'CategoryController', ['only' => ['index', 'show']]);
        $api->get('categories/{id}/posts', 'CategoryController@posts')->name('categories.posts');

        $api->get('links', 'LinkController@index')->name('links.index');

        $api->get('search', 'PostController@search')->name('search');
    });
});
