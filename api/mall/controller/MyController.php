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

use api\common\base\AuthBaseController;
use api\mall\service\CheckoutService;
use app\mall\model\ExpressFeeModel;
use think\Db;

class MyController extends AuthBaseController
{
    /**
     * 我的地址
     * @throws \think\exception\DbException
     */
    public function address()
    {
        $address = CheckoutService::getAddress($this->getUserId());

        $response = [];
        $response['list'] = $address;

        $this->success('请求成功!', $response);
    }

    /**
     * 获取收货地址区域
     */
    public function addressArea()
    {
        $level = $this->request->param('level', 0, 'intval');
        $pid = $this->request->param('pid', 0, 'intval');

        if ($pid > 0) {
            if ($level < 1) {
                $data = Db::name('area')->where('parent_id', $pid)->select();
                $response['list'] =  array_column($data->toArray(), 'name', 'id');
            } else {
                $data = Db::name('area')->field(['id', 'name', 'parent_id'])->select();

                $treeFun = function ($data, $pid = 0) use (&$treeFun) {
                    $tree = [];
                    foreach ($data as $k => $v) {
                        if ($v['parent_id'] == $pid) {
                            $row = [];
                            $row['id'] = $k;
                            $row['name'] = $v['name'];

                            $children = $treeFun($data, $v['id']);
                            if ($children) {
                                $row['children'] = $children;
                            }
                            $tree[] = $row;
                        }
                    }
                    return $tree;
                };

                $tree = $treeFun($data, $pid);
                $response['list'] = $tree;
            }
        } else {
            $data = ExpressFeeModel::all(function ($query) {
                $expressId = config('express_id');
                $query->field(['province', 'province_id']);
                $query->where('express_id', $expressId);
                $query->where('list_order', '<', 9999);
            });
            $response['list'] =  array_column($data->toArray(), 'province', 'province_id');
        }

        $this->success('物流配送地区', $response);
    }
}
