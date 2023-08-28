<?php

namespace Zzwacl\EasyACL\Traits;

use Illuminate\Support\Facades\Redis;
use Zzwacl\EasyACL\Models\Role;

trait HasRoles{
    use HasPermissions;

    /**
     * 角色权限关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.user_has_roles'),
            config('permission.column_names.user_pivot_key'),
            config('permission.column_names.role_pivot_id')
        );
    }

    /**
     * 获取用户的角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userRoles()
    {
        return $this->belongsToMany(Role::class, config('permission.table_names.user_has_roles'), config('permission.column_names.user_pivot_key'), config('permission.column_names.role_pivot_key'));
    }

    /**
     * 用户信息存缓存
     * @return string
     */
    public function setUserLogin()
    {
        $user = $this->toArray();
        $user['roles'] = array_column($this->userRoles()->select(config('permission.column_names.role_pivot_key'))->get()->toArray(), config('permission.column_names.role_pivot_key'));

        $jsonUser = json_encode($user);
        $token = md5($jsonUser.time());
        Redis::set($token, $jsonUser, config('permission.login_cache_duration'));

        return $token;
    }
}