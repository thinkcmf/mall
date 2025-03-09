<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait ReadTrait {
    public function read($data)
    {
        return $this->modelClassName::find($data['id']);
    }
}
