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
namespace app\order\model;

use app\user\model\UserModel;

class OrderModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 订单编号 修改器
     *
     * @param $value
     * @return mixed
     */
    public function setSnAttr($value = '')
    {
        return self::makeSn();
    }

    public static function makeSn()
    {
        return config('order_prefix') . date('YmdHis') . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * 订单进程管理部分：
     */

    public static $process_map = [
        'toPay'      => '待付款',
        'toShip'     => '待发货',
        'toDeleived' => '待送达',
        'toReceived' => '待签收',
        'toFinished' => '待完成',
        'finished'   => '已完成',
        'closed'     => '已关闭',
    ];

    public function getProcessAttr($value, $data)
    {
        if ($data['status']) {
            $process = 'toPay';
            // 套娃开始
            if ($data['pay_status']) {
                $process = 'toShip';

                if ($data['ship_status']) {
                    $process = 'toDeleived';

                    if ($data['delivery_time']) {
                        $process = 'toReceived';

                        if ($data['received_time']) {
                            $process = 'toFinished';

                            if ($data['finished_time']) {
                                $process = 'finished';
                            }
                        }
                    }
                }
            }
        } else {
            $process = 'closed';
        }

        return $process;
    }

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

    /**
     * 关联模型 
     *
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
