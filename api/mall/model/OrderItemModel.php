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

use app\order\model\OrderItemModel as Model;

class OrderItemModel extends Model
{
    /**
     * 订单 关联模型
     *
     * @return \think\model\relation\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
}
