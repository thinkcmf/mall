<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <catmant@thinkcmf.com>
// +----------------------------------------------------------------------
namespace app\order\controller;

use app\order\model\OrderShipmentModel;
use cmf\controller\AdminBaseController;
use think\Db;


class AdminShipmentController extends AdminBaseController
{

    /**
     * 物流方式
     * @adminMenu(
     *     'name'   => '物流方式',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流方式',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $shipments = Db::name('order_shipment')->order('list_order ASC')->select();
        $this->assign('shipments', $shipments);
        return $this->fetch();
    }

    /**
     * 添加物流方式
     * @adminMenu(
     *     'name'   => '添加物流方式',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加物流方式',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加物流方式提交保存
     * @adminMenu(
     *     'name'   => '添加物流方式提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加物流方式提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data               = $this->request->param();
        $orderShipmentModel = new OrderShipmentModel();
        $result             = $orderShipmentModel->allowField(true)->save($data);
        if ($result === false) {
            $this->error($orderShipmentModel->getError());
        }

        $this->success("添加成功！", url("AdminShipment/edit", ['id' => $orderShipmentModel->id]));
    }

    /**
     * 编辑物流方式
     * @adminMenu(
     *     'name'   => '编辑物流方式',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑物流方式',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id                 = $this->request->param('id', 0, 'intval');
        $orderShipmentModel = OrderShipmentModel::get($id);
        $this->assign('shipment', $orderShipmentModel);
        return $this->fetch();
    }

    /**
     * 编辑物流方式提交保存
     * @adminMenu(
     *     'name'   => '编辑物流方式提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑物流方式提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data               = $this->request->param();
        $orderShipmentModel = new OrderShipmentModel();
        $result             = $orderShipmentModel->allowField(true)->isUpdate(true)->save($data);
        if ($result === false) {
            $this->error($orderShipmentModel->getError());
        }

        $this->success("保存成功！");
    }

    /**
     * 删除物流方式
     * @adminMenu(
     *     'name'   => '删除物流方式',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除物流方式',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        OrderShipmentModel::destroy($id);

        $this->success("删除成功！");
    }

    /**
     * 物流方式排序
     * @adminMenu(
     *     'name'   => '物流方式排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流方式排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $orderShipmentModel = new  OrderShipmentModel();
        parent::listOrders($orderShipmentModel);
        $this->success("排序更新成功！");
    }

    /**
     * 物流方式启用
     * @adminMenu(
     *     'name'   => '物流方式启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流方式启用',
     *     'param'  => ''
     * )
     */
    public function enable()
    {
        $data               = $this->request->param();
        $orderShipmentModel = new OrderShipmentModel();

        if (isset($data['ids'])) {
            $ids = $this->request->param('ids/a');
            $orderShipmentModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success("更新成功！");
        }
    }

    /**
     * 物流方式禁用
     * @adminMenu(
     *     'name'   => '物流方式禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '物流方式禁用',
     *     'param'  => ''
     * )
     */
    public function disable()
    {
        $data               = $this->request->param();
        $orderShipmentModel = new OrderShipmentModel();

        if (isset($data['ids'])) {
            $ids = $this->request->param('ids/a');
            $orderShipmentModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }

}
