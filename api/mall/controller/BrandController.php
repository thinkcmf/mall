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
use app\mall\model\MallBrandModel;

class BrandController extends ApiBaseController
{
    /**
     * 列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $model = new MallBrandModel();
        $map = [];
        $map[] = ['status', '=', 1];
        $data = QueryService::getListAll($model, $map, 'name|alias|keywords|description', ['id']);
        $response = [];
        $response['list'] = array_map([__CLASS__, 'listItemFilter'], $data->all());
        $this->success('请求成功!', $response);
    }

    protected static function listItemFilter($item)
    {
        return [
            "id"          => $item['id'],
            // "create_time" => $item['create_time'],
            // "update_time" => $item['update_time'],
            // "delete_time" => $item['delete_time'],
            // "status"      => $item['status'],
            "list_order"  => $item['list_order'],
            "name"        => $item['name'],
            "alias"       => $item['alias'],
            "logo"        => $item['logo'],
            "logo_url"        => $item['logo_url'],
            // "keywords"    => $item['keywords'],
            "description" => $item['description'],
            "url"         => $item['url']
        ];
    }
}
