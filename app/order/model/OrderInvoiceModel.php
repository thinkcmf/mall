<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\order\model;

use think\Model;

class OrderInvoiceModel extends Model
{
    protected $type = [
        'consignee_info' => 'array',
    ];

    public function orders()
    {
        return $this->belongsToMany('OrderModel', 'order_invoice_order', 'order_id', 'invoice_id');
    }
}