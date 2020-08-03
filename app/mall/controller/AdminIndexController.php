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
namespace app\mall\controller;

use app\mall\base\AdminBaseController;

/**
 * Class AdminIndexController
 * @package app\mall\controller
 * @adminMenuRoot(
 *     'name'   =>'商城管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 0,
 *     'icon'   =>'shopping-cart',
 *     'remark' =>'商城管理入口'
 * )
 */
class AdminIndexController extends AdminBaseController
{

    // 页面顶部 NavTabs 内容
    public $navTabs = [
        'index' => ['url' => 'index', 'name' => '基础配置']
    ];

    /**
     * 商城配置
     * @adminMenu(
     *     'name'   => '商城配置',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '商城配置',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->assign([
            'nav_tabs'=>$this->navTabs
        ]);
        return $this->save();
    }

    protected function save(){
        $key = $this->request->param('key','mall-index');
        if($this->request->isPost()){
            cmf_set_option($key,$this->request->param('data/a'));
            $this->success('操作成功');
        }
        $this->assign([
            'data'=>cmf_get_option($key)
        ]);
        return $this->fetch(':config');
    }

}
