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
namespace app\order\service;

use app\order\model\OrderModel;
use app\order\model\OrderItemModel;

/**
 * 服务提供： 订单（后台）
 * 
 */
class OrderService extends BaseService
{

    /**
     * 获取数据列表
     */
    public static function get($map = [], $orderby = 'create_time', $field = '', $deleted = false)
    {
        if ($deleted) {
            $map[] = ['delete_time', '>', 0];
        } else {
            $map[] = ['delete_time', '=', 0];
        }

        $model = OrderModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    public static function buildProcessQuery($process = '')
    {
        $process_map = OrderModel::$process_map;
        $map = [];
        $map[] = ['status', '=', 1];
        if (array_key_exists($process, $process_map)) {
            switch ($process) {
                case 'toPay':
                    $map[] = ['pay_status', '=', 0];
                    $map[] = ['ship_status', '=', 0];
                    $map[] = ['delivery_time', '=', 0];
                    $map[] = ['received_time', '=', 0];
                    $map[] = ['finished_time', '=', 0];
                    break;
                case 'toShip':
                    $map[] = ['pay_status', '=', 1];
                    $map[] = ['ship_status', '=', 0];
                    $map[] = ['delivery_time', '=', 0];
                    $map[] = ['received_time', '=', 0];
                    $map[] = ['finished_time', '=', 0];
                    break;
                case 'toDeleived':
                    $map[] = ['pay_status', '=', 1];
                    $map[] = ['ship_status', '=', 1];
                    $map[] = ['delivery_time', '=', 0];
                    $map[] = ['received_time', '=', 0];
                    $map[] = ['finished_time', '=', 0];
                    break;
                case 'toReceived':
                    $map[] = ['pay_status', '=', 1];
                    $map[] = ['ship_status', '=', 1];
                    $map[] = ['delivery_time', '>', 0];
                    $map[] = ['received_time', '=', 0];
                    $map[] = ['finished_time', '=', 0];
                    break;
                case 'toFinished':
                    $map[] = ['pay_status', '=', 1];
                    $map[] = ['ship_status', '=', 1];
                    // $map[] = ['delivery_time', '>', 0];
                    $map[] = ['received_time', '>', 0];
                    $map[] = ['finished_time', '=', 0];
                    break;
                case 'finished':
                    $map[] = ['pay_status', '=', 1];
                    $map[] = ['ship_status', '=', 1];
                    // $map[] = ['delivery_time', '>', 0];
                    $map[] = ['received_time', '>', 0];
                    $map[] = ['finished_time', '>', 0];
                    break;
                case 'closed':
                    $map = [];
                    $map[] = ['status', '=', 0];
                    break;

                default:
                    # code...
                    break;
            }
        }
        return $map;
    }

    public static function getOrder($id)
    {
        $res = [
            'error' => '',
            'data' => []
        ];
        if (intval($id) < 1) {
            $res['error'] = '无效的ID';
            return $res;
        }
        $order = OrderModel::get($id);
        if (!$order) {
            $res['error'] = '订单不存在';
        } else {
            $res['data'] = $order;
            if (!$order->status) {
                $res['error'] = '订单已关闭';
            } elseif ($order->delete_time > 0) {
                $res['error'] = '订单已删除';
            }
        }
        return $res;
    }

    /**
     * 订单支付
     */
    public static function doPaid($id, $data)
    {
        if (empty($data['pay_time']) || empty($data['pay_amount']) || empty($data['pay_method']) || empty($data['pay_sn'])) {
            $res['error'] = '支付信息有误。';
            return $res;
        }

        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if ($order->pay_status || $order->ship_status || $order->delivery_time || $order->received_time || $order->finished_time) {
                $res['error'] = '订单处于不可的修改支付信息的状态，请联系客服';
            } else {
                $order->pay_status     = 1;
                $order->pay_up_time    = $data['pay_up_time'] ?? 0;
                $order->pay_time       = $data['pay_time'];
                $order->pay_amount     = $data['pay_amount'];
                $order->pay_method     = $data['pay_method'];
                $order->pay_sn         = $data['pay_sn'];
                $order->pay_info       = $data['pay_info'] ?? $data;
                // $order->status = 0;
                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单发货
     */
    public static function doShip($id, $data)
    {
        if (empty($data['ship_time']) || empty($data['ship_code']) || empty($data['ship_name']) || empty($data['ship_sn'])) {
            $res['error'] = '发货信息有误。';
            return $res;
        }

        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if (!$order->pay_status) {
                $res['error'] = '订单还未付款';
            } elseif ($order->ship_status) {
                $res['error'] = '订单已发货';
            } elseif ($order->delivery_time || $order->received_time || $order->finished_time) {
                $res['error'] = '发货状态不可修改';
            } else {
                $order->ship_status = 1;
                $order->ship_time   = $data['ship_time'];
                $order->ship_code   = $data['ship_code'];
                $order->ship_name   = $data['ship_name'];
                $order->ship_sn     = $data['ship_sn'];
                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单送达
     */
    public static function doDelivery($id, $time = null)
    {
        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if (!$order->pay_status) {
                $res['error'] = '订单未付款';
            } elseif (!$order->ship_status) {
                $res['error'] = '订单未发货';
            } elseif ($order->delivery_time) {
                $res['error'] = '订单已送达';
            } elseif ($order->received_time) {
                $res['error'] = '订单已收货';
            } elseif ($order->finished_time) {
                $res['error'] = '订单已完成';
            } else {
                $order->delivery_time = $time ?? time();
                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单确认收货
     */
    public static function doReceived($id, $time = null)
    {
        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if (!$order->pay_status) {
                $res['error'] = '订单未付款';
            } elseif (!$order->ship_status) {
                $res['error'] = '订单未发货';
            } elseif (!$order->delivery_time) {
                $res['error'] = '订单未送达';
            } elseif ($order->received_time) {
                $res['error'] = '订单已收货';
            } elseif ($order->finished_time) {
                $res['error'] = '订单已完成';
            } else {
                $order->received_time = $time ?? time();
                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单确认完成
     */
    public static function doFinished($id, $time = null)
    {
        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if (!$order->pay_status) {
                $res['error'] = '订单未付款';
            } elseif (!$order->ship_status) {
                $res['error'] = '订单未发货';
            } elseif (!$order->delivery_time) {
                $res['error'] = '订单未送达';
            } elseif (!$order->received_time) {
                $res['error'] = '订单未收货';
            } elseif ($order->finished_time) {
                $res['error'] = '订单已完成';
            } else {
                $order->finished_time = $time ?? time();
                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单取消
     */
    public static function doCancel($id, $force = false, $flag = '', $notice = '')
    {
        $res = self::getOrder($id);

        if (empty($res['error'])) {
            $order = $res['data'];
            if (!$force) {
                if ($order->finished_time) {
                    $res['error'] = '订单已完成';
                } elseif ($order->received_time) {
                    $res['error'] = '订单已收货';
                } elseif ($order->delivery_time) {
                    $res['error'] = '订单已送达';
                } elseif ($order->ship_status) {
                    $res['error'] = '订单已发货';
                } elseif ($order->pay_status) {
                    $res['error'] = '订单已付款';
                }
            }
            if (empty($res['error'])) {
                $order->status = 0;

                $order->flag = $flag == 'none' ? '' : $flag;;
                $order->notice = $notice;

                $order->save();
            }
            $res['data'] = $order;
        }
        return $res;
    }

    /**
     * 订单标记
     */
    public static function doNotice($id, $flag = '', $notice = '')
    {
        $res = self::getOrder($id);
        if (!empty($res['data'])) {
            $order = $res['data'];

            $order->flag = $flag == 'none' ? '' : $flag;
            $order->notice = $notice;

            $order->save();
            $res['data'] = $order;
        }
        return $res;
    }
}
