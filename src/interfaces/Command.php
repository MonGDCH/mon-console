<?php

declare(strict_types=1);

namespace mon\console\interfaces;

use mon\console\Input;
use mon\console\Output;

/**
 * 指令接口
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
interface Command
{
    /**
     * 执行指令的接口方法
     *
     * @param Input $input		输入实例
     * @param Output $output	输出实例
     * @return mixed
     */
    public function execute(Input $input, Output $output);
}
