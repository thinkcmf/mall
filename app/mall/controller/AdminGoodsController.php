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
use app\mall\service\BrandService;
use app\mall\service\CategoryService;
use app\mall\service\GoodsService;

class AdminGoodsController extends AdminBaseController
{

    // 页面顶部 NavTabs 内容
    public $navTabs = [
        'index' => ['url' => 'index', 'name' => '商品列表'],
        'add'   => ['url' => 'add', 'name' => '添加商品'],
        'recycle' => ['url' => 'recycle', 'name' => '回收站'],
    ];

    /**
     * 商品管理
     * @adminMenu(
     *     'name'   => '商品管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '商品管理',
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

        // 获取数据
        $data = GoodsService::get();
        // 定义表格数据
        $table['data'] = $data;

        // 预设数据
        $brand = BrandService::getKv();
        $category = CategoryService::getKv();

        // 定义表头
        $table['thead'] = [
            ['field' => 'id',           'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'brand_id',     'name' => '品牌', 'type' => 'map', 'attr' => '', 'td_attr' => '', 'params' => $brand],
            ['field' => 'category_id',  'name' => '分类', 'type' => 'map', 'attr' => '', 'td_attr' => '', 'params' => $category],
            ['field' => 'title',        'name' => '名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'subtitle',     'name' => '副标题', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail',    'name' => '缩略图', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            ['field' => 'keywords',     'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_market_min', 'name' => '最低市价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_market_max', 'name' => '最高市价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_min',    'name' => '最低价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'price_max',    'name' => '最高价', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'is_top',       'name' => '置顶', 'type' => 'map', 'attr' => '', 'td_attr' => 'style="color:red;"', 'params' => [0 => '-', 1 => '<i class="fa fa-check"></i>']],
            ['field' => 'recommended',  'name' => '推荐', 'type' => 'map', 'attr' => '', 'td_attr' => 'style="color:red;"', 'params' => [0 => '-', 1 => '<i class="fa fa-check"></i>']],
            ['field' => 'is_new',       'name' => '新品', 'type' => 'map', 'attr' => '', 'td_attr' => 'style="color:red;"', 'params' => [0 => '-', 1 => '<i class="fa fa-check"></i>']],
            ['field' => 'is_hot',       'name' => '热卖', 'type' => 'map', 'attr' => '', 'td_attr' => 'style="color:red;"', 'params' => [0 => '-', 1 => '<i class="fa fa-check"></i>']],
            ['field' => 'status',       'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '下架', 1 => '出售中']],
        ];

        // 定义行操作
        $table['act_col'][] = ['name' => lang('EDIT'), 'type' => 'pop', 'url' => 'edit', 'params' => ['id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '描述', 'type' => 'pop', 'url' => 'admin_goods_detail/edit', 'params' => ['id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => 'SKU', 'type' => 'pop', 'url' => 'admin_goods_sku/index', 'params' => ['id'], 'map' => ['goods_id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '删除', 'type' => 'delete', 'url' => 'delete', 'params' => ['id'], 'class' => 'btn-danger'];

        // 定义排序操作
        $table['list_order'] = '排序';
        $table['batch'][]    = ['name' => '排序', 'url' => 'listOrder', 'params' => [], 'class' => 'btn-success'];
        // 定义批量操作
        $table['batch'][] = ['name' => '下架', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 0], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '上架', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 1], 'class' => 'btn-primary'];

        $table['batch'][] = ['name' => '设为置顶', 'url' => 'toggle', 'params' => ['field' => 'is_top', 'value' => 1], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '取消置顶', 'url' => 'toggle', 'params' => ['field' => 'is_top', 'value' => 0], 'class' => 'btn-primary'];

        $table['batch'][] = ['name' => '设为推荐', 'url' => 'toggle', 'params' => ['field' => 'recommended', 'value' => 1], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '取消推荐', 'url' => 'toggle', 'params' => ['field' => 'recommended', 'value' => 0], 'class' => 'btn-primary'];

        $table['batch'][] = ['name' => '设为新品', 'url' => 'toggle', 'params' => ['field' => 'is_new', 'value' => 1], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '取消新品', 'url' => 'toggle', 'params' => ['field' => 'is_new', 'value' => 0], 'class' => 'btn-primary'];

        $table['batch'][] = ['name' => '设为热卖', 'url' => 'toggle', 'params' => ['field' => 'is_hot', 'value' => 1], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '取消热卖', 'url' => 'toggle', 'params' => ['field' => 'is_hot', 'value' => 0], 'class' => 'btn-primary'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 商品回收站
     * @adminMenu(
     *     'name'   => '商品回收站',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '商品回收站',
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
        $data = GoodsService::get([], null, '', true);
        // 定义表格数据
        $table['data'] = $data;

        // 定义表头
        $table['thead'] = [
            ['field' => 'id', 'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'title', 'name' => '名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'subtitle', 'name' => '副标题', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail', 'name' => '缩略图', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            ['field' => 'keywords', 'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'status', 'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '下架', 1 => '出售中']],
        ];

        // 定义行操作
        // $table['act_col'][] = ['name' => lang('EDIT'), 'type' => 'pop', 'url' => 'edit', 'params' => ['id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '恢复', 'type' => 'dialog', 'msg' => '确定恢复商品？', 'url' => 'restore', 'params' => ['id'], 'class' => 'btn-danger'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 商品添加
     * @adminMenu(
     *     'name'   => '商品添加',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '商品添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $this->assign('before_form', '<h3>添加商品</h3>');

        // 获取表单配置
        $form = $this->form();

        // 赋值模板
        $this->assign('form', $form);

        // 返回渲染结果
        return $this->fetchPage('form');
    }

    /**
     * 商品编辑
     * @adminMenu(
     *     'name'   => '商品编辑',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '商品编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        if ($id > 0) {
            $model = new MallGoodsModel();
            $data  = $model->get($id);

            $title = '<h3>产品编辑：' . $data->title . '</h3>';
            $this->assign('before_nav_tabs', $title);

            $this->navTabs = [
                // 'index'     => ['url' => 'admin_goods\index', 'name' => '返回商品列表'],
                'goods'     => ['url' => 'admin_goods/edit', 'params' => ['id' => $id], 'name' => '基础信息'],
                'detail'    => ['url' => 'admin_goods_detail/edit', 'params' => ['id' => $id], 'name' => '商品描述'],
                'sku'       => ['url' => 'admin_goods_sku/index', 'params' => ['goods_id' => $id], 'name' => '商品SKU'],
                'add'       => ['url' => 'admin_goods_sku/add', 'params' => ['goods_id' => $id], 'name' => '添加SKU'],
            ];
            $this->assign('nav_tabs', $this->navTabs);
            $this->assign('active', 'goods');

            $form = $this->form($data);
            $this->assign('form', $form);

            return $this->fetchPage('form');
        } else {
            $this->error("商品ID错误！", url("admin_goods/index"));
        }
    }

    /**
     * 产品通用表单
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
        $brand = BrandService::getKv();
        $brand[0] = '请选择品牌';
        $form['item'][] = ['field' => 'brand_id',       'name' => '品牌', 'type' => 'select', 'default' => '0', 'option' => $brand, 'required' => false, 'help' => ''];

        $category = CategoryService::getKv('name', 'id', true);
        $category[0] = '请选择分类';
        $form['item'][] = ['field' => 'category_id',    'name' => '分类', 'type' => 'select', 'default' => '0', 'option' => $category, 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'title',          'name' => '商品名称', 'type' => 'text', 'default' => '', 'option' => [], 'required' => true, 'help' => '商品名必填的'];
        $form['item'][] = ['field' => 'subtitle',       'name' => '副标题', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'thumbnail',      'name' => '缩略图', 'type' => 'image', 'default' => '', 'option' => [], 'required' => true, 'help' => '上传缩略图'];
        $form['item'][] = ['field' => 'keywords',       'name' => '关键词', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'description',    'name' => '介绍描述', 'type' => 'textarea', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'thumbnails',     'name' => '幻灯主图', 'type' => 'images', 'default' => '', 'option' => ['max' => 5], 'required' => false, 'help' => '上传幻灯图'];

        $form['item'][] = ['field' => 'status',         'name' => '上架', 'type' => 'radio', 'default' => '0', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'is_top',         'name' => '置顶', 'type' => 'radio', 'default' => '0', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'recommended',    'name' => '推荐', 'type' => 'radio', 'default' => '0', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'is_new',         'name' => '新品', 'type' => 'radio', 'default' => '0', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'is_hot',         'name' => '热卖', 'type' => 'radio', 'default' => '0', 'option' => [0 => '否', 1 => '是'], 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'view_count',     'name' => '查看数', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'favorite_count', 'name' => '收藏数', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'like_count',     'name' => '点赞数', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'sold_count',     'name' => '销售数', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];

        // $form['item'][] = ['field' => 'content',        'name' => '详情', 'type' => 'richtext', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];

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
     * 商品提交
     * @adminMenu(
     *     'name'   => '商品提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '商品提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        $data   = $this->request->param();
        $model  = new MallGoodsModel();
        $result = $this->validate($data, 'Goods');
        if ($result !== true) {
            $this->error($result);
        }
        if (isset($data['id'])) {
            $model->allowField(true)->isUpdate(true)->save($data);
            $backurl = url('edit', ['id' => $data['id']]);
        } else {
            $model->allowField(true)->save($data);
            $backurl = url('index');
        }

        $this->success("保存成功！", $backurl);
    }

    /**
     * 删除
     * @adminMenu(
     *     'name'   => '删除商品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        // MallGoodsModel::destroy($id);
        $data['status'] = 0;
        $data['delete_time'] = time();
        MallGoodsModel::where('id', $id)->update($data);
        $this->success("删除成功！", url("index"));
    }

    /**
     * 恢复
     * @adminMenu(
     *     'name'   => '恢复商品',
     *     'parent' => 'recycle',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '恢复商品',
     *     'param'  => ''
     * )
     */
    public function restore()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data['status'] = 0;
        $data['delete_time'] = 0;
        MallGoodsModel::where('id', $id)->update($data);
        $this->success("恢复成功，请到调整商品上架状态！", url("index"));
    }

    /**
     * 商品排序
     * @adminMenu(
     *     'name'   => '商品排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $model = new MallGoodsModel();
        parent::listOrders($model);
        $this->success("排序更新成功！");
    }

    /**
     * 商品状态管理
     * @adminMenu(
     *     'name'   => '商品状态管理',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品状态管理',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $toggleFields = ['is_top', 'recommended', 'is_new', 'is_hot', 'status'];
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
