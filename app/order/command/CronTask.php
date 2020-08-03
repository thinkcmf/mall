<?php

namespace app\order\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class CronTask extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('crontask')
        ->setDescription('定时计划执行的命令');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        
    	// 指令输出
    	$output->writeln('crontask ok');
    }
}
