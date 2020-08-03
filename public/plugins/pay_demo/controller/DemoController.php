<?php

namespace plugins\pay_demo\controller;

use api\mall\service\OrderService;
use cmf\controller\PluginRestBaseController;

use think\facade\Log;

class DemoController extends PluginRestBaseController
{
    protected $config = [];

    public function initialize()
    {
        
    }

    public function index()
    {
        $this->notify();
    }

    public function return()
    {

    }

    public function notify()
    {

        //由于是模拟的 这里就留空
    }
}
