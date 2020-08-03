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
use app\mall\model\ExpressModel;
use app\mall\service\ExpressService;

class AdminExpressController extends AdminBaseController
{

    // 页面顶部 NavTabs 内容
    public $navTabs = [
        'index' => ['url' => 'index', 'name' => '物流列表'],
        'add'   => ['url' => 'add', 'name' => '添加物流'],
        // 'recycle' => ['url' => 'recycle', 'name' => '回收站'],
    ];

    /**
     * 物流管理
     * @adminMenu(
     *     'name'   => '物流管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '物流管理',
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
        $data = ExpressService::get();
        // 定义表格数据
        $table['data'] = $data;

        // 定义表头
        $table['thead'] = [
            ['field' => 'id',           'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'name',         'name' => '名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'alias',        'name' => '别名', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'thumbnail',    'name' => '缩略图', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            ['field' => 'code',         'name' => '物流公司代码', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'type',         'name' => '物流类型', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'base_weight',  'name' => '默认首重(KG)', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'base_fee',     'name' => '默认首重费用', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'next_weight',  'name' => '默认续重(KG)', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'next_fee',     'name' => '默认续重运费', 'type' => '', 'attr' => '', 'td_attr' => ''],
            // ['field' => 'keywords',     'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'status',       'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '冻结', 1 => '激活']],
        ];

        // 定义行操作
        $table['act_col'][] = ['name' => lang('EDIT'), 'type' => 'pop', 'url' => 'edit', 'params' => ['id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '地区费用', 'type' => 'pop', 'url' => 'admin_express_fee/index', 'params' => ['id'], 'map' => ['express_id'], 'class' => 'btn-primary'];
        $table['act_col'][] = ['name' => '删除', 'type' => 'delete', 'url' => 'delete', 'params' => ['id'], 'class' => 'btn-danger'];

        // 定义排序操作
        $table['list_order'] = '排序';
        $table['batch'][]    = ['name' => '排序', 'url' => 'listOrder', 'params' => [], 'class' => 'btn-success'];
        // 定义批量操作
        $table['batch'][] = ['name' => '冻结', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 0], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '激活', 'url' => 'toggle', 'params' => ['field' => 'status', 'value' => 1], 'class' => 'btn-primary'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 物流回收站
     * @adminMenu(
     *     'name'   => '物流回收站',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '物流回收站',
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
        $data = ExpressService::get([], null, '', true);
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
        $table['act_col'][] = ['name' => '恢复', 'type' => 'dialog', 'msg'=>'确定恢复物流？', 'url' => 'restore', 'params' => ['id'], 'class' => 'btn-danger'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 物流添加
     * @adminMenu(
     *     'name'   => '物流添加',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '物流添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $this->assign('before_form', '<h3>添加物流</h3>');

        // 获取表单配置
        $form = $this->form();

        // 赋值模板
        $this->assign('form', $form);

        // 返回渲染结果
        return $this->fetchPage('form');
    }

    /**
     * 物流编辑
     * @adminMenu(
     *     'name'   => '物流编辑',
     *     'parent' => 'post',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '物流编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        if ($id > 0) {
            $model = new ExpressModel();
            $data  = $model->get($id);

            $title = '<h3>编辑：' . $data->name . '</h3>';
            $this->assign('before_nav_tabs', $title);

            $this->navTabs = [
                // 'index'     => ['url' => 'admin_express\index', 'name' => '返回物流列表'],
                'express'     => ['url' => 'adminExpressModel/edit', 'params' => ['id' => $id], 'name' => '基础信息'],
                'fee'       => ['url' => 'admin_express_fee/index', 'params' => ['express_id' => $id], 'name' => '物流资费'],
                // 'add'       => ['url' => 'admin_express_fee/add', 'params' => ['express_id' => $id], 'name' => '添加资费'],
            ];
            $this->assign('nav_tabs', $this->navTabs);
            // $this->assign('nav_tabs', null);
            $this->assign('active', 'express');

            $form = $this->form($data);
            $this->assign('form', $form);

            return $this->fetchPage('form');
        } else {
            $this->error("物流ID错误！", url("admin_express/index"));
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
        $form['item'][] = ['field' => 'name',          'name' => '名称', 'type' => 'text', 'default' => '', 'option' => [], 'required' => true, 'help' => '物流名必填的'];
        $form['item'][] = ['field' => 'alias',          'name' => '别名', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'code',           'name' => '物流公司代码', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'thumbnail',      'name' => '缩略图', 'type' => 'image', 'default' => '', 'option' => [], 'required' => false, 'help' => '上传缩略图'];
        $form['item'][] = ['field' => 'keywords',       'name' => '关键词', 'type' => 'text', 'default' => '', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'description',    'name' => '介绍描述(前台展示)', 'type' => 'textarea', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'remark',         'name' => '备注说明(后台)', 'type' => 'textarea', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'type',           'name' => '类型', 'type' => 'textarea', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''];

        $form['item'][] = ['field' => 'base_weight',    'name' => '默认首重(KG)', 'type' => 'text', 'default' => '1.000', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'base_fee',       'name' => '默认首重运费', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'next_weight',    'name' => '默认续重(KG)', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        $form['item'][] = ['field' => 'next_fee',       'name' => '默认续重运费', 'type' => 'text', 'default' => '0', 'option' => [], 'required' => false, 'help' => ''];
        
        $form['item'][] = ['field' => 'status',         'name' => '状态', 'type' => 'radio', 'default' => '1', 'option' => [0 => '冻结', 1 => '开启'], 'required' => false, 'help' => ''];

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
     * 物流提交
     * @adminMenu(
     *     'name'   => '物流提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '物流提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        $data   = $this->request->param();
        $model  = new ExpressModel();
        $result = $this->validate($data, 'Express');
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
     *     'name'   => '删除物流',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除物流',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        // ExpressModel::destroy($id);
        $data['status'] = 0;
        $data['delete_time'] = time();
        ExpressModel::where('id', $id)->update($data);
        $this->success("删除成功！", url("index"));
    }

    /**
     * 恢复
     * @adminMenu(
     *     'name'   => '恢复物流',
     *     'parent' => 'recycle',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '恢复物流',
     *     'param'  => ''
     * )
     */
    public function restore()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data['status'] = 0;
        $data['delete_time'] = 0;
        ExpressModel::where('id', $id)->update($data);
        $this->success("恢复成功，请到调整物流上架状态！", url("index"));
    }

    /**
     * 物流排序
     * @adminMenu(
     *     'name'   => '物流排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $model = new ExpressModel();
        parent::listOrders($model);
        $this->success("排序更新成功！");
    }

    /**
     * 物流状态管理
     * @adminMenu(
     *     'name'   => '物流状态管理',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流状态管理',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $toggleFields = ['is_top', 'recommended', 'is_new', 'is_hot', 'status'];
        $data  = $this->request->param();
        $model = new ExpressModel();

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
