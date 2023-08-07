适用前后端分离权限管理包
一. 前置
1.注册服务提供者，在config/app.php加入
    'providers' => [
        Zzwacl\EasyACL\EasyACLServiceProvider::class,
    ],
2.执行 php artisan verdor:publish --provider="Zzwacl\EasyACL\EasyACLServiceProvider"，迁移数据库和配置文件
3.执行 php artisan migrate，创建数据表
4.在 Kernel.php 注册中间件
    protected $routeMiddleware = [
        'zzwacl' => \Zzwacl\EasyACL\Middlewares\ZzwaclMiddleware::class,
    ];
5.将 'zzwacl' 加入路由中间件验证权限
    Route::group(['middleware' => 'zzwacl'], function () {
        Route::post('/test', [TestController::class, 'test']);
    });

二. 使用方法
1.登陆时使用 setUserLogin 方法设置用户缓存
    $token = $user->setUserLogin();
2.需要验证权限的 User 模型，引入 Hasroles
    use Zzwacl\EasyACL\Traits\HasRoles;
    class UsersModel extends Model
    {
        use HasRoles;
    }
三. 条件
1.php7.2以上
2.框架 laravel
3.安装 Redis
