<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait ListsTrait {
    public function lists($data)
    {
        $limit = $data['limit'] ?? 15;
        $where = [
            ...$data['where']??[]
        ];
        $order = $data['order']??'id DESC';
        return $this->modelClassName::where($where)->limit($limit)->order($order)->select();
    }
}