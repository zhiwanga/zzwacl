<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEasyaclTables extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $tableNames     = config('permission.table_names');
        $columnNames    = config('permission.column_names');

        // 角色
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->default('')->comment('角色名称');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `".$tableNames['roles']."` comment '角色表' ENGINE = InnoDB");

        // 权限
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('栏目名称');
            $table->string('admin_route', 64)->default('')->comment('后端路由');

            // 父级id，层级，前端页面路由，前端图标，是否显示等...
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `".$tableNames['permissions']."` comment '权限表' ENGINE = InnoDB");

        // 用户权限关联
        // Schema::create($tableNames['user_has_permissions'], function (Blueprint $table) use ($columnNames) {
        //     $table->integer($columnNames['user_pivot_key'])->default(0)->comment('用户id');
        //     $table->integer('permission_id')->default(0)->comment('权限id');
        //     $table->timestamps();
        // });
        // DB::statement("ALTER TABLE `".$tableNames['user_has_permissions']."` comment '用户权限单独关联' ENGINE = InnoDB");

        // 用户角色关联
        Schema::create($tableNames['user_has_roles'], function (Blueprint $table) use ($columnNames) {
            $table->integer($columnNames['user_pivot_key'])->default(0)->comment('用户id');
            $table->integer('role_id')->default(0)->comment('角色id');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `".$tableNames['user_has_roles']."` comment '用户角色关联表' ENGINE = InnoDB");

        // 角色权限关联
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->integer('role_id')->default(0)->comment('角色 id');
            $table->integer('permission_id')->default(0)->comment('权限 id');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `".$tableNames['role_has_permissions']."` comment '用户角色关联表' ENGINE = InnoDB");
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['user_has_roles']);
        Schema::dropIfExists($tableNames['user_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
}
