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

use app\order\model\OrderPaymentModel;
use cmf\controller\AdminBaseController;

class AdminPaymentController extends AdminBaseController
{

    /**
     * 支付管理
     * @adminMenu(
     *     'name'   => '支付管理',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '支付管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $orderPaymentModel = new OrderPaymentModel();
        $payments          = $orderPaymentModel->select();
        $this->assign('payments', $payments);
        return $this->fetch();
    }

    /**
     * 编辑支付方式
     * @adminMenu(
     *     'name'   => '编辑支付方式',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑支付方式',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id                = $this->request->param('id', 0, 'intval');
        $orderPaymentModel = OrderPaymentModel::get($id);
        $this->assign('payment', $orderPaymentModel);
        return $this->fetch();
    }

    /**
     * 编辑支付方式提交保存
     * @adminMenu(
     *     'name'   => '编辑支付方式提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑支付方式提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data              = $this->request->param();
        $orderPaymentModel = new OrderPaymentModel();
        $result            = $orderPaymentModel->allowField(true)->isUpdate(true)->save($data);
        if ($result === false) {
            $this->error($orderPaymentModel->getError());
        }

        $this->success("保存成功！");
    }

    /**
     * 删除支付方式
     * @adminMenu(
     *     'name'   => '删除支付方式',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除支付方式',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        OrderPaymentModel::destroy($id);

        $this->success("删除成功！");
    }

    /**
     * 支付方式排序
     * @adminMenu(
     *     'name'   => '支付方式排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '支付方式排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $orderPaymentModel = new  OrderPaymentModel();
        parent::listOrders($orderPaymentModel);
        $this->success("排序更新成功！");
    }

    /**
     * 支付方式启用
     * @adminMenu(
     *     'name'   => '支付方式启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '支付方式启用',
     *     'param'  => ''
     * )
     */
    public function enable()
    {
        $data              = $this->request->param();
        $orderPaymentModel = new OrderPaymentModel();

        if (isset($data['ids'])) {
            $ids = $this->request->param('ids/a');
            $orderPaymentModel->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }
    }

    /**
     * 支付方式禁用
     * @adminMenu(
     *     'name'   => '支付方式禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '支付方式禁用',
     *     'param'  => ''
     * )
     */
    public function disable()
    {
        $data              = $this->request->param();
        $orderPaymentModel = new OrderPaymentModel();

        if (isset($data['ids'])) {
            $ids = $this->request->param('ids/a');
            $orderPaymentModel->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }

}