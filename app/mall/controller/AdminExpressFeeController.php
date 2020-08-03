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
use app\mall\model\ExpressModel;
use app\mall\model\ExpressFeeModel;
use app\mall\service\ExpressService;

class AdminExpressFeeController extends AdminBaseController
{

    // 页面顶部 NavTabs 内容
    public $navTabs = [];

    protected $express;

    protected function initialize()
    {

        $express_id = $this->request->param('express_id', 0, 'intval');

        if ($express_id < 1) {
            $this->error("物流ID为空！", url("admin_express/index"));
        }

        $model = new ExpressModel();
        $express  = $model->get($express_id);
        if (empty($express)) {
            $this->error("物流ID错误！", url("admin_express/index"));
        }
        $this->express_id = $express_id;
        $this->express = $express;

        $name = '<h3>编辑：' . $this->express->name . '</h3>';
        $this->assign('before_nav_tabs', $name);

        $this->navTabs = [
            // 'index'     => ['url' => 'admin_express\index', 'name' => '返回物流列表'],
            'express'     => ['url' => 'admin_express/edit', 'params' => ['id' => $express_id], 'name' => '基础信息'],
            'fee'       => ['url' => 'index', 'params' => ['express_id' => $express_id], 'name' => '物流资费'],
        ];

        parent::initialize();
    }

    /**
     * 资费管理
     * @adminMenu(
     *     'name'   => '资费管理',
     *     'parent' => 'mall/AdminExpress/index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 1100,
     *     'icon'   => '',
     *     'remark' => '资费管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->assign('active', 'fee');

        $table = [
            'thead'      => [],
            'act_col'    => [],
            'data'       => [],
            'list_order' => '',
            'batch'      => [],
        ];

        // 获取数据
        $fee = [];
        ExpressService::getFee($this->express_id)->each(function($item) use(&$fee){
            $fee[$item->province_id] = $item->toArray();
        });
        $provinces = ExpressService::getProvinces();
        if(is_string($provinces)){
            exit($provinces);
        }
        // 赋值模板
        $this->assign('provinces', $provinces);
        $this->assign('fee', $fee);
        $this->assign('express', $this->express);

        // 返回渲染结果
        $view_content = [];

        // $view_content[] = $this->express->name;

        $view_content[] = $this->htmlBlock('nav_tabs');
        $view_content[] = $this->htmlBlock('fee_form', 'mall@admin_express/');

        // $view_content[] = $this->htmlBlock('grid');

        $this->assign('content', $view_content);
        return $this->fetchPage();
    }

    /**
     * 资费提交
     * @adminMenu(
     *     'name'   => 'FEE提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => 'Fe e提交',
     *     'param'  => ''
     * )
     */
    public function post()
    {
        $data   = $this->request->param();
        // dump($data);
        
        // 获取数据
        $fee = [];
        ExpressService::getFee($this->express_id)->each(function($item) use(&$fee){
            $fee[$item->province_id] = $item;
        });

        $provinces = ExpressService::getProvinces()->each(function($item) use($data, $fee){
            $express_id = $data['express_id'];
            $grid = $data['grid'];

            $row = $grid[$item['id']];
            $row['express_id'] = $express_id;
            $row['province'] = $item['name'];
            $row['province_id'] = $item['id'];

            $model = $fee[$item['id']] ?? false;
            if($model){
                $model->allowField(true)->isUpdate(true)->save($row);
            }else{
                $model  = new ExpressFeeModel();
                $model->allowField(true)->save($row);
            }
        });

        $this->success("保存成功！", url('index', ['express_id' => $this->express_id]));
    }

}
