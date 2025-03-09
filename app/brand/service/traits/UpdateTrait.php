<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait UpdateTrait {
    public function update($data)
    {
        return $this->modelClassName::update($data, ['id' => $data['id']]);
    }
}
