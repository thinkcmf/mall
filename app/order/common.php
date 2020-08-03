<?php
function cmf_create_order_sn($value){
    //TODO 自定义前缀+时间+00+value
    //$value = cmf_get_option('mall').date('YmdHis').'00'.$value;
    return $value;
}