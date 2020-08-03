<?php


namespace api\mall\controller;


use api\common\base\AuthBaseController;

class LogisticsController extends AuthBaseController
{
    public function index()
    {

    }

    /**
     * 轨迹
     */
    public function trail()
    {
        $params = [
            'code'   => $this->request->param('code', ''),
            'no' => $this->request->param('no', ''),
            'mobile' => $this->request->param('mobile', '')
        ];
        /**
         * 插件返回数据
         * $params['type'] = array | html | json
         *
         * 示例
         * $params['type'] = 'array'; | isset($params['type']) = false;
         * 无数据
         * $info = [];
         * 有数据
         * $info = [
         *      [
         *          'date'=>'2020-7-7 ...',
         *          'desc'=>'.....'
         *      ],
         * ];
         */
        $info   = hook_one('mall_trail', $params);
        if ($info === false) {
            $this->error('未安装物流轨迹插件');
        }
        $this->success('ok', $info);
    }
}