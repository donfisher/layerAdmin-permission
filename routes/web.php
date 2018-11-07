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
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            //已登陆接口
            $api->get('tests', 'TestsController@index');
            $api->get('tests/{id}', 'TestsController@show');
            $api->get('user/me', 'AuthController@AuthenticatedUser');//获取用户信息
            $api->post('user/list', 'AuthController@userList');//获取管理员列表
            $api->post('user/changeStatus', 'AuthController@changeAdminStatus');//更改管理员状态

            //角色权限列表
            $api->post('roles/lists', 'AuthController@roleList');
            $api->post('roles/add', 'AuthController@roleAdd');
            $api->post('roles/delete', 'AuthController@roleDelete');
            $api->post('roles/allRoles', 'AuthController@allRoles');
            $api->post('permissions/lists', 'AuthController@permissionList');
            $api->any('permissions/add', 'AuthController@permissionAdd');
            $api->post('permissions/delete', 'AuthController@permissionDelete');
            $api->post('permissions/allPermission', 'AuthController@allPermission');
            $api->post('permissions/changeStatus', 'AuthController@changePermissionStatus');
            //新增管理员
            $api->post('user/register', 'AuthController@register');
        });
    });
});
