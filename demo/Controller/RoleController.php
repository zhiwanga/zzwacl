<?php

namespace XXX;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $pagesize = pageSize($request->get('pagesize'));
        $list = RoleModel::select('id', 'name', 'created_at')->orderBy('id', 'desc')->paginate($pagesize)->toArray();
        return ApiResponse::success('', pageResult($list));
    }

    /**
     * 添加
     * @param Request $request
     * @return json
     */
    public function create(Request $request)
    {
        $requestData = $request->only('name', 'permission_ids');

        $rules = [
            'name'              => 'required',
            'permission_ids'    => 'sometimes|array',
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $role = new RoleModel();
            $roleCreate = $role->create(['name' => $requestData['name']]);
            if($requestData['permission_ids']) {
                $role->assignPermissionsToRole($roleCreate->id, $requestData['permission_ids']);
            }

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
        $requestData = $request->only('role_id', 'name', 'permission_ids');

        $rules = [
            'role_id'           => 'required',
            'name'              => 'required',
            'permission_ids'    => 'sometimes|array',
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $role = new RoleModel();
            $role->where('id', $requestData['role_id'])->update(['name' => $requestData['name']]);

            $role->removePermissionsFromRole($requestData['role_id']);

            if($requestData['permission_ids']) {
                $requestData['permission_ids'] = array_unique(array_filter($requestData['permission_ids']));
                $permissions = PermissionModel::whereIn('id', $requestData['permission_ids'])->get()->toArray();
                $role->assignPermissionsToRole($requestData['role_id'], $permissions);
            }
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
        $id = $request->get('role_id');
        if(!$id) {
            return ApiResponse::failed('缺少传参');
        }else{
            $role = new RoleModel();
            $role->removePermissionsFromRole($id);
            $role->where('id', $id)->delete();
            return ApiResponse::success();
        }
    }
}
