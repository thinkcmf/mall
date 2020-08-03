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
namespace api\mall\service;

use api\mall\model\OrderModel;
use think\Db;

/**
 * 服务提供： 订单
 *
 */
class OrderService
{

    /**
     * 订单列表
     *
     * @param $status int 订单状态：0全部，1待付款，2待收货，3已完成
     */
    public static function getList($userId, $status = 0)
    {
        $map   = self::builderStatus($status);
        $map[] = ['user_id', '=', $userId];

        $model = new OrderModel();
        $model->with('item');

        $builder = QueryService::getBuilder($model, $map, '', [], 'id');

        $data = $builder->paginate();

        $list = array_map([__CLASS__, 'orderFilter'], $data->items());

        return [
            'total'        => $data->total(),
            'per_page'     => $data->listRows(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
            'items'        => $list
        ];
    }

    public static function getCount($userId)
    {
        $statuses = [0, 1, 2, 21, 22, 3, 4];
        $res = [];
        foreach ($statuses as $i) {
            $map   = self::builderStatus($i);
            $map[] = ['user_id', '=', $userId];
            $count = Db::name('order')->where($map)->count();
            $res[$i . ''] = $count;
        }
        return $res;
    }

    /**
     * 订单状态查询构建
     */
    public static function builderStatus($status = 0)
    {
        $map = [];
        switch ($status) {
            case 1:
                // 待付款
                $map[] = ['status', '=', 1];
                $map[] = ['pay_status', '=', 0];
                $map[] = ['ship_status', '=', 0];
                $map[] = ['delivery_time', '=', 0];
                $map[] = ['received_time', '=', 0];
                $map[] = ['finished_time', '=', 0];
                break;
            case 2:
                // 待收货(待发货和待收货)
                $map[] = ['status', '=', 1];
                $map[] = ['pay_status', '=', 1];
                // $map[] = ['ship_status', '=', 0];
                // $map[] = ['delivery_time', '=', 0];
                $map[] = ['received_time', '=', 0];
                $map[] = ['finished_time', '=', 0];
                break;
            case 21:
                // 待发货
                $map[] = ['status', '=', 1];
                $map[] = ['pay_status', '=', 1];
                $map[] = ['ship_status', '=', 0];
                $map[] = ['delivery_time', '=', 0];
                $map[] = ['received_time', '=', 0];
                $map[] = ['finished_time', '=', 0];
                break;
            case 22:
                // 待收货
                $map[] = ['status', '=', 1];
                $map[] = ['pay_status', '=', 1];
                $map[] = ['ship_status', '=', 1];
//                $map[] = ['delivery_time', '>', 0];
                $map[] = ['received_time', '=', 0];
                $map[] = ['finished_time', '=', 0];
                break;

            case 3:
                // 已完成
                $map[] = ['status', '=', 1];
                $map[] = ['pay_status', '=', 1];
                $map[] = ['ship_status', '=', 1];
                // $map[] = ['delivery_time', '>', 0];
                $map[] = ['received_time|finished_time', '>', 0];
                // $map[] = ['received_time', '>', 0];
                // $map[] = ['finished_time', '>', 0];
                break;
            case 4:
                // 已关闭
                $map[] = ['status', '=', 0];
                break;
            default:
                // $map[] = ['status', '=', 1];
                break;
        }
        return $map;
    }

    /**
     * 列表数据过滤
     */
    public static function orderFilter($data)
    {
        $items = $data->item->map(function ($item) {
            return self::orderItemFilter($item);
        });

        return [
            'id'             => $data->id,
            // 'user_id'        => $data->user_id,
            'sn'             => $data->sn,
            'channel'        => $data->channel,
            'create_time'    => $data->create_time,
            // 'update_time'    => $data->update_time,
            // 'delete_time'    => $data->delete_time,
            'expire_time'    => $data->expire_time,
            'status'         => $data->status,
            'status_text'    => $data->process_text,
            'country'        => $data->country,
            'province'       => $data->province,
            'city'           => $data->city,
            'district'       => $data->district,
            'town'           => $data->town,
            'area_code'      => $data->area_code,
            'address'        => $data->address,
            'consignee'      => $data->consignee,
            'zip_code'       => $data->zip_code,
            'email'          => $data->email,
            'phone'          => $data->phone,
            'phone2'         => $data->phone2,
            'remark'         => $data->remark,
            'total_weight'   => $data->total_weight,
            'total_item'     => $data->total_item,
            'amount_goods'   => $data->amount_goods,
            'amount_shipfee' => $data->amount_shipfee,
            'amount_offset'  => $data->amount_offset,
            'amount_payable' => $data->amount_payable,
            'pay_status'     => $data->pay_status,
            'pay_up_time'    => $data->pay_up_time,
            'pay_time'       => $data->pay_time,
            'pay_amount'     => $data->pay_amount,
            'pay_method'     => $data->pay_method,
            'pay_sn'         => $data->pay_sn,
            'pay_info'       => $data->pay_info,
            'ship_status'    => $data->ship_status,
            'ship_time'      => $data->ship_time,
            'ship_code'      => $data->ship_code,
            'ship_name'      => $data->ship_name,
            'ship_sn'        => $data->ship_sn,
            'delivery_time'  => $data->delivery_time,
            'received_time'  => $data->received_time,
            'finished_time'  => $data->finished_time,
            // 'flag'           => $data->flag,
            // 'notice'         => $data->notice,
            // 'more'           => $data->more,
            'items'          => $items,
        ];
    }

    /**
     * 订单物品数据过滤
     */
    public static function orderItemFilter($data)
    {
        return [
            'id'             => $data->id,
            // 'order_id'       => $data->order_id,
            // 'user_id'        => $data->user_id,
            // 'create_time'    => $data->create_time,
            // 'update_time'    => $data->update_time,
            // 'item_sn'        => $data->item_sn,
            // 'goods_table'    => $data->goods_table,
            'goods_id'       => $data->goods_id,
            'goods_title'    => $data->goods_title,
            'thumbnail'      => $data->thumbnail,
            'thumbnail_url'  => $data->thumbnail_url,
            // 'sku_table'      => $data->sku_table,
            'sku_id'         => $data->sku_id,
            'sku_title'      => $data->sku_title,
            'brand_id'       => $data->brand_id,
            'brand_name'     => $data->brand ? $data->brand->name : '',
            'quantity'       => $data->quantity,
            'original_price' => $data->original_price,
            'price'          => $data->price,
            // 'more'           => $data->more,
        ];
    }

    /**
     * 新建订单
     */
    public function doPlace($userId, $item, $bill, $address, $remark)
    {
        $billData            = array_merge($bill, $address);
        $billData['user_id'] = $userId;
        $billData['remark']  = $remark;
        $billData['sn']      = OrderModel::makeSn();

        // $order = new OrderModel($billData);
        // $order->allowField(true)->save();
        $model = OrderModel::create($billData);
        $model->item()->saveAll($item);

        $id    = $model->id;
        $order = OrderModel::get($id);

        return self::orderFilter($order);
    }

    public static function getOrder($userId, $id, $filter = true)
    {
        // $map = self::builderStatus(1);
        $map[] = ['user_id', '=', $userId];
        $map[] = ['id', '=', $id];

        $order = OrderModel::where($map)->find();

        if ($filter) {
            return self::orderFilter($order);
        }
        return $order;
    }

    /**
     * 取消订单
     */
    public static function doCancel($userId, Int $id)
    {
        $res = [
            'error' => '',
            'data'  => []
        ];

        // $map = self::builderStatus(1);
        $map[] = ['user_id', '=', $userId];
        $map[] = ['id', '=', $id];

        $order = OrderModel::where($map)->find();

        if ($order) {
            if (!$order->status) {
                $res['error'] = '订单已关闭';
            } elseif ($order->pay_status || $order->ship_status || $order->delivery_time || $order->received_time || $order->finished_time) {
                $res['error'] = '订单处于不可关闭的状态，请联系客服';
            } else {
                $order->status = 0;
                $order->save();
            }
            $res['data'] = self::orderFilter($order);
        } else {
            $res['error'] = '订单不存在';
        }
        return $res;
    }

    /**
     * 订单支付
     */
    public static function doPaid($userId, $id, $payInfo)
    {
        $res = [
            'error' => '',
            'data'  => []
        ];

        if (empty($payInfo['pay_time']) || empty($payInfo['pay_amount']) || empty($payInfo['pay_method']) || empty($payInfo['pay_sn'])) {
            $res['error'] = '支付信息有误。';
            return $res;
        }

        $map[] = ['user_id', '=', $userId];
        $map[] = ['id', '=', $id];
        $order = OrderModel::where($map)->find();

        if ($order) {
            if (!$order->status) {
                $res['error'] = '订单已关闭，不可修改支付状态';
            } elseif ($order->pay_status || $order->ship_status || $order->delivery_time || $order->received_time || $order->finished_time) {
                $res['error'] = '订单处于不可的修改支付信息的状态，请联系客服';
            } else {

                $order->pay_status  = 1;
                $order->pay_up_time = $payInfo['pay_up_time'] ?? 0;
                $order->pay_time    = $payInfo['pay_time'];
                $order->pay_amount  = $payInfo['pay_amount'];
                $order->pay_method  = $payInfo['pay_method'];
                $order->pay_sn      = $payInfo['pay_sn'];
                $order->pay_info    = $payInfo['pay_info'] ?? $payInfo;
                // $order->status = 0;
                $order->save();
            }
            $res['data'] = self::orderFilter($order);
        } else {
            $res['error'] = '订单不存在';
        }
        return $res;
    }

    /**
     * 订单收货
     */
    public static function doReceived($userId, $id)
    {
        $res = [
            'error' => '',
            'data'  => []
        ];

        $map[] = ['user_id', '=', $userId];
        $map[] = ['id', '=', $id];
        $order = OrderModel::where($map)->find();

        if (empty($order)) {
            $res['error'] = '订单不存在';
            return $res;
        }

        if (!$order->pay_status) {
            $res['error'] = '订单未付款';
        } elseif (!$order->ship_status) {
            $res['error'] = '订单未发货';
        } elseif ($order->received_time) {
            $res['error'] = '订单已收货';
        } elseif ($order->finished_time) {
            $res['error'] = '订单已完成';
        } else {
            $order->delivery_time = time();
            $order->received_time = $order->delivery_time;
            $order->save();
        }
        $res['data'] = self::orderFilter($order);
        return $res;
    }
}
