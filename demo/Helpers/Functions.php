<?php

/**
 * 处理分页返回值
 * @param [type] $data
 * @return array
 */
function pageResult($data)
{
    return [
        'current_page'      => $data['current_page'],
        'total_page'        => ceil($data['total'] / config('system.page.pageSize')),
        'per_page'          => $data['per_page'],
        'total'             => $data['total'],
        'list'              => $data['data'],
    ];
}

/**
 * 处理分页最大值
 * @param [type] $pagesize
 * @return int
 */
function pageSize($pagesize)
{
    $pagesize = intval($pagesize);
    $pagesize = ($pagesize > 0) ? $pagesize : config('system.page.pageSize');
    return ($pagesize > 1000) ? 1000 : $pagesize;
}