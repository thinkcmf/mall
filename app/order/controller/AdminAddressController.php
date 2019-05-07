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

use app\order\model\OrderUserAddressModel;
use cmf\controller\AdminBaseController;
use think\Db;

class AdminAddressController extends AdminBaseController
{

    public function addDialog()
    {
        $userId = $this->request->param('user_id', 0, 'intval');

        $provinces = Db::name('Area')->where(['level' => 1, 'id' => ['neq', 51]])->order('convert(name USING gbk) COLLATE gbk_chinese_ci')->select();
        $this->assign('provinces', $provinces);
        $this->assign('user_id', $userId);
        return $this->fetch('add_dialog');
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'AdminUserAddress');
            if ($result !== true) {
                $this->error($result);
            }

            $userAddressModel = new OrderUserAddressModel();

            $data['user_id'] = $data['user_id'];

            $userAddressModel->allowField(true)->save($data);

            $this->success('添加成功！', '', ['id' => $userAddressModel->id]);

        }
    }

    public function editDialog()
    {
        $id          = $this->request->param('id', 0, 'intval');
        $userId      = $this->request->param('user_id', 0, 'intval');
        $userAddress = Db::name('order_user_address')->where(['id' => $id, 'user_id' => $userId])->find();
        if (empty($userAddress)) {
            $this->error('地址不存在！');
        }
        $provinces = Db::name('Area')->where(['level' => 1, 'id' => ['neq', 51]])->order('convert(name USING gbk) COLLATE gbk_chinese_ci')->select();
        if (!empty($userAddress['city'])) {
            $cities = Db::name('Area')->where(['parent_id' => $userAddress['province']])->order('convert(name USING gbk) COLLATE gbk_chinese_ci')->select();
            $this->assign('cities', $cities);

            if (!empty($userAddress['district'])) {
                $districts = Db::name('Area')->where(['parent_id' => $userAddress['city']])->order('convert(name USING gbk) COLLATE gbk_chinese_ci')->select();
                $this->assign('districts', $districts);
            }
        }
        $this->assign('provinces', $provinces);
        $this->assign('user_id', $userId);
        $this->assign($userAddress);
        return $this->fetch('edit_dialog');
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $id     = $this->request->param('id', 0, 'intval');
            $userId = $this->request->param('user_id', 0, 'intval');

            $data   = $this->request->param();
            $result = $this->validate($data, 'AdminUserAddress');
            if ($result !== true) {
                $this->error($result);
            }

            $userAddressModel = new OrderUserAddressModel();

            $userAddressModel->allowField(true)->isUpdate(true)->save($data, ['id' => $id, 'user_id' => $userId]);

            $this->success('保存成功！');
        }
    }

    public function delete()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $userId = $this->request->param('user_id', 0, 'intval');
        Db::name('order_user_address')->where(['id' => $id, 'user_id' => $userId])->delete();
        $this->success('删除成功！');
    }

    public function setDefault()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $userId = $this->request->param('user_id', 0, 'intval');
        Db::name('order_user_address')->where(['user_id' => $userId, 'is_default' => 1])->update(['is_default' => 0]);
        Db::name('order_user_address')->where(['id' => $id, 'user_id' => $userId])->update(['is_default' => 1]);
        $this->success('设置成功！');
    }

    public function getSubAreas()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $areas = Db::name('Area')->where(['parent_id' => $id])->order('convert(name USING gbk) COLLATE gbk_chinese_ci')->select();
        $this->success('success', '', ['areas' => $areas]);
    }
}
