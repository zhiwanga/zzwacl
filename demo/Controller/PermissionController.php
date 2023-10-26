<?php

namespace XXX;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\PermissionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * 栏目列表（有层级）
     * @param Request $request
     * @return json
     */
    public function menu(Request $request)
    {
        $data = PermissionModel::select('id', 'parent_id', 'name', 'level', 'admin_route', 'is_hide', 'sort')
                                ->orderBy('sort')
                                ->get()
                                ->toArray();

        $data = $this->buildTree($data);
        return ApiResponse::success('', $data);
    }

    /**
     * 栏目列表（无层级）
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = PermissionModel::select('id', 'parent_id', 'name', 'level', 'admin_route', 'is_hide', 'sort')
                                ->orderBy('sort')
                                ->get()
                                ->toArray();
        return ApiResponse::success('', $data);
    }

    /**
     * 添加
     * @param Request $request
     * @return json
     */
    public function create(Request $request)
    {
        $requestData = $request->only('name', 'admin_route', 'parent_id', 'is_hide');

        $rules = [
            'name'          => 'required',
            'parent_id'     => 'required|numeric',
            'admin_route'   => 'present',
            'is_hide'       => 'required|numeric|min:1',
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $level = PermissionModel::where('id', $requestData['parent_id'])->value('level');
            $insert = [
                'name'          => $requestData['name'],
                'admin_route'   => $requestData['admin_route'],
                'level'         => $level+1,
                'parent_id'     => $requestData['parent_id'],
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
        $requestData = $request->only('permission_id', 'name', 'admin_route', 'parent_id', 'is_hide');
        $rules = [
            'permission_id' => 'required',
            'name'          => 'required',
            'admin_route'   => 'present',
            'parent_id'     => 'required|numeric',
            'is_hide'       => 'required|numeric|min:1',
        ];

        $validator = Validator::make($requestData, $rules);

        if ($validator->fails()) {
            return ApiResponse::failed($validator->errors()->first());
        }else{
            $level = PermissionModel::where('id', $requestData['parent_id'])->value('level');
            $update = [
                'name'          => $requestData['name'],
                'admin_route'   => $requestData['admin_route'],
                'level'         => $level+1,
                'parent_id'     => $requestData['parent_id'],
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

    // ————————————————————————————————————————————————————————————————————内置方法————————————————————————————————————————————————————————————————————
    /**
     * 处理菜单层级
     * @param [type] $data
     * @param [type] $parentId
     * @return array
     */
    private function buildTree($data, $parentId = null) {
        $tree = array();
        foreach ($data as $row) {
            if ($row['parent_id'] == $parentId) {
                $children = $this->buildTree($data, $row['id']);
                if ($children) {
                    $row['children'] = $children;
                }
                $tree[] = $row;
            }
        }
        return $tree;
    }
}
