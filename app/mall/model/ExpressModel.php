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
namespace app\mall\model;

// use think\Model;

class ExpressModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 物流资费 关联模型
     * 
     * @return 
     */
    public function fee()
    {
        return $this->hasMany(ExpressFeeModel::class, 'express_id');
    }
}