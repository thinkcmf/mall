<?php
// +----------------------------------------------------------------------
// | CMFMall_2020
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 达达 <ccbox.net@163.com>
// +----------------------------------------------------------------------
namespace api\mall\controller;

use api\common\base\AuthBaseController;
use api\mall\service\CheckoutService;
use app\mall\model\ExpressFeeModel;
use app\mall\service\ExpressService;
use think\Db;

class TestController extends AuthBaseController
{
    

    public function test()
    {
        $province = $this->request->param('province');
        $weight = $this->request->param('weight');
        $expressId = $this->request->param('id');

        $res = ExpressService::getShipfee($province, $weight, $expressId);

        $this->success('成功了', [$res]);
    }
}
