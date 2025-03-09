<?php
declare (strict_types = 1);

namespace app\brand\service;

class BaseService
{
    protected Object $app;

    protected string $modelClassName;

    public function __construct(){
        $this->app = app();
        $this->initialize();
    }

    protected function initialize()
    {

    }


}
