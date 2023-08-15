<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\PermissionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * 栏目列表
     * @param Request $request
     * @return json
     */
    public function index()
    {
        $menus = PermissionModel::select('id', 'admin_route', 'name', 'parent_id', 'level', 'sort', 'home_route', 'home_icon', 'is_hide', 'created_at')
                            ->orderBy('sort')
                            ->get();

        return ApiResponse::success('', $menus);
    }

    /**
     * 添加
     * @param Request $request
     * @return json
     */
    public function create(Request $request)
    {
        $requestData = $request->only('admin_route', 'name', 'parent_id', 'home_route', 'home_icon', 'is_hide');

        $rules = [
            'name'          => 'required',
            'parent_id'     => 'required|numeric',
            'is_hide'       => 'required|numeric|min:1',
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $level = PermissionModel::where('id', $requestData['parent_id'])->value('level');
            $insert = [
                'admin_route'   => $requestData['admin_route'],
                'name'          => $requestData['name'],
                'level'         => $level+1,
                'parent_id'     => $requestData['parent_id'],
                'home_route'    => $requestData['home_route'],
                'home_icon'     => $requestData['home_icon'],
                'is_hide'       => $requestData['is_hide']
            ];

            PermissionModel::create($insert);
            return ApiResponse::success();
        }
    }

    /**
     * 修改
     * @param Request $request
     * @return json
     */
    public function update(Request $request)
    {
        $requestData = $request->only('permission_id', 'admin_route', 'name', 'parent_id', 'home_route', 'home_icon', 'is_hide');

        $rules = [
            'admin_route'   => 'required',
            'name'          => 'required',
            'parent_id'     => 'required',
            'home_route'    => 'required',
            'home_icon'     => 'required',
            'is_hide'       => 'required'
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $level = PermissionModel::where('id', $requestData['parent_id'])->value('level');
            $update = [
                'admin_route'   => $requestData['admin_route'],
                'name'          => $requestData['name'],
                'level'         => $level+1,
                'parent_id'     => $requestData['parent_id'],
                'home_route'    => $requestData['home_route'],
                'home_icon'     => $requestData['home_icon'],
                'is_hide'       => $requestData['is_hide']
            ];

            PermissionModel::where('id', $requestData['permission_id'])->update($update);
            return ApiResponse::success();
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return json
     */
    public function delete(Request $request)
    {
        $id = $request->get('permission_id');
        if(!$id) {
            return ApiResponse::failed('缺少传参');
        }else{
            PermissionModel::where('id', $id)->delete();
            return ApiResponse::success();
        }
    }
}
