<?php

namespace mon\console;

use mon\console\Input;
use mon\console\Output;

/**
 * 指令基类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
abstract class Command implements \mon\console\interfaces\Command
{
	/**
	 * 默认指令名 
	 *
	 * @var string
	 */
	protected static $defaultName;

	/**
	 * 默认指令别名
	 *
	 * @var string
	 */
	protected static $defaultAliasName;

	/**
	 * 默认指令描述
	 *
	 * @var string
	 */
	protected static $defaultDescription;

	/**
	 * 默认指令组别
	 *
	 * @var string
	 */
	protected static $defaultGroup = 'available';

	/**
	 * 执行指令的接口方法
	 *
	 * @param Input $input		输入实例
	 * @param Output $output	输出实例
	 * @return mixed
	 */
	abstract public function execute(Input $input, Output $output);

	/**
	 * 获取指令名称
	 *
	 * @return string|null
	 */
	public static function getCommandName(): ?string
	{
		return static::$defaultName;
	}

	/**
	 * 获取指令别名
	 *
	 * @return string|null
	 */
	public static function getCommandAliasName(): ?string
	{
		return static::$defaultAliasName;
	}

	/**
	 * 获取指令描述
	 *
	 * @return string|null
	 */
	public static function getCommandDesc(): ?string
	{
		return static::$defaultDescription;
	}

	/**
	 * 获取指令组别
	 *
	 * @return string
	 */
	public static function getCommandGroup(): string
	{
		return static::$defaultGroup;
	}
}
