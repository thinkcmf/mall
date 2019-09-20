<?php

namespace plugins\express\lib;

class Config
{
    public static function codeMap($key, $code = false)
    {
        $data = [
            'Kuaidiniao' => [
                'sf'        => 'SF',        //顺丰快递		 
                'yto'       => 'YTO',        //圆通快递		 
                'sto'       => 'STO',        //申通快递		 
                'zto'       => 'ZTO',        //中通快递		 
                'ems'       => 'EMS',    //EMS速递		 
                'yunda'     => 'YD',    //韵达快递		 
                'chinapost' => 'YZPY',    //邮政包裹		 
                'best'      => 'HTKY',        //百世汇通
                'gto'       => 'GTO',    //国通快递	
                'ttk'       => 'HHTT',    //天天快递		 
                'yousu'     => 'UC',    //优速快递		 	 
                'zaijisong' => 'ZJS',    //宅急送
            ]
        ];
        if ($code) {
            return $data[$key][$code];
        }
        return $data[$key];
    }
}