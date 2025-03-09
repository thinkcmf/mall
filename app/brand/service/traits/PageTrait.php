<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait PageTrait {
    public function page($data = [])
    {
        $where = [
            ...$data['where']??[]
        ];
        $order = $data['order']??'id DESC';
        return $this->modelClassName::where($where)->order($order)->paginate();
    }
}