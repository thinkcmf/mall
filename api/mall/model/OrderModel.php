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
namespace api\mall\model;

use app\order\model\OrderModel as Model;

class OrderModel extends Model
{
    public static $process_map = [
        'toPay'      => '待付款',
        'toShip'     => '待发货',
        'toDeleived' => '待收货',
        'toReceived' => '待收货',
        'toFinished' => '已收货',
        'finished'   => '已收货',
        'closed'     => '已关闭',
    ];

    public function getProcessTextAttr($value, $data)
    {
        $process = $this->getProcessAttr($value, $data);
        return self::$process_map[$process];
    }

    /**
     * 订单商品
     *
     * @return \think\model\relation\HasMany
     */
    public function item()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }
}
