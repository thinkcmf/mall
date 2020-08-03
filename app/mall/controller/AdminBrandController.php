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
use app\mall\model\MallBrandModel;

class AdminBrandController extends AdminBaseController
{
    // 页面顶部 NavTabs 内容
    public $navTabs = [
        'index' => ['url' => 'mall/AdminBrand/index', 'name' => '品牌列表'],
        'add'   => ['url' => 'mall/AdminBrand/add', 'name' => '添加品牌'],
    ];

    /**
     * 品牌管理
     * @adminMenu(
     *     'name'   => '品牌管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '品牌管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $table = [
            // 表头，单元对象，对象中的field必须是data中的数据字段，否则报错
            // field:字段， name:显示名， attr:标签属性， td_attr:列单元属性
            'thead'      => [],
            // 操作列对象： name:显示名， type:类型（jump/ajax/pop/delete）， url:连接地址， params:连接参数字段， class:css类
            'act_col'    => [],
            // 数据
            'data'       => [],
            // 批量排序
            'list_order' => '',
            // 批量操作
            'batch'      => [],
        ];

        // 获取数据
        $model = new MallBrandModel();
        $data  = $model->where('delete_time', 'eq', 0)->order('list_order')->select();

        // 定义表头
        $table['thead'] = [
            ['field' => 'id', 'name' => 'ID', 'type' => '', 'attr' => 'width="50"', 'td_attr' => ''],
            ['field' => 'name', 'name' => '品牌名称', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'alias', 'name' => '品牌别名', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'logo', 'name' => 'LOGO', 'type' => 'img', 'attr' => '', 'td_attr' => ''],
            ['field' => 'keywords', 'name' => '关键词', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'description', 'name' => '介绍', 'type' => '', 'attr' => '', 'td_attr' => ''],
            ['field' => 'status', 'name' => '状态', 'type' => 'switch', 'attr' => '', 'td_attr' => '', 'params' => [0 => '冻结', 1 => '激活']],
        ];

        // 定义行操作
        $table['act_col'] = [
            ['name' => lang('EDIT'), 'type' => 'link', 'url' => 'add', 'params' => ['id'], 'class' => 'btn-primary'],
            ['name' => '编辑(新TAB)', 'type' => 'jump', 'option'=>['title'=>'品牌'], 'url' => 'mall/admin_brand/add', 'params' => ['id'], 'class' => 'btn-primary'],
            ['name' => '编辑(弹窗)', 'type' => 'pop', 'url' => 'mall/admin_brand/add', 'params' => ['id'], 'class' => 'btn-primary'],
            ['name' => '删除', 'type' => 'delete', 'url' => 'mall/admin_brand/delete', 'params' => ['id'], 'class' => 'btn-danger'],
        ];

        // 定义表格数据
        $table['data'] = $data;

        // 定义排序操作
        $table['list_order'] = '排序';
        $table['batch'][]    = ['name' => '排序', 'url' => 'listOrder', 'params' => [], 'class' => 'btn-primary'];
        // 定义批量操作
        $table['batch'][] = ['name' => '冻结', 'url' => 'toggleStatus', 'params' => ['status' => 0], 'class' => 'btn-danger'];
        $table['batch'][] = ['name' => '激活', 'url' => 'toggleStatus', 'params' => ['status' => 1], 'class' => 'btn-primary'];

        // 赋值模板
        $this->assign('table', $table);

        // 返回渲染结果
        return $this->fetchPage('table');
    }

    /**
     * 品牌添加
     * @adminMenu(
     *     'name'   => '品牌添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '品牌添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $this->assign('before_form', '<h3>品牌表单</h3>');

        $id   = $this->request->param('id', 0, 'intval');
        $data = [];
        if ($id > 0) {
            $model = new MallBrandModel();
            $data  = $model->get($id);
        }

        $form = [
            'method'  => 'post',
            'action'  => 'post',
            'item'    => '',
            'data'    => '',
            'backurl' => 'index',
        ];

        $form['item'] = [
            // ['field'=>'', 'required'=>false, 'type'=>'text', 'option'=>[]],
            ['field' => 'name', 'type' => 'text', 'name' => '品牌名', 'default' => '', 'option' => [], 'required' => true, 'help' => '品牌名必填的'],
            ['field' => 'alias', 'type' => 'text', 'name' => '别名', 'default' => '', 'option' => [], 'required' => false, 'help' => ''],
            ['field' => 'logo', 'type' => 'image', 'name' => 'LOGO', 'default' => '', 'option' => [], 'required' => false, 'help' => '上传LOGO图片'],
            ['field' => 'keywords', 'type' => 'text', 'name' => '关键词', 'default' => '', 'option' => [], 'required' => false, 'help' => ''],
            ['field' => 'description', 'type' => 'textarea', 'name' => '简介', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => ''],
        ];
        $form['data'] = $data;

        // 赋值模板
        $this->assign('form', $form);

        // 返回渲染结果
        return $this->fetchPage('form');
    }

    /**
     * 品牌提交
     * @adminMenu(
     *     'name'   => '品牌提交',
     *     'parent' => 'add',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '品牌提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        $data   = $this->request->param();
        $model  = new MallBrandModel();
        $result = $this->validate($data, 'Brand');
        if ($result !== true) {
            $this->error($result);
        }
        if (isset($data['id'])) {
            $model->allowField(true)->isUpdate(true)->save($data);
        } else {
            $model->allowField(true)->save($data);
        }

        $this->success("保存成功！", url("index"));
    }

    /**
     * 删除
     * @adminMenu(
     *     'name'   => '删除品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除品牌',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        MallBrandModel::destroy($id);
        $this->success("删除成功！", url("index"));
    }

    /**
     * 品牌排序
     * @adminMenu(
     *     'name'   => '品牌排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $model = new MallBrandModel();
        parent::listOrders($model);
        $this->success("排序更新成功！");
    }

    /**
     * 品牌显示隐藏
     * @adminMenu(
     *     'name'   => '品牌显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggleStatus()
    {
        $data  = $this->request->param();
        $model = new MallBrandModel();

        if (isset($data['ids'])) {
            $ids    = $this->request->param('ids/a');
            $status = $data["status"] ? 1 : 0;
            $model->where('id', 'in', $ids)->update(['status' => $status]);
            $this->success("更新成功！" . $status);
        }
    }


    public function form()
    {
        // // 重定义顶部激活标签
        // $this->assign('active', 'add');

        $this->assign('before_form', '<h3>品牌表单</h3>');

        $form = [
            'method'  => 'post',
            'action'  => 'post',
            'item'    => '',
            'data'    => '',
            'backurl' => 'index',
        ];

        $selecter      = [];
        $selecter['a'] = '第A项';
        $selecter['b'] = '第B项';
        $selecter['c'] = '第c项';
        $selecter['d'] = '第d项';
        $form['item']  = [
            // ['field'=>'', 'required'=>false, 'type'=>'text', 'option'=>[]],
            ['field' => 'name', 'type' => 'text', 'name' => '品牌名', 'default' => '', 'option' => [], 'required' => true, 'help' => '品牌名必填的'],
            ['field' => 'alias', 'type' => 'text', 'name' => '别名', 'default' => '', 'option' => [], 'required' => false, 'help' => ''],
            ['field' => 'logo', 'type' => 'image', 'name' => 'LOGO', 'default' => '', 'option' => [], 'required' => false, 'help' => '上传LOGO图片'],
            ['field' => 'keywords', 'type' => 'text', 'name' => '关键词', 'default' => '', 'option' => [], 'required' => false, 'help' => ''],
            ['field' => 'description', 'type' => 'textarea', 'name' => '简介', 'default' => '', 'option' => 'rows="5"', 'required' => false, 'help' => '上传LOGO图片2'],
            ['field' => 'wwww', 'type' => 'select', 'name' => '选择器', 'default' => 'c', 'option' => $selecter, 'required' => false, 'help' => ''],
            ['field' => 'rddw', 'type' => 'radio', 'name' => '单选框', 'default' => 'b', 'option' => $selecter, 'required' => false, 'help' => ''],
            ['field' => 'rddw', 'type' => 'checkbox', 'name' => '单选框', 'default' => ['b', 'd'], 'option' => $selecter, 'required' => false, 'help' => ''],
        ];
        $form['data']  = [];

        // 赋值模板
        $this->assign('form', $form);

        // 返回渲染结果
        return $this->fetchPage('form');
    }
}
