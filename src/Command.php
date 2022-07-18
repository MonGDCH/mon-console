<?php

namespace mon\console;

use mon\console\Input;
use mon\console\Output;

/**
 * 指令基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
abstract class Command
{
	/**
	 * 执行指令的接口方法
	 *
	 * @param Input $input		输入实例
	 * @param Output $output	输出实例
	 * @return mixed
	 */
	abstract public function execute($input, $output);
}
