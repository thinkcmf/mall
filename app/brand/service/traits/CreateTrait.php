<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait CreateTrait {
    public function create($data)
    {
        return $this->modelClassName::create($data);
    }
}
