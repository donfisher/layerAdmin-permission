<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Controllers','middleware' => ['client.change']], function ($api) {
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
        $api->post('user/register', 'AuthController@register');

        //leads录入
        $api->post('leads/create','LeadsController@create');
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            //已登陆接口
            $api->get('tests', 'TestsController@index');
            $api->get('tests/{id}', 'TestsController@show');
            $api->get('user/me', 'AuthController@AuthenticatedUser');//获取用户信息
            $api->post('user/list', 'AuthController@userList');//获取管理员列表
            $api->post('leads/lists','LeadsController@lists');
        });
    });
});
