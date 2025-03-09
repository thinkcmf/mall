<?php
declare (strict_types=1);

namespace app\brand\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * @mixin \think\Model
 */
class BrandModel extends Model
{
    protected $name = 'brand';

    protected $autoWriteTimestamp = true;

    use SoftDelete;

    protected $defaultSoftDelete = 0;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function getLogoAttr($value): string
    {
        return empty($value) ? '' : cmf_get_image_url($value);
    }
}
