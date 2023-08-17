<?php

namespace Zzwacl\EasyACL\Cache;

use Illuminate\Support\Facades\Redis;

class RoleCacheManager
{
    private $roleId = 0;
    private $prefix = '';

    public function __construct($roleId)
    {
        $this->roleId = $roleId;
        $this->prefix = config('database.redis.options.prefix');
    }

    /**
     * 设置缓存
     * @param array $permissions
     * @return void
     */
    public function storePermissions(array $permissions)
    {
        Redis::sadd("zzwacl_role_route:$this->roleId", ...array_unique(array_filter(array_column($permissions, config('permission.column_names.admin_route', 'admin_route')))));

        Redis::set("zzwacl_role_data:$this->roleId", json_encode($permissions));
    }

    /**
     * 获取角色拥有的路由
     * @return array
     */
    public function getAdminRoutes()
    {
        return Redis::smembers($this->prefix.'zzwacl_role_route:'.$this->roleId);
    }

    /**
     * 获取角色所有权限
     * @return array
     */
    public function getPermissionsJson()
    {
        $json = Redis::get($this->prefix.'zzwacl_role_data:'.$this->roleId);
        return json_decode($json, true);
    }

    /**
     * 清除缓存
     * @return void
     */
    public function clearRoleCache()
    {
        Redis::del("zzwacl_role_route:$this->roleId");
        Redis::del("zzwacl_role_data:$this->roleId");
    }
}
