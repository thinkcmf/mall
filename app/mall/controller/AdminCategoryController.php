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

use app\mall\model\MallBrandModel;
use app\mall\model\MallAttrModel;
use app\mall\model\MallCategoryModel;
use app\mall\model\MallModelModel;
use cmf\controller\AdminBaseController;
use think\Db;
use tree\Tree;

class AdminCategoryController extends AdminBaseController
{

    /**
     * 商品分类管理
     * @adminMenu(
     *     'name'   => '商品分类',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $mallCategoryModel = new MallCategoryModel();
        $categories        = $mallCategoryModel->getMallCategories();
        $this->assign('categories', $categories);
        return $this->fetch();
    }

    /**
     * 新建商品分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $parentId = $this->request->param('parent', 0, 'intval');
        //分类树状
        $mallCategoryModel = new MallCategoryModel();
        $categoriesTree    = $mallCategoryModel->mallCategoryTree($parentId);
        //商品模型
        $mallModel  = new MallModelModel();
        $mallModels = $mallModel->where('status', 1)->select();

        $this->assign('mall_models', $mallModels);
        $this->assign('categories_tree', $categoriesTree);

        return $this->fetch();
    }

    /**
     * @throws \think\exception\PDOException
     */
    public function addPost()
    {
        $param             = $this->request->param();
        $mallCategoryModel = new MallCategoryModel();
        $result            = $this->validate($param, 'MallCategory');
        if ($result !== true) {
            $this->error($result);
        }

        $result = $mallCategoryModel->addCategory($param);
        if ($result) {
            $this->success('新建商品分类成功', 'index');
        } else {
            $this->error('新建商品分类失败');
        }
    }

    /**
     * 编辑商品分类
     * @adminMenu(
     *     'name'   => '编辑商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (empty($id)) {
            $this->error('操作错误!');
        }
        $mallCategoryModel = new MallCategoryModel();
        $category          = $mallCategoryModel::get($id)->toArray();
        $categoriesTree    = $mallCategoryModel->mallCategoryTree($category['parent_id'], $id);
        //商品模型
        $mallModel  = new MallModelModel();
        $mallModels = $mallModel->where('status', 1)->select();
        $this->assign('category', $category);
        $this->assign('mall_models', $mallModels);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();

    }

    /**
     * 编辑商品分类提交
     * @adminMenu(
     *     'name'   => '编辑商品分类提交',
     *     'parent' => 'edit',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类提交',
     *     'param'  => ''
     * )
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editPost()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'MallCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $mallCategoryModel = new MallCategoryModel();
        $result            = $mallCategoryModel->editCategory($data);
        if ($result) {
            $this->success('编辑商品分类成功');
        } else {
            $this->error('编辑商品分类失败');
        }
    }

    /**
     * 设置商品分类启用状态
     * @adminMenu(
     *     'name'   => '设置商品分类启用状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置商品分类启用状态',
     *     'param'  => ''
     * )
     */
    public function setStatusOn()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (empty($id)) {
            $this->error('状态更改操作失败');
        }
        $mallCategoryModel = new MallCategoryModel();
        $result            = $mallCategoryModel->where('id', $id)->update(['status' => 1]);
        if ($result) {
            $this->success('状态更改操作成功');
        } else {
            $this->error('状态更改操作失败');
        }

    }

    /**
     * 设置商品分类禁用状态
     * @adminMenu(
     *     'name'   => '设置商品分类禁用状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置商品分类禁用状态',
     *     'param'  => ''
     * )
     */
    public function setStatusOff()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (empty($id)) {
            $this->error('状态更改操作失败');
        }
        $mallCategoryModel = new MallCategoryModel();
        $result            = $mallCategoryModel->where('id', $id)->update(['status' => 0]);
        if ($result) {
            $this->success('状态更改操作成功');
        } else {
            $this->error('状态更改操作失败');
        }
    }

    /**
     *删除商品分类
     * @adminMenu(
     *     'name'   => '删除商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品分类',
     *     'param'  => ''
     * )
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (empty($id)) {
            $this->error('意外的错误');
        }
        $mallCategoryModel = new MallCategoryModel();
        //获取删除的内容
        $findCategory = $mallCategoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
        //判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $mallCategoryModel
            ->where('parent_id', $id)
            ->where('delete_time', 0)
            ->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此分类有子类无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'mall_category',
            'name'        => $findCategory['name']
        ];
        $result = $mallCategoryModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 商品分类排序
     * @adminMenu(
     *     'name'   => '商品分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $mallCategoryModel = new MallCategoryModel();
        parent::listOrders($mallCategoryModel);
        $this->success("排序更新成功！");
    }
}
