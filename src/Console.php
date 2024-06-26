<?php

declare(strict_types=1);

namespace mon\console;

use Closure;
use Throwable;
use mon\console\Input;
use mon\console\Output;
use mon\console\libs\Util;
use mon\console\libs\Style;
use InvalidArgumentException;
use mon\console\exception\ConsoleException;

/**
 * mon-console控制台
 *
 * @author Mon <985558837@qq.com>
 * @version 1.1.0
 */
class Console
{
	/**
	 * 输入实例
	 *
	 * @var Input
	 */
	protected $input;

	/**
	 * 输出实例
	 *
	 * @var Output
	 */
	protected $output;

	/**
	 * 执行的指令
	 *
	 * @var string
	 */
	protected $command;

	/**
	 * 注册的指令
	 *
	 * @var array
	 */
	protected $commands = [];

	/**
	 * 应用标题
	 *
	 * @var string
	 */
	protected $title = <<<TITLE
  _____   _       _____             _____   _____  
 / ____| | |     |_   _|     /\    |  __ \ |  __ \ 
| |      | |       | |      /  \   | |__) || |__) |
| |      | |       | |     / /\ \  |  ___/ |  ___/ 
| |____  | |____  _| |_   / ____ \ | |     | |     
 \_____| |______||_____| /_/    \_\|_|     |_|     
													 
TITLE;

	/**
	 * 指令的描述
	 *
	 * @var array
	 */
	protected $messages = [
		'help'	=> [
			'alias'	=> 'h',
			'group'	=> 'internal',
			'desc'	=> 'Show command help information'
		],
	];

	/**
	 * 是否显示帮助信息指令
	 *
	 * @var boolean
	 */
	protected $showHelpCommand = false;

	/**
	 * 初始化
	 *
	 * @param Input|null $input		输入对象实例
	 * @param Output|null $output	输出对象实例
	 */
	public function __construct(Input $input = null, Output $output = null)
	{
		$this->input = $input ?: Input::instance();
		$this->output = $output ?: Output::instance();
		$this->command = $this->input->getCommand();
	}

	/**
	 * 设置应用标题
	 *
	 * @param string $title
	 * @return Console
	 */
	public function setTitle(string $title): Console
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * 设置默认是否显示help指令
	 *
	 * @param boolean $show
	 * @return Console
	 */
	public function setShowHelpCommand(bool $show): Console
	{
		$this->showHelpCommand = $show;
		return $this;
	}

	/**
	 * 获取应用标题
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * 获取设置显示帮助信息指令
	 *
	 * @return boolean
	 */
	public function getShowHelpCommand(): bool
	{
		return $this->showHelpCommand;
	}

	/**
	 * 执行应用
	 *
	 * @param boolean $exit	是否结束应用
	 * @return void
	 */
	public function run(bool $exit = true)
	{
		if (!$this->command) {
			return $this->showHelp();
		} elseif ($this->command == 'help' || $this->command == '-h') {
			return $this->showHelp();
		}

		// 执行指令
		$status = 0;
		try {
			if (isset($this->commands[$this->command])) {
				$status = $this->hanedle((string) $this->command, $this->commands[$this->command]);
			} else {
				return $this->showError("The command [{$this->command}] not exists!");
			}
		} catch (Throwable $e) {
			$status = $e->getCode() !== 0 ? $e->getCode() : 0;
			$error = sprintf("Exception(%d): %s\nFile: %s(Line %d)\nTrace:\n%s\n", $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

			return $this->showError($error, $status);
		}

		if ($exit) {
			exit((int) $status);
		}
	}

	/**
	 * 执行指令
	 *
	 * @param string $command	指令名
	 * @param Closure|string $handler	指令回调
	 * @throws ConsoleException
	 * @return mixed
	 */
	public function hanedle(string $command, $handler)
	{
		if ($handler instanceof Closure) {
			// 匿名函数
			return $handler($this->input, $this->output);
		} elseif (class_exists($handler) && is_subclass_of($handler, "\\mon\\console\\interfaces\\Command")) {
			$instance = new $handler();
			return $instance->execute($this->input, $this->output);
		}

		throw new ConsoleException('The execute method is not found in the command: ' . $command);
	}

	/**
	 * 注册指令
	 *
	 * @param string $command		指令名
	 * @param mixed $handle			指令回调
	 * @param array|string $option	指令参数
	 * @return Console
	 */
	public function addCommand(string $command, $handle, $option = []): Console
	{
		$parse = $this->parseOption($option);

		return $this->recordMessgae($command, $parse['group'], $parse['desc'], $parse['alias'])->recordHandle($command, $handle, $parse['alias']);
	}

	/**
	 * 记录指令
	 *
	 * @param string $command	指令名
	 * @param mixed $handle		指令回调
	 * @param string $alias		指令别名
	 * @return Console
	 */
	protected function recordHandle(string $command, $handle, string $alias = null): Console
	{
		$this->commands[$command] = $handle;
		if (!is_null($alias)) {
			$alias = '-' . $alias;
			$this->commands[$alias] = $handle;
		}

		return $this;
	}

	/**
	 * 记录指令信息
	 *
	 * @param string $command	指令名
	 * @param string $desc		指令描述
	 * @param string $alias		指令别名
	 * @return Console
	 */
	protected function recordMessgae(string $command, string $group, string $desc = null, string $alias = null): Console
	{
		$this->messages[$command] = [
			'desc'	=> $desc,
			'alias'	=> $alias,
			'group'	=> $group
		];

		return $this;
	}

	/**
	 * 解析准备注册的指令的其他信息
	 *
	 * @param array|string $option	指令参数
	 * @throws InvalidArgumentException
	 * @return array
	 */
	protected function parseOption($option): array
	{
		$parse = [];
		if (is_array($option)) {
			$parse['desc'] = $option['desc'] ?? null;
			$parse['alias'] = $option['alias'] ?? null;
			$parse['group'] = $option['group'] ?? 'available';
		} elseif (is_string($option)) {
			$parse['desc'] = $option;
			$parse['alias'] = null;
			$parse['group'] = 'available';
		} else {
			throw new InvalidArgumentException('Command option invalid arguments.');
		}

		return $parse;
	}

	/**
	 * 显示帮助
	 *
	 * @return void
	 */
	public function showHelp()
	{
		$this->title && $this->output->write("\n" . $this->title);
		$this->output->write(Util::wrapTag('Usage:', 'yellow'), true);
		$this->output->write('php ' . $this->input->getScript() . ' ' . Util::wrapTag('{{command}}', 'green') . ' [--opt -v -h ...] [arg0 arg1 arg2=value2 ...]' . PHP_EOL, true);
		// 整理指令信息
		$data = [];
		foreach ($this->messages as $command => $option) {
			$group = $option['group'] ?: 'available';
			$desc = $option['desc'] ?: '';
			$alias = $option['alias'] ?: '';

			if (!$this->showHelpCommand && $command == 'help' && $group == 'internal') {
				continue;
			}

			$data[strtolower($group)][$command] = [
				'desc'	=> $desc,
				'alias'	=> $alias
			];
		}
		// 整理数据内容
		$string = '';
		foreach ($data as $group => $cmds) {
			$groupStr = '';
			$groupStr .= Util::wrapTag('- ' . ucfirst($group) . ' Commands:', 'white') . PHP_EOL;
			$len = Util::getKeyMaxWidth($cmds) + 4;
			foreach ($cmds as $cmd => $opt) {
				$str = '  ' . Util::wrapTag(str_pad($cmd, $len, ' '), 'success') . ' ' . $opt['desc'];
				if (isset($opt['alias']) && !empty($opt['alias'])) {
					$str .= Util::wrapTag(' (alias: ' . $opt['alias'] . ') ', 'cyan');
				}
				$groupStr .= $str . PHP_EOL;
			}
			$string .= $groupStr . PHP_EOL;
		}

		return $this->output->write($string, true, true);
	}

	/**
	 * 显示错误信息
	 *
	 * @param string $error	错误信息
	 * @param integer $ststus 结束状态值
	 * @return void
	 */
	public function showError(string $error = '', int $ststus = 0)
	{
		if ($error) {
			$this->output->write(Style::color("<red>[ERROR]</red>: {$error}"));
		}
		exit($ststus);
	}
}
