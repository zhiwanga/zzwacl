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
   ```

## 二. 配置

1. PHP 7.2 以上版本
2. 框架 Laravel
3. 安装 Redis

## 三. 方法

1. 登录时使用 `setUserLogin` 方法设置用户缓存：

   ```php
    $userModel = new UsersModel();
    $user = $userModel->where('account', $requestData['account'])->orWhere('phone', $requestData['account'])->first();
    if ($user && Hash::check($requestData['password'], $user->password)) {

        // $oldToken = $user->access_token;

        // 生成token，用户信息保存至缓存
        $token = $user->setUserLogin();

        // $user->update(['access_token' => $token]);

        // 是否单点登录
        // if($this->single) Redis::del($oldToken);

        // 成功结果
    }else{
        // 失败结果
    }
   ```

2. 对需要验证权限的 `User` 模型引入 `HasRoles` trait：

   ```php
   use Zzwacl\EasyACL\Traits\HasRoles;
   
   class UsersModel extends Model
   {
       use HasRoles;
   }
   ```
3. 修改，删除角色时需要调用此方法，管理缓存

   ```php
   $user->removePermissionsFromRole();
   ```

4. 添加，修改，删除权限时需要调用此方法，管理缓存
   ```php
   $user->removeRoleFromPermissions();
   ```

## 四. 操作

    ```php
    use Zzwacl\EasyACL\Models\Role;
    use Zzwacl\EasyACL\Traits\HasRoles;

    $roleModel = new Role;
    /**
     * 获取角色权限
     * @param integer $roleId
     * @param integer $type 1：所有权限数据，1<：角色路由
     * @return array
     */ 
    $roleModel->getPermissionsForRole();
    /**
     * 获取多个角色权限路由
     * @param array $roleIds
     * @return array
     */
    $roleModel->getPermissionsForRoles();


    $userModel = new UsersModel;
    $user = $userModel->find(1);

    /**
     * 获取用户的角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    $roles = $user->userRoles()->pluck('name');

    /**
     * 获取用户所有权限
     * @return \Illuminate\Support\Collection
     */
    $roles = $user->permissionsOwned()->pluck('admin_route');

    /**
     * 角色绑定权限（追加形式）
     * @param integer $roleId
     * @param array $permissions 权限数组
     * @return null
     */
    $user->assignPermissionsToRole();

    /**
     * 角色移除全部权限
     * @param integer $roleId
     * @return integer
     */
    $user->removePermissionsFromRole();


   ```

```