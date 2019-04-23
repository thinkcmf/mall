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
namespace app\order\Controller;

use cmf\controller\HomeBaseController;
use think\Db;

class PublicController extends HomeBaseController
{

    public function confirm()
    {
        $code   = $this->request->param('code');
        $params = explode('_', $code);

        if (count($params) != 3) {
            $this->error('参数错误！');
        }

        $userId  = $params[1];
        $orderSn = $params[2];


        $orderMore = Db::name('order')->where(['user_id' => $userId, 'order_sn' => $orderSn])->value('more');

        if (empty($orderMore)) {
            $this->error('订单确认失败！');
        }

        $orderMore = json_decode($orderMore, true);
        if ($orderMore['user_confirm_code'] == $code) {
            Db::name('order')->where(['user_id' => $userId, 'order_sn' => $orderSn])->update([
                'user_confirmed' => 1
            ]);
        } else {
            $this->error('code 无效！');
        }

        return $this->fetch();

    }


}