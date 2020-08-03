<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace plugins\express;


use app\mall\model\ExpressModel;
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
        foreach ($info as $key => $val) {
            $find  = ExpressModel::where('code', $key)->find();
            $value = [
                'code'        => $key,
                'name'        => $val,
                'alias'       => $val,
                'description' => $val,
                'remark'      => $val
            ];
            if ($find) {
                ExpressModel::where('code', $key)->update($value);
            } else {
                $data[] = $value;
            }


        }
        if (!empty($data)) {
            Db::name('express')->insertAll($data);
        }

        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

    public function expressTrail($param)
    {
        $config = $this->getConfig();
        if (!isset($param['no']) || !isset($param['code']) || empty($param['no']) || empty($param['code'])) {
            return false;
        }
        $key = 'mall-express-track-' . $param['no'] . '-' . $param['code'];
        if (cache('?' . $key)) {
            $data = cache($key);
        } else {
            $waybill               = new \plugins\express\lib\Waybill($param['no'], $param['code']);
            $waybill->customerName = substr(isset($param['mobile']) && !empty($param['mobile']) ? $param['mobile'] : '111111', -4);

            (new \plugins\express\lib\Trackers\Kuaidiniao($config['appid'], $config['apikey']))->track($waybill);
            $data = $waybill->getTraces()->sort();
            $data = $data->toArray();
            $data = array_map(function($val){
                $temp = [
                    'date'=>$val['datetime'],
                    'desc'=>$val['desc']
                ];
                return $temp;
            },$data);
            cache($key, $data, $config['cacheTime']);
        }
        $param['type'] = isset($param['type'])?:'array';
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

    /**
     * 快递公司
     *
     * @return string[]
     */
    public function expressChannel(){
        return $this->express;
    }
}
