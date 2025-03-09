<?php
declare (strict_types = 1);

namespace app\brand\service\traits;

trait DeleteTrait {
    public function delete($data)
    {
        return $this->modelClassName::destroy($data['id']);
    }
}
