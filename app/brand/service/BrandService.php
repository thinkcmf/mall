<?php
declare (strict_types = 1);

namespace app\brand\service;

use app\brand\model\BrandModel;

class BrandService extends BaseService
{
    protected function initialize(): void
    {
        parent::initialize();
        $this->modelClassName = BrandModel::class;
    }

    use \app\brand\service\traits\CreateTrait;
    
    use \app\brand\service\traits\ListsTrait;

    use \app\brand\service\traits\PageTrait;
    
    use \app\brand\service\traits\ReadTrait;
    
    use \app\brand\service\traits\UpdateTrait;
    
    use \app\brand\service\traits\DeleteTrait;

    public function status($ids,$param): string
    {
        if (isset($param['ids']) && isset($param["yes"])) {

            $this->modelClassName::where('id' ,'in', $ids)->update(['status' => 0]);
            return '启用成功';
        }
        if (isset($param['ids']) && isset($param["no"])) {
            $this->modelClassName::where('id' ,'in', $ids)->update(['status' => 0]);
            return '禁用成功！';
        }
        return '操作失败！';
    }


}
