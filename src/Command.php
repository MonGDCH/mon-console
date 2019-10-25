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
	 * 构造方法
	 */
	public function __construct()
	{ }

	/**
	 * 执行指令的接口方法
	 *
	 * @return [type] [description]
	 */
	abstract public function execute($input, $output);
}
