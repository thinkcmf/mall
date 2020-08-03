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
use api\mall\service\GoodsService;

class GoodsController extends ApiBaseController
{
    /**
     * 列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $type = $this->request->param('type');
        $category_id = $this->request->param('category_id', 0, 'intval');
        $brand_id = $this->request->param('brand_id', 0, 'intval');
        $limit = $this->request->param('num', 'paginate', 'intval');

        $data = GoodsService::goodsList($type, $category_id, $brand_id,$limit);

        $response = ['list' => $data];
        $this->success('请求成功!', $response);
    }

    /**
     * 获取指定的商品
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的商品id！');
        } else {
            $goods = GoodsService::goods($id);
            if (!empty($goods['error'])) {
                $this->error($goods['error']);
            } else {
                $this->success('请求成功!', $goods['data']);
            }
        }
    }
}
