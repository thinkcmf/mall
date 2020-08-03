<?php
// +----------------------------------------------------------------------
// | CMFMall_2020
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 达达 <ccbox.net@163.com>
// +----------------------------------------------------------------------
namespace api\mall\controller;

use api\common\base\ApiBaseController;
use api\mall\service\QueryService;
use app\mall\model\MallCategoryModel;

class CategoryController extends ApiBaseController
{
    /**
     * 列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new MallCategoryModel();
        $map = [];
        $map[] = ['status', '=', 1];
        $data = QueryService::getListAll($model, $map, 'name|keywords|description');
        $ids = array_column($data->toArray(), 'id');
        $list =  $data->map(function ($item) use ($ids) {
            return $this->listItemFilter($item, $ids);
        })->toArray();

        $response = [];
        $response['list'] = array_filter($list);

        $this->success('请求成功!', $response);
    }

    protected function listItemFilter($item, $ids)
    {
        $parent = 'enable';
        if ($item['parent_id'] > 0 && !in_array($item['parent_id'], $ids)) {
            $parent = 'disable';
            return null;
        }
        return [
            "id"          => $item['id'],
            // "create_time" => $item['create_time'],
            // "update_time" => $item['update_time'],
            // "delete_time" => $item['delete_time'],
            // "status"      => $item['status'],
            "list_order"  => $item['list_order'],
            "name"        => $item['name'],
            "alias"       => $item['alias'],
            "thumbnail"   => $item['thumbnail'],
            "thumbnail_url"   => $item->thumbnail_url,
            // "keywords"    => $item['keywords'],
            "description" => $item['description'],
            "parent_id"   => $item['parent_id'],
            "path"        => $item['path'],
        ];
    }
}
