<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\controller;

use app\mall\model\MallAttrModel;
use cmf\controller\AdminBaseController;
use app\mall\model\MallModelModel;

class AdminModelController extends AdminBaseController
{

    /**
     * 模型管理
     * @adminMenu(
     *     'name'   => '模型管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '模型管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('admin_model_index_view');

        if (!empty($content)) {
            return $content;
        }

        $modelModel = new MallModelModel();
        $models     = $modelModel->select();
        $this->assign('models', $models);

        return $this->fetch();
    }

    /**
     * 添加商品模型
     * @adminMenu(
     *     'name'   => '添加商品模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品模型',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加商品模型提交保存
     * @adminMenu(
     *     'name'   => '添加商品模型提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品模型提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data       = $this->request->param();
        $modelModel = new MallModelModel();

        $result = $this->validate($data, 'AdminModel');
        if ($result !== true) {
            $this->error($result);
        }

        $modelModel->allowField(true)->save($data);

        $this->success('添加成功！', url('AdminModel/edit', ['id' => $modelModel->id]));
    }

    /**
     * 编辑商品模型
     * @adminMenu(
     *     'name'   => '编辑商品模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品模型',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $modelModel = MallModelModel::get($id);
        $this->assign('model', $modelModel);

        $attrModel = new MallAttrModel();

        $attrs = $attrModel->where('model_id', $id)->select();
        $this->assign('attrs', $attrs);
        return $this->fetch();
    }

    /**
     * 编辑商品模型提交保存
     * @adminMenu(
     *     'name'   => '编辑商品模型提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品模型提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data       = $this->request->param();
        $modelModel = new MallModelModel();

        $result = $this->validate($data, 'AdminModel');
        if ($result !== true) {
            $this->error($result);
        }

        $modelModel->allowField(true)->isUpdate(true)->save($data);

        $this->success('保存成功！');
    }

    /**
     * 删除商品模型
     * @adminMenu(
     *     'name'   => '删除商品模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品模型',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        MallModelModel::destroy($id);

        $this->success('删除成功！', url('AdminModel/index'));
    }

    /**
     * 商品模型启用禁用
     * @adminMenu(
     *     'name'   => '商品模型 启用禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品模型 启用禁用',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data       = $this->request->param();
        $modelModel = new MallModelModel();

        if (isset($data['ids']) && !empty($data['display'])) {
            $ids = $this->request->param('ids/a');
            $modelModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success('更新成功！');
        }

        if (isset($data['ids']) && !empty($data['hide'])) {
            $ids = $this->request->param('ids/a');
            $modelModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success('更新成功！');
        }
    }

    /**
     * 商品模型属性列表
     * @adminMenu(
     *     'name'   => '商品模型属性列表',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品模型属性列表',
     *     'param'  => ''
     * )
     */
    public function attrs()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $modelModel = MallModelModel::get($id);
        $this->assign('model', $modelModel);

        $attrModel = new MallAttrModel();

        $attrs = $attrModel->where('model_id', $id)->select();
        $this->assign('attrs', $attrs);
        return $this->fetch();
    }


}