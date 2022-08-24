<?php

declare(strict_types=1);

namespace mon\console;

use mon\console\libs\Parse;
use mon\console\libs\Password;

/**
 * 输入操作类
 *
 * @author Mon <985558837@qq.com>
 * @version	1.0.0
 */
class Input
{
	/**
	 * 单例实体
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * 原始argv数据
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * 应用路径
	 *
	 * @var string
	 */
	protected $pwd;

	/**
	 * 完整的请求脚本
	 *
	 * @var string
	 */
	protected $fullScript;

	/**
	 * 执行的脚本
	 *
	 * @var array
	 */
	protected $script;

	/**
	 * 参数
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * 短标签
	 * @var array
	 */
	protected $sOpts = [];

	/**
	 * 长标签
	 *
	 * @var array
	 */
	protected $lOpts = [];

	/**
	 * 指令
	 *
	 * @var string
	 */
	protected $command;

	/**
	 * 获取实例
	 *
	 * @param array $argv
	 * @return Input
	 */
	public static function instance($argv = null): Input
	{
		if (is_null(self::$instance)) {
			self::$instance = new self($argv);
		}
		return self::$instance;
	}

	/**
	 * 构造方法
	 *
	 * @param array  $argv  请求参数
	 */
	protected function __construct($argv = null)
	{
		if (is_null($argv)) {
			$argv = $_SERVER['argv'];
		}

		$this->data = $argv;
		$this->pwd = $this->getPwd();
		$this->fullScript = implode(' ', $argv);
		$this->script = array_shift($argv);

		// 解析参数
		list($this->command, $this->args, $this->sOpts, $this->lOpts) = Parse::parseArgv($argv);
	}

	/**
	 * 读取输入信息
	 *
	 * @param  string $question 若不为空，则先输出文本消息
	 * @param  bool $nl true 会添加换行符 false 原样输出，不添加换行符
	 * @return string
	 */
	public function read(string $question = null, bool $nl = false): string
	{
		if ($question) {
			fwrite(STDOUT, $question . ($nl ? "\n" : ''));
		}

		return trim(fgets(STDIN));
	}

	/**
	 * 获取用户输入密码
	 *
	 * @param string $tips	提示信息
	 * @return string
	 */
	public function password($tips = 'Please Enter Password:'): string
	{
		return Password::interaction($tips);
	}

	/**
	 * 获取路径
	 *
	 * @return string
	 */
	public function getPwd(): string
	{
		if (!$this->pwd) {
			$this->pwd = getcwd();
		}

		return $this->pwd;
	}

	/**
	 * 获取参数
	 *
	 * @param string $key		参数名
	 * @param mixed $default	默认参数值
	 * @return mixed			结果值
	 */
	public function getArgs(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->args;
		}

		return isset($this->args[$key]) ? $this->args[$key] : $default;
	}

	/**
	 * 设置参数
	 *
	 * @param array $val		配置值
	 * @param boolean $replace	是否替换
	 * @return Input
	 */
	public function setArgs(array $val, $replace = false): Input
	{
		$this->args = $replace ? $val : array_merge($this->args, $val);

		return $this;
	}

	/**
	 * 获取短标签
	 *
	 * @param string $key		标签名
	 * @param mixed $default	默认值
	 * @return mixed			标签值
	 */
	public function getSopt(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->sOpts;
		}

		return isset($this->sOpts[$key]) ? $this->sOpts[$key] : $default;
	}

	/**
	 * 设置短标签
	 *
	 * @param array $val		配置值
	 * @param boolean $replace	是否替换
	 * @return Input
	 */
	public function setSopt(array $val, $replace = false): Input
	{
		$this->sOpts = $replace ? $val : array_merge($this->sOpts, $val);

		return $this;
	}

	/**
	 * 获取长标签
	 *
	 * @param string $key		标签名
	 * @param mixed $default	默认值
	 * @return mixed
	 */
	public function getlopt(string $key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->lOpts;
		}

		return isset($this->lOpts[$key]) ? $this->lOpts[$key] : $default;
	}

	/**
	 * 设置长标签
	 *
	 * @param array $val		配置值
	 * @param boolean $replace	是否替换
	 * @return Input
	 */
	public function setlopt(array $val, $replace = false): Input
	{
		$this->lOpts = $replace ? $val : array_merge($this->lOpts, $val);
		return $this;
	}

	/**
	 * 获取指令
	 *
	 * @return string|null
	 */
	public function getCommand(): ?string
	{
		return $this->command;
	}

	/**
	 * 设置指令
	 *
	 * @param string $command	指令名
	 * @return Input
	 */
	public function setCommand($command): Input
	{
		$this->command = $command;

		return $this;
	}
}
