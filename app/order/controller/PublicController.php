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
use think\Hook;

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

        $order     = Db::name('order')->where(['user_id' => $userId, 'order_sn' => $orderSn])->find();
        $orderMore = $order['more'];
        if (empty($orderMore)) {
            $this->error('订单确认失败！');
        }

        $orderMore = json_decode($orderMore, true);
        if ($orderMore['user_confirm_code'] == $code) {

            if ($order['user_confirmed'] == 1) {
                $this->error('订单已经确认！');
            }

            $items = Db::name('order_item')->where('order_id', $order['id'])->select();

            if (!$items->isEmpty()) {
                $allPass = true;
                Db::startTrans();

                foreach ($items as $item) {
                    $params = ['order_item' => $item, 'order_sn' => $order['order_sn'], 'user_id' => $order['user_id']];

                    $tableNameArr = explode('_', $item['table_name']);

                    $app = $tableNameArr[0];

                    $class = 'app\\' . $app . '\\behavior\\OrderConfirmCallback' . cmf_parse_name($item['table_name'], 1) . "Behavior";

                    if (class_exists($class)) {
                        try {
                            Hook::exec($class, 'run', $params);
                        } catch (\Exception $e) {
                            $allPass = false;
                            Db::rollback();
                            file_put_contents('OrderConfirmCallback.log', $e->getMessage() . "\n\n\n", 8);
                        }
                    }
                }


                if ($allPass) {
                    try {
                        Db::name('order')->where('id', $order['id'])->update(['user_confirmed' => 1, 'confirm_time' => time()]);
                        Db::name('order_item')->where('order_id', $order['id'])->update(['confirm_time' => time()]);
                        $findNotReadyOrderItemCount = Db::name('order_item')->where('order_id', $order['id'])
                            ->whereExp('goods_quantity', '>goods_quantity_locked')->count();

                        // 如果订单产品库存已够，可以发货了
                        if ($findNotReadyOrderItemCount == 0) {
                            Db::name('order')->where('id', $order['id'])->update([
                                'is_inventory_ready' => 1 // 库存完成
                            ]);
                        }
                        Db::commit();
                    } catch (\Exception $E) {
                        Db::rollback();
                        $this->error('订单确认失败！' . $E->getMessage());
                    }

                } else {
                    $this->error('订单确认失败！');
                }

            } else {

            }
            $this->success('订单确认成功！');
        } else {
            $this->error('code 无效！');
        }

        return "订单确认成功！";

    }


}