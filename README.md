## 前后端分离权限管理包
   - 避免中间件查询数据库
   - 所有权限使用 redis 管理

## 一. 配置

   - PHP 7.2 ^
   - 框架 Laravel
   - 安装 Redis

## 二. 前置步骤

   1. 注册服务提供者，在 `config/app.php` 加入：

      ```php
      'providers' => [
         Zzwacl\EasyACL\EasyACLServiceProvider::class,
      ],
      ```

   2. 执行以下命令发布资源文件并迁移数据库和配置文件：

      ```php
      php artisan vendor:publish --provider="Zzwacl\EasyACL\EasyACLServiceProvider"
      ```

   3. 运行数据库迁移以创建所需的数据表（可选：增加role 或 permission 字段）：

      ```php
      php artisan migrate
      ```

   4. 在 `app/Http/Kernel.php` 中注册中间件：

      ```php
      protected $routeMiddleware = [
         'zzwacl' => \Zzwacl\EasyACL\Middlewares\ZzwaclMiddleware::class,
      ];
      ```

   5. 将 `zzwacl` 加入路由中间件以验证权限：

      ```php
      Route::middleware(['zzwacl'])->group(function () {
         Route::post('/test', [TestController::class, 'test']);
      });
      ```

   6. 将 `.env` 文件的 `APP_NAME` 修改为项目名称，不然同一服务器不同项目会出现权限覆盖，导致旧项目权限不足问题

## 三. 方法

   1. 对需要验证权限的 `User` `Role` 模型引入 `HasRoles` trait：

      ```php
      use Zzwacl\EasyACL\Traits\HasRoles;

      class UserModel extends Model
      {
         use HasRoles;
      }

      use Zzwacl\EasyACL\Traits\HasRoles;

      class RoleModel extends Model
      {
         use HasRoles;
      }
      ```

   2. 登录时使用 `setUserLogin` 方法设置用户缓存：

      ```php
      $userModel = new UserModel();
      $user = $userModel->where('phone', $requestData['phone'])->first();
      if ($user && Hash::check($requestData['password'], $user->password)) {
            // 是否单点登录
            // $oldToken = $user->access_token;
            // if($this->single) Redis::del($oldToken);

         // 生成token，用户信息保存至缓存
         $token = $user->setUserLogin();

         $user->update(['access_token' => $token]);

         // 成功结果
      }else{
         // 失败结果
      }
      ```

   3. 修改，删除角色时需调用此方法。详情请查看 demo/RoleController

      ```php
      $user->removePermissionsFromRole($roleId);
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
   $roleModel->getPermissionsForRole($roleId);
   /**
    * 获取多个角色权限路由
    * @param array $roleIds
    * @return array
    */
   $roleModel->getPermissionsForRoles([1,2,3]);

   $userModel = new UserModel;
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
    * @param array $permissions 权限数组二维数组
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

## 五.可能出现的问题

   1. 权限不足
      - 保证 permissions 表有需要访问的路由
      
      - 确保 role_has_permissions 存在角色和权限的关联
      - 确保 users 存在该用户
      - 确保 user_has_roles 存在用户和角色的关联
      - 确保 roles 存在角色
