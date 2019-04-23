<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 <449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\controller;

use app\mall\model\MallBrandModel;
use cmf\controller\AdminBaseController;

class AdminBrandController extends AdminBaseController
{
    /**
     * 品牌管理
     * @adminMenu(
     *     'name'   => '品牌管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌管理',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $mallBrandModel = new MallBrandModel();
        $list           = $mallBrandModel->where('delete_time', 0)->paginate();
        $page           = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }


    /**
     * 设置品牌状态
     * @adminMenu(
     *     'name'   => '设置品牌启用状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置品牌启用状态',
     *     'param'  => ''
     * )
     */
    public function status()
    {
        $param          = $this->request->param();
        $mallBrandModel = new MallBrandModel();
        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');
            $mallBrandModel->where('id' ,'in', $ids)->update(['status' => 1]);
            $this->success("启用成功！");
        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');
            $mallBrandModel->where('id' ,'in', $ids)->update(['status' => 0]);
            $this->success("禁用成功！");
        }
    }

    /**
     * 新增品牌
     * @adminMenu(
     *     'name'   => '新增品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '新增品牌',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 新增品牌提交
     * @adminMenu(
     *     'name'   => '新增品牌提交',
     *     'parent' => 'add',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '新增品牌提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $param    = $this->request->param();
            $validate = $this->validate($param, 'AdminBrand');
            if (true !== $validate) {
                $this->error($validate);
            }
            $mallBrandModel = new MallBrandModel();
            $result         = $mallBrandModel->addBrand($param);
            if ($result) {
                $this->success('品牌新增成功');
            } else {
                $this->error('品牌新增失败');
            }
        }
    }

    /**
     * 编辑品牌
     * @adminMenu(
     *     'name'   => '编辑品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑品牌',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            $this->error('该品牌不存在');
        }
        $mallBrandModel = new MallBrandModel();
        $brand          = $mallBrandModel->where('id', $id)->find();
        $this->assign('brand', $brand);
        return $this->fetch();
    }

    /**
     * 编辑品牌提交
     * @adminMenu(
     *     'name'   => '编辑品牌提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑品牌提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $param    = $this->request->param();
            $validate = $this->validate($param, 'AdminBrand');
            if (true !== $validate) {
                $this->error($validate);
            }
            $mallBrandModel = new MallBrandModel();
            $result         = $mallBrandModel->editBrand($param);
            if ($result) {
                $this->success('品牌编辑成功');
            } else {
                $this->error('品牌编辑失败');
            }
        }
    }

    /**
     * 删除品牌
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
        if (empty($id)) {
            $this->error('id不存在');
        }
        $mallBrandModel = new MallBrandModel();
        $result         = $mallBrandModel->editBrand(['id' => $id, 'delete_time' => time()]);
        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }
}
