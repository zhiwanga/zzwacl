<?php

namespace Zzwacl\EasyACL\Middlewares;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Zzwacl\EasyACL\Models\Permission;

class ZzwaclMiddleware
{
    public function handle(Request $request, $next)
    {
        $accessToken = $request->header('access-token');

        if(!$accessToken) {
            return response()->json(['code' => 401, 'message' => '未登录']);
        }else{
            $user = Redis::get($accessToken);
            if(!$user) {
                return response()->json(['code' => 401, 'message' => '登录已过期']);
            }else{
                $permission = new Permission();
            
                $routePath = $request->getPathInfo();
                if(!$permission->hasRoutePermission($user, $routePath)) {
                    return response()->json(['code' => 403, 'message' => '权限不足，'.$routePath]);
                }
                return $next($request);
            }
        }
    }
}