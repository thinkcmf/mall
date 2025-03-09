<?php
declare (strict_types = 1);

namespace app\brand\validate;

use think\Validate;

class BrandInfoValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'  => ['require'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [

    ];

    /**
     * 定义范围
     *
     * @var array
     */
    protected $scene = [
        'delete'=>['id'],
        'update'=>['id'],
        'read'  =>['id']
    ];

    protected function sceneIndex()
    {
        return $this->only(['id'])->remove('id',true);
    }

    protected function sceneLists()
    {
        return $this->only(['id'])->remove('id',true);
    }

    protected function sceneCreate()
    {
        return $this->only(['id'])->remove('id',true);
    }
}
