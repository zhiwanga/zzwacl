<?php

namespace Zzwacl\EasyACL\Models;

use Illuminate\Database\Eloquent\Model;
use Zzwacl\EasyACL\Cache\RoleCacheManager;

class Role extends Model
{
    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    /**
     * 角色权限关联
     * @return object
     */
    public function permissions()
    {
        $columnNames = config('permission.column_names');
        $tableNames = config('permission.table_names');
        return $this->belongsToMany(config('permission.models.permission'), $tableNames['role_has_permissions'], $columnNames['role_pivot_key'], $columnNames['permission_pivot_key']);
    }

    /**
     * 获取角色权限 （选字段会影响缓存，默认所有字段）
     * @param integer $roleId
     * @param integer $type 1：所有权限数据，1<：角色路由
     * @return array
     */
    public function getPermissionsForRole(int $roleId, $type = 1)
    {
        $roleCacheManager = new RoleCacheManager($roleId);
        $roleCache = $roleCacheManager->getAdminRoutes();
        if(!$roleCache) {
            $role = self::find($roleId);
            if (!$role) return [];
    
            $permissions = $role->permissions()->get()->toArray();
            $roleCacheManager->storePermissions($permissions);
        }
        return $this->permissionsForRoleReturn($roleCacheManager, $type);
    }

    private function permissionsForRoleReturn(&$model, $type)
    {
        return (1 == $type) ? $model->getPermissionsJson() : $model->getAdminRoutes();
    }

    /**
     * 获取多个角色权限路由
     * @param array $roleIds
     * @return array
     */
    public function getPermissionsForRoles(array $roleIds)
    {
        $permissions = [];
        foreach ($roleIds as $v) {
            $rolePermissions = $this->getPermissionsForRole($v, 2);
            $permissions = array_merge($permissions, $rolePermissions);
        }
        return array_unique($permissions);
    }
}