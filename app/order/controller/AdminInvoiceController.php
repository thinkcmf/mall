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

use app\order\model\OrderInvoiceModel;
use app\user\model\OrderUserAddressModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminInvoiceController extends AdminBaseController
{

    /**
     * 所有开发票
     * @adminMenu(
     *     'name'   => '所有开发票',
     *     'parent' => 'order/AdminOrder/defaultFinance',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '所有开发票',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data         = $this->request->param();
        $invoiceModel = new OrderInvoiceModel();
        $invoices     = $invoiceModel
            ->order('create_time DESC')
            ->where(function ($query) use ($data) {
                if (!empty($data['invoice_no'])) {
                    $query->where('invoice_no', 'like', "{$data['invoice_no']}%");
                }

                if (!empty($data['title'])) {
                    $query->where('title', 'like', "{$data['title']}%");
                }
            })->select();
        $this->assign('invoices', $invoices);
        return $this->fetch();
    }

    /**
     * 待开普通发票
     * @adminMenu(
     *     'name'   => '待开普通发票',
     *     'parent' => 'order/AdminOrder/defaultFinance',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '待开普通发票',
     *     'param'  => ''
     * )
     */
    public function waitPrint()
    {
        $invoiceModel = new OrderInvoiceModel();
        $invoices     = $invoiceModel->where(['invoice_no' => '', 'type' => 2])->order('create_time DESC')->select();
        $this->assign('invoices', $invoices);
        return $this->fetch('wait_print');
    }

    /**
     * 待开增值税发票
     * @adminMenu(
     *     'name'   => '待开增值税发票',
     *     'parent' => 'waitPrint',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '待开增值税发票',
     *     'param'  => ''
     * )
     */
    public function waitPrintValueAdded()
    {
        $invoiceModel = new OrderInvoiceModel();
        $invoices     = $invoiceModel->where(['invoice_no' => '', 'type' => 3])->order('create_time DESC')->select();
        $this->assign('invoices', $invoices);
        return $this->fetch('wait_print_value_added');
    }

    public function setInvoiceNo()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $invoiceNo = $this->request->param('invoice_no');

        Db::name('order_invoice')->where('id', $id)->update(['invoice_no' => $invoiceNo]);

        $this->success('设置成功！');
    }

    public function detail()
    {
        $id = $this->request->param('id', 0, 'intval');

        $findInvoice = Db::name('order_invoice')->where('id', $id)->find();

        if (empty($findInvoice)) {
            $this->error('发票不存在！');
        }

        $findInvoice['consignee_info'] = json_decode($findInvoice['consignee_info'], true);

        $areaIds = [];

        if (!empty($findInvoice['consignee_info']['province'])) {
            array_push($areaIds, $findInvoice['consignee_info']['province']);
        } else {
            $findInvoice['consignee_info']['province'] = 0;
        }

        if (!empty($findInvoice['consignee_info']['city'])) {
            array_push($areaIds, $findInvoice['consignee_info']['city']);
        } else {
            $findInvoice['consignee_info']['city'] = 0;
        }

        if (!empty($findInvoice['consignee_info']['district'])) {
            array_push($areaIds, $findInvoice['consignee_info']['district']);
        } else {
            $findInvoice['consignee_info']['district'] = 0;
        }

        if (!empty($findInvoice['consignee_info']['town'])) {
            array_push($areaIds, $findInvoice['consignee_info']['town']);
        } else {
            $findInvoice['consignee_info']['town'] = 0;
        }

        $areas = Db::name('area')->where(['id' => ['in', $areaIds]])->column('id,name', 'id');

        $this->assign('areas', $areas);


        $orderIds      = Db::name('order_invoice_order')->where('invoice_id', $id)->column('order_id');
        $totalAmount   = Db::name('order')->where('id', 'in', $orderIds)->sum('order_amount');
        $orderItems    = Db::name('order_item')->where('order_id', 'in', $orderIds)->select();
        $order         = Db::name('order')->where('id', $orderIds[0])->find();
        $order['more'] = json_decode($order['more'], true);

        $this->assign('order', $order);
        $this->assign('invoice', $findInvoice);
        $this->assign('total_amount', $totalAmount);
        $this->assign('order_items', $orderItems);

        $shipments = Db::name('order_shipment')->where(['status' => 1])->select();

        $this->assign('shipments', $shipments);

        return $this->fetch();
    }

    public function notPrintedInvoiceOrders()
    {
        $id = $this->request->param('id', 0, 'intval');

        $findInvoice = Db::name('order_invoice')->where(['id' => $id, 'invoice_no' => ''])->find();

        if ($findInvoice == 0) {
            $this->error('发票不存在或已开！');
        }

        $orderIds = Db::name('order_invoice_order')->where('invoice_id', $id)->column('order_id');

        if (empty($orderIds)) {
            $this->error('此发票没有关联的订单！');
        }

        $orders = Db::name('order')->where('id', 'in', $orderIds)->select();

        $userIds = [];
        foreach ($orders as $order) {
            array_push($userIds, $order['user_id']);
        }

        if (!empty($userIds)) {
            $userIds   = array_unique($userIds);
            $customers = Db::name('crm_customer')->where('user_id', 'in', $userIds)->column('*', 'user_id');
            $this->assign('customers', $customers);
        }

        $this->assign('orders', $orders);

        return $this->fetch('not_printed_invoice_orders');
    }

    public function downloadInvoicesXml()
    {
//        if ($this->request->isPost()) {
//            $ids = $this->request->param('ids/a');
//            $ids = Db::name('order_invoice')->where(['id' => ['in', $ids], 'invoice_no' => ''])->column('id');
//            if (empty($ids)) {
//                $this->error('请选择未打印发票！');
//            }
//
//            $this->success('正在下载...', url('AdminInvoice/downloadInvoicesXml', ['id' => implode(',', $ids)]));
//        }

        if ($this->request->isGet()) {
            $isDownload = $this->request->param('download');
            if (empty($isDownload)) {

                $ids      = $this->request->param('ids');
                $invoices = Db::name('order_invoice')->where(['id' => ['in', $ids], 'invoice_no' => ''])->select();
                if (empty($invoices)) {
                    $this->error('请选择未打印发票！');
                }

                $this->assign('invoices', $invoices);

                $this->assign('ids', $ids);

                return $this->fetch('download_invoices_xml');

            } else {
                $ids      = $this->request->param('ids');
                $ids      = explode(',', $ids);
                $invoices = Db::name('order_invoice')->where(['id' => ['in', $ids], 'invoice_no' => ''])->select();
                if (empty($invoices)) {
                    $this->error('请选择未打印发票！');
                }

                $fpxx = [
                    'Zsl'  => count($invoices),
                    'Fpsj' => []
                ];

                foreach ($invoices as $invoice) {

//                    $order               = Db::name('order')->find();
//                    $order['more']       = json_decode($order['more']);

                    $orderIds   = Db::name('order_invoice_order')->where('invoice_id', $invoice['id'])->column('order_id');
                    $orderItems = Db::name('order_item')->where('order_id', 'in', $orderIds)->select();
                    $fp         = [
                        'Djh'    => $invoice['id'],//单据号（20字节）
                        'Gfmc'   => $invoice['title'], //购方名称（100字节）
                        'Gfsh'   => $invoice['taxpayer_id'], //购方税号
                        'Gfyhzh' => $invoice['bank_name'] . $invoice['bank_account'], //购方银行账号（100字节）
                        'Gfdzdh' => $invoice['address'] . $invoice['phone'], //购方地址电话（100字节）
                        'Bz'     => '备注',//备注（240字节）
                        'Fhr'    => '复核人', //复核人（8字节）
                        'Skr'    => '收款人', //收款人（8字节）
                        'Spxx'   => ''
                    ];

                    $Spxx = [];

                    foreach ($orderItems as $orderItem) {
                        $Sph = [
                            'Sph' => [
                                'Xh'   => $orderItem['id'], //序号
                                'Spmc' => $orderItem['goods_name'],//商品名称，金额为负数时此项为折扣
                                'Ggxh' => $orderItem['goods_spec'],////规格型号（40字节）
                                'Jldw' => '瓶', //计量单位（32字节）
                                'Dj'   => $orderItem['goods_price'], //单价
                                'Sl'   => $orderItem['goods_quantity'], //数量
                                'Je'   => number_format(round($orderItem['goods_quantity'] * $orderItem['goods_price'], 2), 2),//金额，当金额为负数时为折扣行
                                'Slv'  => '0.17', //税率
                            ]
                        ];

                        array_push($Spxx, cmf_data_to_xml($Sph, 'item', ''));
                    }

                    $fp['Spxx'] = join('', $Spxx);

                    array_push($fpxx['Fpsj'], $fp);
                }

                $xml = cmf_xml_encode($fpxx, 'Kp', 'Fp', '', '', 'GBK');

                $xml = iconv('UTF-8', 'GBK', $xml);

                $filename = date('YmdHis') . rand(1000, 9999) . '.xml';

                //下载文件需要用到的头
                Header("Content-type: application/octet-stream");
                //Header("Accept-Ranges: bytes");
                //Header("Accept-Length:".);
                Header("Content-Disposition: attachment; filename=" . $filename);

                echo $xml;
                exit;
            }


        }


    }

    public function uploadInvoicesXml()
    {

        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (isset($post['file'])) {
                $file = './upload/' . $post['file'];

                if (!file_exists_case($file)) {
                    $this->error('上传的文件不存在！');
                }

                $content = str_replace('encoding="GBK"', 'encoding="UTF-8"', file_get_contents($file));
                $content = iconv('GBK', 'UTF-8', $content);

                try {
                    $invoices = simplexml_load_string($content);
                } catch (\Exception $e) {
                    $this->error('文件内容解析错误！');
                }

                $invoices = json_decode(json_encode($invoices), true);

                if (!empty($invoices['Fpxx']['Fpsj']['Fp'])) {
                    foreach ($invoices['Fpxx']['Fpsj']['Fp'] as $fp) {
                        Db::name('order_invoice')->where('id', $fp['Djh'])->update(['invoice_no' => $fp['Fphm']]);
                    }
                }

                $this->success('发票信息导入完成！');


            }
        }

    }

    public function deleteInvoiceOrder()
    {
        $orderId          = $this->request->param('order_id', 0, 'intval');
        $findInvoiceOrder = Db::name('order_invoice_order')->where('order_id', $orderId)->find();

        if (empty($findInvoiceOrder)) {
            $this->error('订单开票请求不存在!');
        }

        $invoiceNo = Db::name('order_invoice')->where('id', $findInvoiceOrder['invoice_id'])->value('invoice_no');

        if (!empty($invoiceNo)) {
            $this->error('发票已经开，无法取消订单开票！');
        }

        $findInvoiceOrderCount = Db::name('order_invoice_order')->where('invoice_id', $findInvoiceOrder['invoice_id'])->count();

        if ($findInvoiceOrderCount < 2) {
            $this->error('发票只有一个关联订单无法取消！');
        }

        Db::name('order_invoice_order')->where('order_id', $orderId)->delete();

        $this->success('订单开票取消成功！');

    }

    public function setTrackingNumber()
    {
        $trackingNumber = $this->request->param('tracking_number');
        $shipmentCode   = $this->request->param('shipment_code');
        $id             = $this->request->param('id', 0, 'intval');

        if (empty($trackingNumber)) {
            $this->error('运单号不能为空！');
        }

        $shipmentName = Db::name('order_shipment')->where('code', $shipmentCode)->value('name');

        if (empty($shipmentName)) {
            $this->error('物流不存在！');
        }

        $invoice = Db::name('order_invoice')->where('id', $id)->find();

        if (empty($invoice)) {
            $this->error('订单不存在！');
        }

        Db::name('order_invoice')->where('id', $id)->update([
            'tracking_number' => $trackingNumber,
            'shipping_status' => 1, //已发货
            'shipment_code'   => $shipmentCode,
            'shipment_name'   => $shipmentName,
            'deliver_time'    => time()
        ]);


        $this->success('操作成功！');

    }

}
