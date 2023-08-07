```markdown
# 前后端分离权限管理包（EasyACL）

## 一. 前置步骤

1. 注册服务提供者，在 `config/app.php` 加入：
   
   ```php
   'providers' => [
       Zzwacl\EasyACL\EasyACLServiceProvider::class,
   ],
   ```

2. 执行以下命令发布资源文件并迁移数据库和配置文件：

   ```bash
   php artisan vendor:publish --provider="Zzwacl\EasyACL\EasyACLServiceProvider"
   ```

3. 运行数据库迁移以创建所需的数据表：

   ```bash
   php artisan migrate
   ```

4. 在 `Kernel.php` 中注册中间件：

   ```php
   protected $routeMiddleware = [
       'zzwacl' => \Zzwacl\EasyACL\Middlewares\ZzwaclMiddleware::class,
   ];
   ```

5. 将 `'zzwacl'` 加入路由中间件以验证权限：

   ```php
   Route::group(['middleware' => 'zzwacl'], function () {
       Route::post('/test', [TestController::class, 'test']);
   });
   
## 二. 使用方法

1. 登录时使用 `setUserLogin` 方法设置用户缓存：

   ```php
   $token = $user->setUserLogin();
   ```

2. 对需要验证权限的 `User` 模型引入 `HasRoles` trait：

   ```php
   use Zzwacl\EasyACL\Traits\HasRoles;
   
   class UsersModel extends Model
   {
       use HasRoles;
   }
   ```
   
## 三. 条件

1. PHP 7.2 以上版本
2. 框架 Laravel
3. 安装 Redis
```