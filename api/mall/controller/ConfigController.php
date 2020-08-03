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
namespace api\mall\controller;

use api\common\base\ApiBaseController;

class ConfigController extends ApiBaseController
{
    public function index()
    {
        $this->request->param('key','');
        $data['site_info'] = $this->getSiteInfo();
        $data['mall_index'] = $this->getMallIndex();
        if(!empty($key)){
            $data = isset($data[$key])?$data[$key]:[];
        }
        $this->success('ok',$data);
    }

    protected function getMallIndex(){
        return cmf_get_option('mall-index');
    }

    protected function getSiteInfo(){
        return cmf_get_option('site_info');
    }
}
