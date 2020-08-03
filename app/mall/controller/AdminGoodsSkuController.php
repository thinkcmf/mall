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
use app\mall\model\MallGoodsSkuModel;
use app\mall\service\BrandService;
use app\mall\service\CategoryService;
use app\mall\service\GoodsService;

class AdminGoodsSkuController extends AdminBaseController
{

    // 页面顶部 NavTabs 内容
    public $navTabs = [];

    protected $goods;

    protected function initialize()
    {

        $goods_id = $this->request->param('goods_id', 0, 'intval');

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
            'sku'       => ['url' => 'index', 'params' => ['goods_id' => $goods_id], 'name' => '商品SKU'],
            'add'       => ['url' => 'add', 'params' => ['goods_id' => $goods_id], 'name' => '添加SKU'],
            'recycle'   => ['url' => 'recycle', 'params' => ['goods_id' => $goods_id], 'name' => 'SKU回收站'],
        ];

        parent::initialize();
    }

    /**
     * SKU管理
     * @adminMenu(
     *     'name'   => 'SKU管理',
     *     'parent' => 'mall/AdminGoods/index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1100,
     *     'icon'   => '',
     *     'remark' => 'SKU管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->assign('active', 'sku');

        $table = [
            'thead'      => [],
            'act_col'    => [],
            'data'       => [],
            'list_order' => '',
            'batch'      => [],
        ];

        // 获取数据
        $data = GoodsService::getSku($this->goods_id);

        // 定义表格数据
        $table['data'] = $data;

        // 定义表头
        $table['thead'] = [
            ['field' => 'id',           'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'title',        'name' => '名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'subtitle',     'name' => '副标题', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail',    'name' => '缩略图', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            // ['field' => 'keywords',     'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_market', 'name' => '市场参考价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price',        'name' => '售价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sn',           'name' => '货号', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'barcode',      'name' => '条形码', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'stock',        'name' => '库存', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sold_count',   'name' => '累计售出', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'shipfee',      'name' => '单件运费', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'weight',       'name' => '重量(KG)', 'type' => '', 'attr' => '', 'td_attr' => ''],

            ['field' => 'status',      'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '下架', 1 => '出售中']],
        ];

        // 定义行操作
        $table['act_col'][] = ['name' => lang('EDIT'), 'type' => 'pop', 'url' => 'edit', 'params' => ['goods_id', 'id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '删除', 'type' => 'delete', 'url' => 'delete', 'params' => ['goods_id', 'id'], 'class' => 'btn-danger'];
        // $table['act_col'][] = ['name' => '添加SKU', 'type' => 'pop', 'url' => 'add', 'params' => ['goods_id',], 'class' => 'btn-primary'];

        // 定义排序操作
        $table['list_order'] = '排序';
        $table['batch'][]    = ['name' => '排序', 'url' => 'listOrder', 'params' => ['goods_id' => $this->goods_id], 'class' => 'btn-success'];
        // 定义批量操作
        $table['batch'][] = ['name' => '下架', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 0, 'goods_id' => $this->goods_id], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '上架', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 1, 'goods_id' => $this->goods_id], 'class' => 'btn-primary'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        $view_content = [];

        // $view_content[] = $this->goods->title;

        $view_content[] = $this->htmlBlock('nav_tabs');
        $view_content[] = $this->htmlBlock('grid');

        $this->assign('content', $view_content);
        return $this->fetchPage();
    }

    /**
     * SKU回收站
     * @adminMenu(
     *     'name'   => 'SKU回收站',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'SKU回收站',
     *     'param'  => ''
     * )
     */
    public function recycle()
    {
        $table = [
            'thead'      => [],
            'act_col'    => [],
            'data'       => [],
            'list_order' => '',
            'batch'      => [],
        ];

        // 获取数据
        $data = GoodsService::getSku($this->goods_id, [], null, '', true);
        // 定义表格数据
        $table['data'] = $data;

        // 定义表头
        $table['thead'] = [
            ['field' => 'id',           'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'title',        'name' => '名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'subtitle',     'name' => '副标题', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail',    'name' => '缩略图', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            // ['field' => 'keywords',     'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_market', 'name' => '市场参考价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price',        'name' => '售价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sn',           'name' => '货号', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'barcode',      'name' => '条形码', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'stock',        'name' => '库存', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'sold_count',   'name' => '累计售出', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'shipfee',      'name' => '单件运费', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'weight',       'name' => '重量(KG)', 'type' => '', 'attr' => '', 'td_attr' => ''],

            ['field' => 'status',      'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '下架', 1 => '出售中']],
        ];


        // 定义行操作
        $table['act_col'][] = ['name' => '恢复', 'type' => 'dialog', 'msg' => '确定恢复此SKU？', 'url' => 'restore', 'params' => ['goods_id', 'id'], 'class' => 'btn-danger'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * SKU添加
     * @adminMenu(
     *     'name'   => 'SKU添加',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => 'SKU添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $this->assign('before_form', '<h3>添加SKU</h3>');

        // 获取表单配置
        $form = $this->form();

        // 赋值模板
        $this->assign('form', $form);

        // 返回渲染结果
        return $this->fetchPage('form');
    }

    /**
     * SKU编辑
     * @adminMenu(
     *     'name'   => 'SKU编辑',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => 'SKU编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        if ($id > 0) {
            $model = new MallGoodsSkuModel();
            $data  = $model->get($id);

            $this->assign('nav_tabs', null);

            $form = $this->form($data);
            $this->assign('form', $form);

            return $this->fetchPage('form');
        } else {
            $this->error("商品SKU ID错误！", url("index"));
        }
    }

    /**
     * SKU通用表单
     */
    protected function form($data = null, $buildOnly = true)
    {
        $form = [
            'method'  => 'post',
            'action'  => 'post',
            'item'    => [],
            'data'    => '',
            'backurl' => 'index',
        ];

        // 字段表单
        $form['item'][] = ['field' => 'goods_id',       'name' => '商品ID', 'type' => 'hidden', 'default' => $this->goods->id, 'option' => [], 'required' => true, 'help' => ''];
        $form['item'][] = ['field' => 'title',          'name' => 'SKU名称', 'type' => 'text', 'default' => '', 'option' => [], 'required' => true, 'help' => 'SKU名称必填的'];
        $form['item'][] = ['field' => 'subtitle',       'name' => '副标题', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'thumbnail',      'name' => '缩略图', 'type' => 'image', 'default' => '', 'option' => [], 'required' => false, 'help' => '上传缩略图'];
        $form['item'][] = ['field' => 'keywords',       'name' => '关键词', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'description',    'name' => '介绍描述', 'type' => 'textarea', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'price_market',   'name' => '市场参考价', 'type' => 'text', 'default' => '0.00', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'price',          'name' => '售价', 'type' => 'text', 'default' => '', 'option' => [], 'required' => true, 'help' => ''];

        $form['item'][] = ['field' => 'sn',             'name' => '货号', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'barcode',        'name' => '条形码', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'stock',          'name' => '库存', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'shipfee',        'name' => '单件运费(元)', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'weight',         'name' => '重量(KG)', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'status',        'name' => '上架', 'type' => 'radio', 'default' => '1', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];

        // 数据赋值
        if ($data) {
            $form['data'] = $data;
            $form['backurl'] = '';
        }

        if ($buildOnly) {
            return $form;
        } else {
            // 赋值模板
            $this->assign('form', $form);
            return $this->htmlBlock('form');
        }
    }

    /**
     * SKU提交
     * @adminMenu(
     *     'name'   => 'SKU提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => 'SKU提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        $data   = $this->request->param();
        $model  = new MallGoodsSkuModel();
        $result = $this->validate($data, 'GoodsSku');
        if ($result !== true) {
            $this->error($result);
        }
        if (isset($data['id'])) {
            $model->allowField(true)->isUpdate(true)->save($data);
        } else {
            $model->allowField(true)->save($data);
        }

        GoodsService::priceRefresh($this->goods_id);

        $this->success("保存成功！", url('index', ['goods_id' => $this->goods_id]));
    }

    /**
     * 删除SKU
     * @adminMenu(
     *     'name'   => '删除SKU',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除SKU',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        // MallGoodsSkuModel::destroy($id);
        $data['status'] = 0;
        $data['delete_time'] = time();
        MallGoodsSkuModel::where('id', $id)->update($data);

        GoodsService::priceRefresh($this->goods_id);

        $this->success("删除成功！", url('index', ['goods_id' => $this->goods_id]));
    }

    /**
     * 恢复SKU
     * @adminMenu(
     *     'name'   => '恢复SKU',
     *     'parent' => 'recycle',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '恢复SKU',
     *     'param'  => ''
     * )
     */
    public function restore()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data['status'] = 0;
        $data['delete_time'] = 0;
        MallGoodsSkuModel::where('id', $id)->update($data);

        GoodsService::priceRefresh($this->goods_id);

        $this->success("恢复成功，请调整SKU上架状态！", url('index', ['goods_id' => $this->goods_id]));
    }

    /**
     * SKU排序
     * @adminMenu(
     *     'name'   => 'SKU排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'SKU排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $model = new MallGoodsSkuModel();
        parent::listOrders($model);
        $this->success("排序更新成功！", url('index', ['goods_id' => $this->goods_id]));
    }

    /**
     * SKU状态管理
     * @adminMenu(
     *     'name'   => 'SKU状态管理',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'SKU状态管理',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $toggleFields = ['status'];
        $data  = $this->request->param();
        $model = new MallGoodsSkuModel();

        if (isset($data['ids'])) {
            $ids    = $this->request->param('ids/a');
            $field = $data["field"];
            $value = $data["value"];
            if (in_array($field, $toggleFields)) {
                $model->where('id', 'in', $ids)->update([$field => $value]);
            }

            GoodsService::priceRefresh($this->goods_id);

            $this->success("更新成功！", url('index', ['goods_id' => $this->goods_id]));
        }
    }
}
