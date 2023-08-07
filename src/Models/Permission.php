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
}