<?php

use mon\console\interfaces\Command;

class C implements Command
{
    /**
     * 执行指令
     *
     * @return [type] [description]
     */
    public function execute($input, $output)
    {
        return $output->dump(__FILE__);
    }

    /**
     * 定义指令名
     *
     * @return void
     */
    public static function getCommandName()
    {
        return 'C';
    }
}
