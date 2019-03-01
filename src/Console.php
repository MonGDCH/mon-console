<?php
namespace Mon\FCli;

use Exception;
use InvalidArgumentException;
use Mon\FCli\CliException;
use Mon\FCli\input\Input;
use Mon\FCli\util\Style;

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
		'help'		=> 'Display help for a command',
		'version'	=> 'Display this console version'
	];

	/**
	 * 初始化
	 *
	 * @param Input $input [description]
	 */
	public function __construct(Input $input = null)
	{
		$this->input = $input ?: Input::instance();
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
		elseif($this->command == 'help'){
			return $this->showHelp();
		}
		elseif($this->command == 'version'){
			return $this->showVersion();
		}

		// 执行指令
		$status = 0;
		try{
			if(isset($this->commands[$this->command])){
				$status = $this->runHandler($this->command, $this->commands[$this->command]);
			}
			else{
                return $this->showError("The command [{$this->command}] not exists!");
            }
		}catch(Exception $e){
			$status = $this->handlerException($e);
			$error = printf("Exception(%d): %s\nFile: %s(Line %d)\nTrace:\n%s\n", $code, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

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
	public function runHandler($command, $handler)
	{
		if(method_exists($handler, 'execute')){
			$handler->setConsole($this);
			return $handler->execute();
		}

		throw new CliException('The execute method is not found in the command: '.$command);
	}

	/**
	 * 处理异常
	 *
	 * @param  [type] $e [description]
	 * @return [type]    [description]
	 */
	public function handlerException($e)
	{
		$code = $e->getCode() !== 0 ? $e->getCode() : 0;
        return $code;
	}

	/**
	 * 注册指令
	 * 
	 * @param string 		 $command 	指令对象名称
	 * @param method 		 $handler 	指令回调
	 * @param string 		 $desc    	指令描述
	 */
	public function addCommand($command)
	{
		if( is_string($command) && class_exists($command) && 
			is_subclass_of($command, "\\Mon\\FCli\\Command"))
		{
			$handler = new $command();
			$name = $handler->getName();
			$desc = $handler->getDesc();

			// 判断指令是否已存在
			if(isset($this->commands[$name]) || isset($this->commands[$name])){
	        	throw new InvalidArgumentException('Command Exists');
	        }

	        $this->commands[$name] = $handler;
        	$this->messages[$name] = trim($desc);
		}
		else{
			// 参数错误
			throw new InvalidArgumentException('Invalid arguments');
		}
	}

	/**
	 * 获取input实例
	 *
	 * @return [type] [description]
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * 显示版本信息
	 *
	 * @return [type] [description]
	 */
	public function showVersion()
	{
		echo "Mon-Console version " . self::VERSION . PHP_EOL;
        exit(0);
	}

	/**
	 * 显示帮助
	 *
	 * @return [type] [description]
	 */
	public function showHelp()
	{
		$help = $this->getHelp();
        echo Style::color($help) . PHP_EOL;
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
        $help = "Welcome to Mon-Console Application.\n\n<comment>Available Commands:</comment>\n";
        foreach($this->messages as $command => $desc)
        {
            $command = str_pad($command, $commandWidth, ' ');
            $desc = $desc ?: 'No description for the command';
            $help .= "  {$command}   {$desc}\n";
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
            echo Style::color("<red>[ERROR]</red>: {$error}\n\n");
        }

        $help = $this->getHelp();
        echo Style::color($help) . PHP_EOL;
        exit($ststus);
	}
}