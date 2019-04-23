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
use cmf\controller\UserBaseController;
use think\Db;

class UserAddressController extends UserBaseController
{

    public function addDialog()
    {
        $provinces = Db::name('Area')->where(['level' => 1])->where('id', 'neq', 51)->order(Db::raw('convert(name USING gbk) COLLATE gbk_chinese_ci'))->select();
        $this->assign('provinces', $provinces);
        return $this->fetch('add_dialog');
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'UserAddress');
            if ($result !== true) {
                $this->error($result);
            }

            $orderUserAddressModel = new OrderUserAddressModel();

            $data['user_id'] = cmf_get_current_user_id();

            $orderUserAddressModel->allowField(true)->save($data);

            $this->success('添加成功！', '', ['id' => $orderUserAddressModel->id]);

        }
    }

    public function editDialog()
    {
        $id          = $this->request->param('id', 0, 'intval');
        $userId      = cmf_get_current_user_id();
        $userAddress = Db::name('OrderUserAddress')->where(['id' => $id, 'user_id' => $userId])->find();
        if (empty($userAddress)) {
            $this->error('地址不存在！');
        }
        $provinces = Db::name('Area')->where(['level' => 1])->where('id', 'neq', 51)->order(Db::raw('convert(name USING gbk) COLLATE gbk_chinese_ci'))->select();
        if (!empty($userAddress['city'])) {
            $cities = Db::name('Area')->where('parent_id' , $userAddress['province'])->order(Db::raw('convert(name USING gbk) COLLATE gbk_chinese_ci'))->select();
            $this->assign('cities', $cities);

            if (!empty($userAddress['district'])) {
                $districts = Db::name('Area')->where('parent_id' , $userAddress['city'])->order(Db::raw('convert(name USING gbk) COLLATE gbk_chinese_ci'))->select();
                $this->assign('districts', $districts);
            }
        }
        $this->assign('provinces', $provinces);

        $this->assign($userAddress);
        return $this->fetch('edit_dialog');
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $id     = $this->request->param('id', 0, 'intval');
            $userId = cmf_get_current_user_id();

            $data   = $this->request->param();
            $result = $this->validate($data, 'UserAddress');
            if ($result !== true) {
                $this->error($result);
            }

            $orderUserAddressModel = new OrderUserAddressModel();

            $orderUserAddressModel->allowField(true)->isUpdate(true)->save($data, ['id' => $id, 'user_id' => $userId]);

            $this->success('保存成功！');
        }
    }

    public function getSubAreas()
    {
        $id    = $this->request->param('id', 0, 'intval');
        $areas = Db::name('Area')->where('parent_id' , $id)->order(Db::raw('convert(name USING gbk) COLLATE gbk_chinese_ci'))->select();
        $this->success('success', '', ['areas' => $areas]);
    }

    public function delete()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $userId = cmf_get_current_user_id();
        Db::name('OrderUserAddress')->where(['id' => $id, 'user_id' => $userId])->delete();
        $this->success('删除成功！');
    }

    public function setDefault()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $userId = cmf_get_current_user_id();
        Db::name('OrderUserAddress')->where(['user_id' => $userId, 'is_default' => 1])->update(['is_default' => 0]);
        Db::name('OrderUserAddress')->where(['id' => $id, 'user_id' => $userId])->update(['is_default' => 1]);
        $this->success('设置成功！');
    }
}
