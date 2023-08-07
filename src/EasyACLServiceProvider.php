<?php

namespace Zzwacl\EasyACL;

use Illuminate\Support\ServiceProvider;

class EasyACLServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../config/permission.php' => config_path('permission.php'),
        ], 'zzwacl-config');

        // 发布迁移文件
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'zzwacl-migrations');

        // 注册迁移文件
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}