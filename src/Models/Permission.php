<?php

namespace Zzwacl\EasyACL\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function getTable()
    {
        return config('permission.table_names.permissions', parent::getTable());
    }

    /**
     * 验证用户是否有访问该路由权限
     * @param string $user
     * @param string $routeName
     * @return boolean
     */
    public function hasRoutePermission($user, $routeName)
    {
        $user = json_decode($user, true);
        $roles = (new Role())->getPermissionsForRoles($user['roles']);

        if (in_array($routeName, $roles)) {
            return true;
        }
        return false;
    }

    /**
     * 权限关联角色
     * @return \Illuminate\Support\Collection
     */
    public function privileges()
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.permission_pivot_key'),
            config('permission.column_names.role_pivot_key')
        );
    }
}