<?php

namespace XXXX;

class ApiResponse
{
    /**
     * 成功响应
     * @param string $message
     * @param mixed  $data
     * @param int    $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($message = '成功', $data = [], $statusCode = 200)
    {
        return response()->json([
            'code'    => $statusCode,
            'message' => $message ?: '成功',
            'data'    => $data,
        ]);
    }

    /**
     * 失败响应
     * @param string $message
     * @param mixed  $data
     * @param int    $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function failed($message = '失败', $data = [], $statusCode = 500)
    {
        return response()->json([
            'code'    => $statusCode,
            'message' => $message,
            'data'    => $data,
        ]);
    }
}