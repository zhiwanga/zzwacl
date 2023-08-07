<?php
return [
    // 表名
    'table_names' => [

        'roles'                 => 'roles',                 // 角色

        'permissions'           => 'permissions',           // 权限

        'users_has_permissions' => 'user_has_permissions',  // 用户权限单独关联

        'users_has_roles'       => 'user_has_roles',        // 用户角色关联

        'role_has_permissions'  => 'role_has_permissions',  // 角色权限关联
    ],
    // 列名
    'column_names' => [
        'user_pivot_key'        => 'user_id',               // 用户关联id
        'role_pivot_key'        => 'role_id',               // 角色关联id
        'permission_pivot_key'  => 'permission_id',         // 权限关联id

        'admin_route'           => 'admin_route',           // 后端路由（权限验证字段）
    ],
    // 模型
    'models' => [
        'permission'    => Zzwacl\EasyACL\Models\Permission::class,
        'role'          => Zzwacl\EasyACL\Models\Role::class,
    ],
];