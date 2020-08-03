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

use api\mall\model\CartModel;
use api\mall\model\OrderModel;
use app\mall\model\MallGoodsSkuModel;
use app\mall\service\ExpressService;
use think\Validate;

/**
 * 服务提供： 下单服务
 *
 */
class CheckoutService
{
    protected $preBill;
    protected $preItems;
    protected $preAddress;
    protected $preShipfee;
    protected $preRemark;

    /**
     * 预下单
     */
    public function checkout(Int $userId, array $goods, array $address = [], String $remark = '')
    {
        $checkBill = $this->checkBill($goods);
        $bill = $checkBill['bill'];
        $items = $checkBill['items'];

        $address = $this->checkAddress($address, $userId);
        $shipfee = [];
        if ($bill['total_weight'] > 0) {
            $shipfee = $this->getShipfee($address['province'], $bill['total_weight']);
            if (empty($shipfee['error'])) {
                $bill['amount_shipfee'] += $shipfee['shipfee'];
            }
        }

        $bill['amount_payable'] = $bill['amount_goods'] + $bill['amount_shipfee'] - $bill['amount_offset'];

        $remark = self::checkRemark($remark);

        $this->preBill    = $bill;
        $this->preItems   = $items;
        $this->preAddress = $address;
        $this->preShipfee = $shipfee;
        $this->preRemark = $remark;

        $error = $this->getError();
        $data = $this->getPreData();
        $data['error'] = $error;

        return $data;
    }

    public function getError($errSeparate = true)
    {
        $err = [];
        if (!empty($this->preBill['error'])) {
            $err['bill'] = $this->preBill['error'];
            if ($errSeparate) {
                unset($this->preBill['error']);
            }
        }

        if (!empty($this->preAddress['error'])) {
            $err['address']  = $this->preAddress['error'];
            if ($errSeparate) {
                unset($this->preAddress['error']);
            }
        }

        if (!empty($this->preShipfee['error'])) {
            $err['shipfee']  = $this->preShipfee['error'];
            if ($errSeparate) {
                unset($this->preShipfee['error']);
            }
        }

        if (!empty($this->preRemark['error'])) {
            $err['remark']  = $this->preRemark['error'];
            if ($errSeparate) {
                unset($this->preRemark['error']);
            }
        }

        return $err;
    }

    public function getPreData()
    {
        $res['bill'] = $this->preBill;
        $res['items'] = $this->preItems;
        $res['address'] = $this->preAddress;
        $res['shipfee'] = $this->preShipfee;
        // remark字段太长了，不返回了
        // $res['remark'] = $this->preRemark['remark'];

        return $res;
    }

    /**
     * 提交订单
     */
    public function submitOrder(Int $userId, array $goods, array $address = [], String $remark = '')
    {
        $data = $this->checkout($userId, $goods, $address, $remark);

        if (!empty($data['error'])) {
            return $data;
        }

        $orderService = new OrderService();
        $order = $orderService->doPlace($userId, $data['items'], $data['bill'], $data['address'], $this->preRemark['remark']);

        if (!empty($order)) {
            $skuIds = $data['items']->column('sku_id');
            $map = [
                ['user_id', '=', $userId],
                ['sku_id', 'in', $skuIds],
            ];

            $skuData = [];
            $skuData['status'] = 0;
            $skuData['quantity'] = 0;
            $skuData['selected'] = 0;
            CartModel::where($map)->update($skuData);
        }

        return $order;
    }

    /**
     * 校验订单账单
     */
    public function checkBill($goods)
    {
        $validData = [];
        $invalidData = [];
        $errorData = [];
        foreach ($goods as $id => $number) {
            if (intval($id) > 0 && intval($number) > 0) {
                $validData[$id] = $number;
            } else {
                $invalidData[$id] = $number;
            }
        }

        $skusInDb = [];
        $idsInDb = [];
        if ($validData) {
            $validIds = array_keys($validData);
            $skus = MallGoodsSkuModel::all(
                function ($query) use ($validIds) {
                    $query->where('id', 'in', $validIds);
                    $query->where('delete_time', '=', 0);
                },
                ['goods' => function ($query) {
                    $query->where('delete_time', '=', 0);
                }]
            )->filter(function ($sku) use (&$errorData, &$skusInDb, &$idsInDb) {
                $skusInDb[] = $sku;
                $idsInDb[] = $sku->id;
                if ($sku->status && $sku->goods && $sku->goods->status) {
                    return true;
                }
                $errorData[$sku->id] = $sku;
                return false;
            });

            $invalidIds = array_diff($validIds, $idsInDb);
            foreach ($invalidIds as $id) {
                $invalidData[$id] = $validData[$id];
                unset($validData[$id]);
            }
        }

        $error = [];
        $errorMsg = '';
        if (!empty($invalidData)) {
            $invalidInfo = '！存在无效数据（';
            foreach ($invalidData as $key => $val) {
                $invalidInfo .= 'ID：' . $key . '，数量：' . $val;
                $error[$key] = 'ID：' . $key . '，数量：' . $val . '，数据无效';
            }
            $errorMsg .= $invalidInfo . '）';
        }
        if (!empty($errorData)) {
            $errorInfo = [];
            $errorInfo[] = '！！以下商品无法结算：';
            foreach ($errorData as $id => $sku) {
                $msg = '';
                if (empty($sku->goods)) {
                    $msg = 'SKU所属商品已删除或下架';
                } elseif ($sku->goods->status != 1) {
                    $msg = 'SKU所属商品已下架';
                } elseif ($sku->status != 1) {
                    $msg = 'SKU已下架';
                }
                $errorInfo[] = 'ID：' . $id . '，' . $msg . '；';
                $error[$id] = 'ID：' . $id . '，' . $msg;
            }
            $errorMsg .= implode('', $errorInfo);
        }

        $bill = [
            // 'id'             => null,
            // 'user_id'        => null,
            // 'sn'             => null,
            // 'channel'        => null,
            // 'create_time'    => null,
            // 'update_time'    => null,
            // 'delete_time'    => null,
            // 'expire_time'    => null,
            // 'status'         => null,
            // 'country'        => null,
            // 'province'       => null,
            // 'city'           => null,
            // 'district'       => null,
            // 'town'           => null,
            // 'area_code'      => null,
            // 'address'        => null,
            // 'consignee'      => null,
            // 'zip_code'       => null,
            // 'email'          => null,
            // 'phone'          => null,
            // 'phone2'         => null,
            // 'remark'         => null,
            'total_weight'   => 0,
            'total_item'     => 0,
            'amount_goods'   => 0.00,
            'amount_shipfee' => 0.00,
            'amount_offset'  => 0.00,
            'amount_payable' => 0.00,
            // 'pay_status'     => null,
            // 'pay_up_time'    => null,
            // 'pay_time'       => null,
            // 'pay_amount'     => null,
            // 'pay_method'     => null,
            // 'pay_sn'         => null,
            // 'pay_info'       => null,
            // 'ship_status'    => null,
            // 'ship_time'      => null,
            // 'ship_code'      => null,
            // 'ship_name'      => null,
            // 'ship_sn'        => null,
            // 'delivery_time'  => null,
            // 'received_time'  => null,
            // 'finished_time'  => null,
            // 'flag'           => null,
            // 'notice'         => null,
        ];

        $items = $skus->map(function ($sku) use ($goods, &$bill) {
            $quantity = $goods[$sku->id];
            $weight   = $sku->weight * $quantity;
            $amount   = $sku->price * $quantity;
            $shipfee  = $sku->shipfee * $quantity;
            $offset   = 0;
            $payable  = $amount + $shipfee = $offset;

            $bill['total_weight']   += $weight;
            $bill['total_item']     += $quantity;
            $bill['amount_goods']   += $amount;
            $bill['amount_shipfee'] += $shipfee;
            $bill['amount_offset']  += $offset;

            return [
                // 'item_sn'        => $sku->sn,
                // 'goods_table'    => $sku->goods->getName(),
                'goods_id'       => $sku->goods->id,
                'goods_title'    => $sku->goods->title,
                'thumbnail'      => $sku->goods->thumbnail,
                'thumbnail_url'  => $sku->goods->thumbnail_url,
                // 'sku_table'      => $sku->getName(),
                'sku_id'         => $sku->id,
                'sku_title'      => $sku->title,
                'brand_id'       => $sku->goods->brand_id,
                'quantity'       => (int) $quantity,
                'original_price' => (float) $sku->price,
                'price'          => (float) $sku->price,
                'per_weight'     => (float) $sku->weight,
                'per_shipfee'    => (float) $sku->shipfee,

                'amount'         => $amount,
                'weight'         => $weight,
                'shipfee'        => $shipfee,
                'offset'         => $offset,
                'payable'        => $payable,
            ];
        });

        if ($error) {
            $bill['error'] = $error;
        }

        $res = [
            'bill' => $bill,
            'items' => $items,
        ];

        return $res;
    }

    /**
     * 获取最近一次下单地址
     */
    public static function getAddress(Int $userId, $lastOne = false)
    {
        $map = [];
        $map[] = ['user_id', '=', $userId];

        $fields = 'country,province,city,district,town,area_code,address,consignee,zip_code,email,phone,phone2';
        if ($lastOne) {
            $result = OrderModel::where($map)->field($fields)->find();
        } else {
            $md5s = [];
            $count = 0;
            $max = 2;
            $result = OrderModel::where($map)->field($fields)->select()
                ->map(function ($address) use (&$md5s) {
                    $md5_str  = $address->country;
                    $md5_str .= $address->province;
                    $md5_str .= $address->city;
                    $md5_str .= $address->district;
                    $md5_str .= $address->town;
                    $md5_str .= $address->area_code;
                    $md5_str .= $address->address;
                    $md5_str .= $address->consignee;
                    $md5_str .= $address->zip_code;
                    $md5_str .= $address->email;
                    $md5_str .= $address->phone;
                    $md5_str .= $address->phone2;
                    $md5 = md5($md5_str);
                    if (array_key_exists($md5, $md5s)) {
                        return null;
                    }
                    $md5s[$md5] = $md5;
                    $address->address_md5 = $md5;
                    return $address;
                })->filter(function ($address) use (&$count, $max) {
                    if (empty($address)) {
                        return false;
                    } else {
                        $count += 1;
                        return $count <= $max;
                    }
                })->toArray();
        }

        return $result;
    }

    public function checkAddress($address, $userId)
    {
        $address = $address ?: self::getAddress($userId, true);

        $data['country']   = $address['country'] ?? '中国';
        $data['province']  = $address['province'] ?? '';
        $data['city']      = $address['city'] ?? '';
        $data['district']  = $address['district'] ?? '';
        $data['town']      = $address['town'] ?? '';
        $data['area_code'] = $address['area_code'] ?? '';
        $data['address']   = $address['address'] ?? '';
        $data['consignee'] = $address['consignee'] ?? '';
        $data['zip_code']  = $address['zip_code'] ?? '';
        $data['email']     = $address['email'] ?? '';
        $data['phone']     = $address['phone'] ?? '';
        $data['phone2']    = $address['phone2'] ?? '';

        $rule = [
            'province'  => 'require|max:25',
            'city'      => 'require|max:25',
            'district'  => 'require|max:25',
            'town'      => 'require|max:25',
            'address'   => 'require|length:4,200',
            'consignee' => 'require|length:2,20',
            'email'     => 'email',
            'phone'     => 'require|regex:1\d{10}',
            // 'phone2'    => 'regex:1\d{10}',
        ];
        $msg = [
            'province.require'  => '缺少省份',
            'province.max'      => '省份长度不对',
            'city.require'      => '缺少城市',
            'city.max'          => '城市长度不对',
            'district.require'  => '缺少辖区',
            'district.max'      => '辖区长度不对',
            'town.require'      => '缺少城镇/街道',
            'town.max'          => '城镇/街道长度不对',
            'address.require'   => '缺少详细地址',
            'address.length'    => '详细地址在4～100个字之间',
            'consignee.require' => '缺少联系人',
            'consignee.length'  => '联系人在2～10个字之间',
            'email'             => '邮箱格式不对',
            'phone.require'     => '缺少联系手机',
            'phone'             => '手机号格式不对',
        ];

        $validater = new Validate($rule, $msg);
        $result = $validater->batch()->check($data);
        if (!$result) {
            $data['error'] = $validater->getError();
        }

        return $data;
    }

    /**
     * 按地址、重量和快递公司计算运费
     */
    public function getShipfee(string $province, Int $weight, Int $express_id = 0)
    {
        if ($express_id < 1) {
            $express_id = config('express_id');
        }
        $res = ExpressService::getShipfee($province, $weight, $express_id);
        if (!empty($res['error'])) {
            $res['data']['error'] = $res['error'];
        }
        return $res['data'];
    }

    public static function checkRemark($remark = '')
    {
        $maxLen = 80;
        if (mb_strlen($remark) > $maxLen) {
            $data['error'] = '备注信息请控制在' . $maxLen . '字以内';
            $data['remark'] = mb_substr($remark, 0, $maxLen);
        } else {
            $data['remark'] = $remark;
        }
        return $data;
    }
}
