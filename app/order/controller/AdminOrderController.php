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
namespace app\order\controller;

use app\mall\service\ExpressService;
use app\order\base\AdminBaseController;
use app\mall\model\MallGoodsModel;
use app\mall\service\BrandService;
use app\mall\service\CategoryService;
use app\mall\service\GoodsService;
use app\order\model\OrderModel;
use app\order\service\OrderService;

class AdminOrderController extends AdminBaseController
{

    protected function initialize()
    {
        // 页面顶部 NavTabs 内容
        $process_map = OrderModel::$process_map;
        $navTabs['index'] = ['url' => 'index', 'name' => '全部订单'];
        foreach ($process_map as $key => $val) {
            $navTabs[$key] = ['url' => 'index', 'params' => ['process' => $key], 'name' => $val];
        }
        $this->navTabs = $navTabs;

        parent::initialize();
    }

    /**
     * 订单列表
     * @adminMenu(
     *     'name'   => '订单列表',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $table = [
            'thead'      => [],
            'act_col'    => [],
            'data'       => [],
            'list_order' => '',
            'batch'      => [],
        ];

        // 构建查询条件
        $map = $this->buildQuery();

        // 获取数据
        $data = [];
        OrderService::get($map)->each(function ($item) use (&$data) {
            // $data[$item->id] = $item->toArray();
            $user = $item->user;
            $item->user_name = $user->user_nickname;
            $item->create_time = date('Y-m-d H:i:s', $item['create_time']);
            if ($item->pay_time > 0) {
                $item->pay_time = date('Y-m-d H:i:s', $item['pay_time']);
            } else {
                $item->pay_time = '<span style="color:red;">未支付</span>';
            }
            // $item->pay_status_ = $user->user_nickname;
            $data[$item->id] = $item;
        });

        // 定义表格数据
        $table['data'] = $data;

        // 定义表头
        $table['thead'] = [
            ['field' => 'id',           'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'flag',         'name' => '标记', 'type' => 'flag', 'attr' => '', 'td_attr' => '', 'params' => ['icon' => 'flag']],
            ['field' => 'sn',           'name' => '订单号', 'type' => '', 'attr' => '', 'td_attr' => ''],
            // ['field' => 'user_id',   'name' => '用户ID', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'user_name',    'name' => '用户名', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'consignee',    'name' => '收件人', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'phone',        'name' => '联系电话', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'city',         'name' => '城市', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'amount_payable',      'name' => '订单金额', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'create_time',  'name' => '下单时间', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'pay_time',     'name' => '支付时间', 'type' => '', 'attr' => '', 'td_attr' => ''],
            // ['field' => 'pay_status',      'name' => '支付状态', 'type' => 'map', 'attr' => '', 'td_attr' => 'style="color:red;"', 'params' => [0 => '未支付', 1 => '<i class="fa fa-check"></i>']],

            // ['field' => 'process',      'name' => '订单进程', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'process_text',      'name' => '订单进程', 'type' => '', 'attr' => '', 'td_attr' => ''],

            ['field' => 'status',       'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '关闭', 1 => '正常']],
        ];

        // 定义行操作
        $table['act_col'][] = ['name' => '订单详情', 'type' => 'pop', 'url' => 'details', 'params' => ['id'], 'class' => 'btn-primary'];
        // $table['act_col'][] = ['name' => '订单详情', 'type' => 'jump', 'option'=>['title'=>'订单'], 'url' => 'details', 'params' => ['id'], 'class' => 'btn-primary'];

        // // 定义排序操作

        // 赋值模板
        $this->assign('table', $table);

        // TODO：分页啊，分页啊

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 查询构建器
     */
    protected function buildQuery()
    {
        $map = [];
        $process  = $this->request->param('process');
        if (array_key_exists($process, $this->navTabs)) {
            $this->assign('active', $process);
            $map = OrderService::buildProcessQuery($process);
        }

        $status  = $this->request->param('status');
        if (is_numeric($status)) {
            $map[] = ['status', '=', $status > 0 ? 1 : 0];
        }

        // TODO：关键字搜索等

        return $map;
    }

    /**
     * 订单详情
     * @adminMenu(
     *     'name'   => '订单详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '',
     *     'param'  => ''
     * )
     */
    public function details()
    {
        $id = $this->request->param('id', 0, 'intval');

        if ($id > 0) {
            $model = new OrderModel();

            // TODO：预加载相关模型数据
            $order  = $model->get($id);

            $url = url('details', ['id' => $id]);
            $title = '<h3>订单号：' . $order->sn . '（ID：' . $order->id . '）<a href="' . $url . '" target="_blank"><i class="fa fa-external-link"></i></a></h3>';
            $this->assign('before_nav_tabs', $title);
            // $this->assign('nav_tabs', null);
            //物流
            $express = ExpressService::get();
            if(false === hook_one('express_channel')){
                $express = [];//插件未安装
            }
            $this->assign('express', $express);
            // 赋值模板
            $this->assign('order', $order);
            $this->assign('item_table', $this->detail_item_table($order->item));

            // TODO：定义订单详情内容和操作
            $view_content = [];
            $view_content[] = $this->htmlBlock('details', 'order@admin_order/');

            $this->assign('content', $view_content);

            // 返回渲染结果
            return $this->fetchPage();
        } else {
            $this->error("ID错误！", url("index"));
        }
    }

    protected function detail_item_table($item)
    {
        $table = [
            'thead'      => [],
            'act_col'    => [],
            'data'       => [],
            'list_order' => '',
            'batch'      => [],
        ];

        $item->each(function (&$item) {
            $item->amount = $item->quantity * $item->price;
            $item->brand_name = $item->brand ? $item->brand->name : '';
        });

        // 定义表格数据
        $table['data'] = $item;

        // 定义表头
        $table['thead'] = [
            ['field' => 'goods_id',         'name' => '商品ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'goods_title',      'name' => '商品名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail',        'name' => '商品图片', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sku_id',           'name' => 'SKU_ID', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sku_title',        'name' => 'SKU', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'brand_name',       'name' => '品牌', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'original_price',   'name' => '原价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price',            'name' => '售价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'quantity',         'name' => '数量', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'amount',           'name' => '小记', 'type' => '', 'attr' => '', 'td_attr' => ''],
        ];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->htmlBlock('grid');
    }

    /**
     * 订单状态管理
     * @adminMenu(
     *     'name'   => '订单状态管理',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单状态管理',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $toggleFields = ['status'];
        $data  = $this->request->param();
        $model = new MallGoodsModel();

        if (isset($data['ids'])) {
            $ids    = $this->request->param('ids/a');
            $field = $data["field"];
            $value = $data["value"];
            if (in_array($field, $toggleFields)) {
                $model->where('id', 'in', $ids)->update([$field => $value]);
            }
            $this->success("状态更新成功！");
        }
    }
}
