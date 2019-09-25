<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace plugins\express;


use cmf\lib\Plugin;
use think\Db;


class ExpressPlugin extends Plugin
{
    public $info = [
        'name'        => 'Express',
        'title'       => '快递物流插件',
        'description' => '快递物流插件',
        'status'      => 1,
        'author'      => '五五',
        'version'     => '1.0',
        'demo_url'    => 'http://www.thinkcmf.com',
        'author_url'  => 'http://www.thinkcmf.com'
    ];

    public $hasAdmin = 0;//插件是否有后台管理界面

    protected $express = [
        'sf'        => '顺丰快递',
        'yto'       => '圆通快递',
        'sto'       => '申通快递',
        'zto'       => '中通快递',
        'ems'       => 'ems速递',
        'yunda'     => '韵达快递',
        'best'      => '百世汇通',
        'gto'       => '国通快递',
        'ttk'       => '天天快递	',
        'zaijisong' => '宅急送',
    ];

    // 插件安装
    public function install()
    {
        $info = $this->express;
        $data = [];
        foreach ($info as $key => $val){
            $find = Db::name('order_shipment')->where('code',$key)->find();
            $value = [
                'status'=>1,
                'code'=>$key,
                'name'=>$val,
                'description'=>$val
            ];
            if($find){
                Db::name('order_shipment')->where('code',$key)->update($value);
            }
            $data[] = $value;

        }
        if(!empty($data)){
            Db::name('order_shipment')->insertAll($data);
        }

        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        Db::name('order_shipment')->where('code','in',array_keys($this->express))->update(['status'=>0]);
        return true;//卸载成功返回true，失败false
    }

    public function mallExpressTrack($param)
    {
        $config = $this->getConfig();
        if (!isset($param['number']) || !isset($param['code']) || empty($param['number']) || empty($param['code'])) {
            return false;
        }
        $key = 'mall-express-track-' . $param['number'] . '-' . $param['code'];
        if (cache('?' . $key)) {
            $data = cache($key);
        } else {
            $waybill = new \plugins\express\lib\Waybill($param['number'], $param['code']);
            $waybill->customerName = substr(isset($param['mobile']) && !empty($param['mobile'])?$param['mobile']:'111111',-4);

            (new \plugins\express\lib\Trackers\Kuaidiniao($config['appid'], $config['apikey']))->track($waybill);
            $data = $waybill->getTraces()->sort();
            $data = $data->toArray();
            cache($key, $data, 1200);
        }

        switch ($param['type']) {
            case 'html':
                $str = '<ul class="list-group">';
                foreach ($data as $info) {
                    $str .= '<li class="list-group-item"><span>' . $info['datetime'] . '</span> <span>' . $info['desc'] . '</span> <span>' . $info['memo'] . '</span></li>';
                }
                $str .= '<ul>';
                return $str;
                break;
            case 'array':
                return $data;
                break;
            default:
                return \json_encode($data);
        }
    }
}
