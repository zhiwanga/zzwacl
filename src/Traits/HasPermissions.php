<?php

namespace Zzwacl\EasyACL\Traits;

use Zzwacl\EasyACL\Cache\RoleCacheManager;
use Zzwacl\EasyACL\Models\Permission;
use Zzwacl\EasyACL\Models\Role;

trait HasPermissions
{
    /**
     * 获取用户所有权限
     * @return \Illuminate\Support\Collection
     */
    public function permissionsOwned()
    {
        return $this->loadMissing('roles', 'roles.permissions')
                    ->roles->flatMap(function ($role) {
                        return $role->permissions;
                    })->values();
    }

    /**
     * 角色绑定权限（追加形式）
     * @param integer $roleId
     * @param array $permissions
     * @return null
     */
    public function assignPermissionsToRole(int $roleId, array $permissionIds)
    {
        $roleCacheManager = new RoleCacheManager($roleId);
        $roleCacheManager->clearRoleCache();
        return Role::find($roleId)->permissions()->attach($permissionIds);
    }

    /**
     * 角色移除全部权限
     * @param integer $roleId
     * @return integer
     */
    public function removePermissionsFromRole(int $roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            return 0;
        }else{
            $roleCacheManager = new RoleCacheManager($roleId);
            $roleCacheManager->clearRoleCache();
            return $role->permissions()->detach();
        }
    }

    /**
     * 移除权限关联角色缓存
     * @param integer $permissionId
     * @return boolean
     */
    public function removeRoleFromPermissions(int $permissionId)
    {
        $permission = Permission::find($permissionId);
        if($permission) {
            $roleIds = $permission->privileges()->pluck('role_id')->toArray();
            foreach ($roleIds as $value) {
                $roleCacheManager = new RoleCacheManager($value);
                $roleCacheManager->clearRoleCache();
            }
        }
        return true;
    }
}