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
use app\mall\model\MallAttrValueModel;
use app\mall\model\MallItemAttrModel;
use cmf\controller\AdminBaseController;
use app\mall\model\MallModelModel;

class AdminAttrController extends AdminBaseController
{
    /**
     * 属性管理
     * @adminMenu(
     *     'name'   => '属性管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '属性管理',
     *     'param'  => ''
     * )
     * @return mixed
     */
    public function index(){

    }

    /**
     * 添加商品模型属性提交保存
     * @adminMenu(
     *     'name'   => '添加商品模型属性提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品模型属性提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data         = $this->request->param();
        $data['type'] = 1;

        $result = $this->validate($data, 'AdminAttr');
        if ($result !== true) {
            $this->error($result);
        }

        $attrModel = new MallAttrModel();
        $attrModel->save($data);

        $this->success('添加成功！', null, ['id' => $attrModel->id]);
    }

    /**
     * 编辑商品模型属性提交保存
     * @adminMenu(
     *     'name'   => '编辑商品模型属性提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品模型属性提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data         = $this->request->param();
        $data['type'] = 1;

        $result = $this->validate($data, 'AdminAttr');
        if ($result !== true) {
            $this->error($result);
        }

        $attrModel = new MallAttrModel();
        $attrModel->save($data, ['id' => intval($data['id'])]);

        $this->success('保存成功！', null, ['id' => $attrModel->id]);
    }

    /**
     * 删除商品模型属性
     * @adminMenu(
     *     'name'   => '删除商品模型属性',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品模型属性',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');

        $itemAttrModel = new MallItemAttrModel();

        $findItemAttr = $itemAttrModel->where('attr_id', $id)->find();

        if (!empty($findItemAttr)) {
            $this->error('此属性已经在使用，无法删除！');
        }

        MallAttrModel::destroy($id);

        $this->success('删除成功！', null);
    }

    /**
     * 商品模型属性启用禁用
     * @adminMenu(
     *     'name'   => '商品模型属性 启用禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品模型属性 启用禁用',
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
     * 商品模型属性排序
     * @adminMenu(
     *     'name'   => '商品模型属性排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品模型属性排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $attrModel = new  MallAttrModel();
        parent::listOrders($attrModel);
        $this->success("排序更新成功！");
    }

    /**
     * 添加商品模型属性值提交保存
     * @adminMenu(
     *     'name'   => '添加商品模型属性值提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品模型属性值提交保存',
     *     'param'  => ''
     * )
     */
    public function addValuePost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminAttrValue');
        if ($result !== true) {
            $this->error($result);
        }

        $attrValueModel = new MallAttrValueModel();
        $attrValueModel->save($data);

        $this->success('添加成功！', null, ['id' => $attrValueModel->id]);

    }

    /**
     * 编辑商品模型属性值提交保存
     * @adminMenu(
     *     'name'   => '编辑商品模型属性值提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品模型属性值提交保存',
     *     'param'  => ''
     * )
     */
    public function editValuePost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminAttrValue');
        if ($result !== true) {
            $this->error($result);
        }

        $attrValueModel = new MallAttrValueModel();
        $attrValueModel->save($data, ['id' => intval($data['id'])]);

        $this->success('保存成功！', null, ['id' => $attrValueModel->id]);

    }

    /**
     * 删除商品模型属性值
     * @adminMenu(
     *     'name'   => '删除商品模型属性值',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品模型属性值',
     *     'param'  => ''
     * )
     */
    public function deleteValue()
    {
        $id = $this->request->param('id', 0, 'intval');

        $itemAttrModel = new MallItemAttrModel();

        $findItemAttr = $itemAttrModel->where('attr_value_id', $id)->find();

        if (!empty($findItemAttr)) {
            $this->error('此属性值已经在使用，无法删除！');
        }

        MallAttrValueModel::destroy($id);

        $this->success('删除成功！');
    }

    /**
     * 商品模型属性值排序
     * @adminMenu(
     *     'name'   => '商品模型属性值排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品模型属性值排序',
     *     'param'  => ''
     * )
     */
    public function valueListOrder()
    {
        $attrValueModel = new  MallAttrValueModel();
        parent::listOrders($attrValueModel);
        $this->success("排序更新成功！");
    }

}