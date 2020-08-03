<?php


namespace app\common\tratis;


trait Error
{
    protected $error = '';

    protected function setError($error = '')
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }
}