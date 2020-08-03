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
namespace api\mall\service;

use think\facade\Request as FacadeRequest;
use think\Model;
use think\Request;

/**
 * 通用查询服务提供
 *
 */
class QueryService
{
    public static function getBuilder(Model $model, array $map, string $keyField, $orderFields, $orderDefault)
    {
        // where
        $map[] = ['delete_time', '=', 0];

        $request = request();
        $keyword = $request->param('keyword');
        if ($keyword) {
            $map[] = [$keyField, 'like', "%$keyword%"];
        }

        $model = $model::where($map);

        // order by
        $order   = $request->param('order');
        $orderby = [];
        if ($orderDefault) {
            $orderby[$orderDefault] = 'desc';
        }
        if ($order && !empty($orderFields)) {
            $desc  = substr($order, 0, 1) == '+' ? 'asc' : 'desc';
            $order = substr($order, 1);
            if (in_array($order, $orderFields)) {
                $orderby[$order] = $desc;
            }
        }
        $builder = $model->order($orderby);

        return $builder;
    }

    public static function getListOnPage(Model $model, array $map = [], $keyField = 'title', $orderFields = [], $orderDefault = 'list_order')
    {
        $builder = self::getBuilder($model, $map, $keyField, $orderFields, $orderDefault);
        // if (!empty($field)) {
        //     $model = $model->field($field);
        // }
        $data = $builder->paginate();

        return [
            'total'        => $data->total(),
            'list_rows'    => $data->listRows(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
            'has_more'     => $data->currentPage() < $data->lastPage(),
            'items'        => $data->items()
        ];
    }

    public static function getListAll(Model $model, array $map = [], $keyField = 'title', $orderFields = [], $orderDefault = 'list_order')
    {
        $builder = self::getBuilder($model, $map, $keyField, $orderFields, $orderDefault);
        $data    = $builder->select();
        return $data;
    }
}
