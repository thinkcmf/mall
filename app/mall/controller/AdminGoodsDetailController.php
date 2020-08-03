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
namespace app\mall\controller;

use app\mall\base\AdminBaseController;
use app\mall\model\MallGoodsModel;

class AdminGoodsDetailController extends AdminBaseController
{
    // 页面顶部 NavTabs 内容
    public $navTabs = [];

    protected $goods;

    protected function initialize()
    {
        $goods_id = $this->request->param('id', 0, 'intval');

        if ($goods_id < 1) {
            $this->error("商品ID为空！", url("admin_goods/index"));
        }

        $model = new MallGoodsModel();
        $goods  = $model->get($goods_id);
        if (empty($goods)) {
            $this->error("商品ID错误！", url("admin_goods/index"));
        }
        $this->goods_id = $goods_id;
        $this->goods = $goods;

        $title = '<h3>产品编辑：' . $this->goods->title . '</h3>';
        $this->assign('before_nav_tabs', $title);

        $this->navTabs = [
            // 'index'     => ['url' => 'admin_goods\index', 'name' => '返回商品列表'],
            'goods'     => ['url' => 'admin_goods/edit', 'params' => ['id' => $goods_id], 'name' => '基础信息'],
            'detail'    => ['url' => 'admin_goods_detail/edit', 'params' => ['id' => $goods_id], 'name' => '商品描述'],
            'sku'       => ['url' => 'admin_goods_sku/index', 'params' => ['goods_id' => $goods_id], 'name' => '商品SKU'],
            'add'       => ['url' => 'admin_goods_sku/add', 'params' => ['goods_id' => $goods_id], 'name' => '添加SKU'],
            'recycle'   => ['url' => 'admin_goods_sku/recycle', 'params' => ['goods_id' => $goods_id], 'name' => 'SKU回收站'],
        ];

        parent::initialize();
    }

    /**
     * 商品详情
     * @adminMenu(
     *     'name'   => '商品详情',
     *     'parent' => 'mall/AdminGoods/edit',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1100,
     *     'icon'   => '',
     *     'remark' => '商品详情',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $this->assign('active', 'detail');

        // 赋值模板
        $this->assign('goods', $this->goods);

        // 返回渲染结果
        $view_content = [];

        $view_content[] = $this->htmlBlock('nav_tabs');
        $view_content[] = $this->htmlBlock('detail', 'mall@admin_goods/');

        $this->assign('content', $view_content);
        return $this->fetchPage();
    }

    /**
     * 详情提交
     * @adminMenu(
     *     'name'   => '详情提交',
     *     'parent' => 'edit',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '详情提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        // 获取数据
        $file_path = $this->request->param('file_path', []);
        $title     = $this->request->param('title', []);
        $link      = $this->request->param('link', []);
        $type      = $this->request->param('type', []);

        $detail = [];
        for ($i = 0; $i < count($file_path); $i++) {
            $detail[] = [
                'file_path' => $file_path[$i],
                'title' => $title[$i],
                'link' => $link[$i],
                'type' => empty($file_path[$i]) ? 'text' : 'image'
            ];
        }

        $this->goods->allowField(true)->isUpdate(true)->save(['content' => $detail]);

        $this->success("保存成功！", url('edit', ['id' => $this->goods_id]), $detail);
    }
}
