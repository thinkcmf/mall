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

class ExpressFeeModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        // 'more' => 'array'
    ];

    /**
     * 物流模板 关联模型
     *
     * @return \think\model\relation\BelongsTo
     */
    public function express()
    {
        return $this->belongsTo(ExpressModel::class,'express_id');
    }
}