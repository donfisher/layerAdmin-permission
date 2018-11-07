<p align="center">
<h2>
后端基于Laravel下jwt+Dingo+permission <br>
前端基于layuiAdmin的用户权限角色绑定分配和Api接口开发通用轮子。
主要包括后台用户注册登录，权限和角色CURD，以及用户角色权限分配，开箱即用。
</h2>
</p>

## About layerAdmin-permission
使用组件如下： <br>
后端 <br>
1.Laravel (https://github.com/laravel/laravel) <br>
2.tymon/jwt-auth (https://github.com/tymondesigns/jwt-auth)  <br>
3.dingo/api (https://github.com/dingo/api) <br>
4.spatie/laravel-permission (https://github.com/spatie/laravel-permission) <br>

前端 <br>
layuiAdmin (http://www.layui.com/admin/)
## Usage
1、确保本地环境已安装composer<br>
2、git clone 到本地目录。<br>
3、此laravel框架不包含vendor目录，需要执行composer update 升级最新package(下载更新vendor目录) <br>
4、根目录新建.env文件，配置mysql数据库信息,添加API_PREFIX=api <br>
5、php artisan migrate 通过migrations创建数据表。<br>
6、进入后台登录页http://localhost/permission/public/start/index.html#/user/login <br>
7、默认管理员账号:123@abc  默认密码：1234567 <br>
6、Do what you want to do...
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
