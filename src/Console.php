<?php
namespace Mon\console;

use Closure;
use Exception;
use InvalidArgumentException;
use Mon\console\Command;
use Mon\console\CliException;
use Mon\console\Input;
use Mon\console\Output;
use Mon\console\libs\Style;


/**
 * Mon-Console控制台
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Console
{
	/**
	 * 版本信息
	 */
	const VERSION = '1.0.0';

	/**
	 * 输入实例
	 *
	 * @var [type]
	 */
	protected $input;

	/**
	 * 输出实例
	 *
	 * @var [type]
	 */
	protected $output;

	/**
	 * 执行的指令
	 *
	 * @var [type]
	 */
	protected $command;

	/**
	 * 注册的指令
	 *
	 * @var array
	 */
	protected $commands = [];

	/**
	 * 指令的描述
	 *
	 * @var array
	 */
	protected $messages = [
		'help'		=> [
			'alias'		=> 'h',
			'desc'		=> 'Display help for command'
		],
		'version'	=> [
			'alias'		=> 'v',
			'desc'		=> 'Display this console version'
		]
	];

	/**
	 * 初始化
	 *
	 * @param Input $input [description]
	 */
	public function __construct(Input $input = null, Output $output = null)
	{
		$this->input = $input ?: Input::instance();
		$this->output = $output ?: Output::instance();
		$this->command = $this->input->getCommand();
	}

	/**
	 * 执行应用
	 *
	 * @param  boolean $exit 执行完指令是否exit
	 * @return [type]        [description]
	 */
	public function run($exit = true)
	{
		if(!$this->command){
			return $this->showError();
		}
		elseif($this->command == 'help' || $this->command == '-h'){
			return $this->showHelp();
		}
		elseif($this->command == 'version' || $this->command == '-v'){
			return $this->showVersion();
		}

		// 执行指令
		$status = 0;
		try{
			if(isset($this->commands[$this->command])){
				$status = $this->hanedle($this->command, $this->commands[$this->command]);
			}
			else{
                return $this->showError("The command [{$this->command}] not exists!");
            }
		}catch(Exception $e){
			$status = $e->getCode() !== 0 ? $e->getCode() : 0;
			$error = printf("Exception(%d): %s\nFile: %s(Line %d)\nTrace:\n%s\n", $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

			return $this->showError($error, $status);
		}
		if($exit){
			exit((int)$status);
		}
	}

	/**
	 * 执行指令
	 *
	 * @param  [type] $command [description]
	 * @param  [type] $handler [description]
	 * @return [type]          [description]
	 */
	public function hanedle(String $command, $handler)
	{
		if($handler instanceof Closure){
			// 匿名函数
			return call_user_func_array($handler, [$this->input, $this->output]);
		}
		elseif(class_exists($handler) && is_subclass_of($handler, "\\Mon\\console\\Command")){
			$instance = new $handler();
			return call_user_func_array([$instance, 'execute'], [$this->input, $this->output]);
		}

		throw new CliException('The execute method is not found in the command: '.$command);
	}

	/**
	 * 注册指令
	 *
	 * @param [type] $command [description]
	 * @param String $handle  [description]
	 */
	public function addCommand(String $command, $handle, $option = [])
	{
		$parse = $this->parseOption($option);

		return $this->recordMessgae($command, $parse['desc'], $parse['alias'])
					->recordHandle($command, $handle, $parse['alias']);
	}

	/**
	 * 记录指令
	 *
	 * @param  String      $command [description]
	 * @param  [type]      $handle  [description]
	 * @param  String|null $alias   [description]
	 * @return [type]               [description]
	 */
	protected function recordHandle(String $command, $handle, String $alias = null)
	{
		$this->commands[$command] = $handle;
		if(!is_null($alias)){
			$alias = ($alias[0] == '-') ? $alias : '-'.$alias;
			$this->commands[$alias] = $handle;
		}

		return $this;
	}

	/**
	 * 记录指令信息
	 *
	 * @param  String      $command [description]
	 * @param  String|null $desc    [description]
	 * @param  String|null $alias   [description]
	 * @return [type]               [description]
	 */
	protected function recordMessgae(String $command, String $desc = null, String $alias = null)
	{
		if(is_null($alias)){
			$this->messages[$command] = $desc;
		}
		else{
			$this->messages[$command] = [
				'desc'	=> $desc,
				'alias'	=> $alias
			];
		}

		return $this;
	}

	/**
	 * 解析准备注册的指令的其他信息
	 *
	 * @param  [type] $command [description]
	 * @return [type]          [description]
	 */
	protected function parseOption($option):Array
	{
		$parse = [];
		if(is_array($option)){
			$parse['desc'] = $option['desc'] ?? null;
			$parse['alias'] = $option['alias'] ?? null;
		}
		elseif(is_string($option)){
			$parse['desc'] = $option;
			$parse['alias'] = null;
		}
		else{
			throw new InvalidArgumentException('Command option invalid arguments.');
		}

		return $parse;
	}

	/**
	 * 显示版本信息
	 *
	 * @return [type] [description]
	 */
	public function showVersion()
	{
		$status = $this->output->write("Mon-Console version " . self::VERSION);
        exit(0);
	}

	/**
	 * 显示帮助
	 *
	 * @return [type] [description]
	 */
	public function showHelp()
	{
		$this->output->write("\nWelcome to Mon-Console Application.\n");
		$columns = ['command', 'alias', 'desc'];
		$data = [];
		foreach($this->messages as $command => $option)
		{
			if(is_array($option)){
            	$desc = $option['desc'] ?: 'No description for the command';
            	$alias = '-' . $option['alias'];
            }
            else{
            	$desc = $option ?: 'No description for the command';
            	$alias = '';
            }
			$data[] = [$command, $alias, $desc];
		}

		$this->output->table($data, 'Mon-console Help', ['columns' => $columns]);
        exit(0);
	}

	/**
	 * 获取帮助信息
	 *
	 * @return [type] [description]
	 */
	public function getHelp()
	{
		$commandWidth = 12;
        foreach($this->messages as $command => $option)
        {
            $command = str_pad($command, $commandWidth, ' ');
            if(is_array($option)){
            	$desc = $option['desc'] ?: 'No description for the command';
            	$alias = str_pad($option['alias'] ?: '', $commandWidth, ' ');
            }
            else{
            	$desc = $option ?: 'No description for the command';
            	$alias = str_pad('', $commandWidth, ' ');
            }
            $help .= "  {$command}   {$alias}   {$desc}\n";
        }

        return $help;
	}

	/**
	 * 显示错误信息
	 *
	 * @param  string $error [description]
	 * @return [type]        [description]
	 */
	public function showError($error = '', $ststus = 0)
	{
		if($error){
            $this->output->write(Style::color("<red>[ERROR]</red>: {$error}"));
        }

       	return $this->showHelp();
	}
}