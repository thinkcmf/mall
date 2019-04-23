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

use app\user\model\OrderUserAddressModel;
use cmf\controller\UserBaseController;
use think\Db;

class UserInvoiceController extends UserBaseController
{

    public function editDialog()
    {
        $userId      = cmf_get_current_user_id();
        $invoiceInfo = Db::name('order_user_invoice')->where('user_id', $userId)->find();

        if (!empty($invoiceInfo)) {
            $invoiceInfo['consignee_info'] = json_decode($invoiceInfo['consignee_info'], true);
            $this->assign($invoiceInfo);
        }

        $userAddresses = Db::name('order_user_address')->where('user_id', $userId)->select();

        if (!empty($userAddresses)) {
            $areaIds = [];

            foreach ($userAddresses as $address) {
                array_push($areaIds, $address['province']);
                if (!empty($address['city'])) {
                    array_push($areaIds, $address['city']);
                }
                if (!empty($address['district'])) {
                    array_push($areaIds, $address['district']);
                }
            }


            $areas = Db::name('Area')->where('id' ,'in', $areaIds)->column('id,name', 'id');

            $this->assign('user_addresses', $userAddresses);
            $this->assign('areas', $areas);
        }

        return $this->fetch('edit_dialog');
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $type   = $this->request->param('type', 0, 'intval');
            $userId = cmf_get_current_user_id();

            $data = $this->request->param();

            switch ($type) {
                case 1:
                    $result = $this->validate($data, 'UserInvoice.default');
                    break;
                case 2:
                    $result = $this->validate($data, 'UserInvoice.normal');
                    break;
                case 3:
                    $result = $this->validate($data, 'UserInvoice.special');
                    break;
                default:
                    $this->error('非法发票类型！');

            }

            if ($result !== true) {
                $this->error($result);
            }

            $userAddressId = $this->request->param('consignee', 0, 'intval');
            $userAddress   = Db::name('order_user_address')->where(['id' => $userAddressId, 'user_id' => $userId])->find();

            if (empty($userAddress)) {
                $this->error('收票人信息不正确！');
            }

            $findUserInvoice = Db::name('order_user_invoice')->where('user_id', $userId)->find();

            $data['consignee_info'] = json_encode($userAddress);
            $data['user_id']        = $userId;

            if ($findUserInvoice) {
                $invoiceId = $findUserInvoice['id'];
                Db::name('order_user_invoice')->where('user_id', $userId)->strict(false)->field(true)->update($data);
            } else {
                $invoiceId = Db::name('order_user_invoice')->strict(false)->field(true)->insertGetId($data);
            }
            $data['invoice_id'] = $invoiceId;
            $this->success('保存成功！', '', $data);
        }
    }

}
