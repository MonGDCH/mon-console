<?php
namespace Mon\console;

use STDIN;
use STDOUT;
use Mon\console\libs\Parse;
use Mon\console\libs\Password;

/**
 * 输入操作类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
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
	 * @var [type]
	 */
	protected $data;

	/**
	 * 应用路径
	 *
	 * @var [type]
	 */
	protected $pwd;

	/**
	 * 完整的请求脚本
	 *
	 * @var [type]
	 */
	protected $fullScript;

	/**
	 * 执行的脚本名称
	 *
	 * @var [type]
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
	 * @var [type]
	 */
	protected $command;

	/**
	 * 获取单例
	 *
	 * @see __construct
	 * @return static
	 */
	public static function instance($argv = null)
	{
	    if(is_null(self::$instance)){
	        self::$instance = new self($argv);
	    }
	    return self::$instance;
	}

	/**
	 * 构造方法
	 *
	 * @param [type]  $argv  请求参数
	 */
	protected function __construct($argv = null)
	{
		if(is_null($argv)){
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
    public function read($question = null, $nl = false): string
    {
        if ($question) {
            fwrite(STDOUT, $question . ($nl ? "\n" : ''));
        }

        return trim(fgets(STDIN));
    }

    /**
     * 获取用户输入密码
     *
     * @param  string $tips [description]
     * @return [type]       [description]
     */
    public function password($tips = 'Please Enter Password:')
    {
    	return Password::interaction($tips);
    }

	/**
	 * 获取路径
	 *
	 * @return [type] [description]
	 */
	public function getPwd(): string
	{
		if(!$this->pwd){
			$this->pwd = getcwd();
		}

		return $this->pwd;
	}

	/**
	 * 获取参数
	 *
	 * @return [type] [description]
	 */
	public function getArgs($key = null, $defalue = null)
	{
		if(is_null($key)){
			return $this->args;
		}
		
		return isset($this->args[$key]) ? $this->args[$key] : $defalue;
	}

	/**
	 * 设置参数
	 *
	 * @param array   $val     [description]
	 * @param boolean $replace [description]
	 */
	public function setArgs(array $val, $replace = false)
	{
		$this->args = $replace ? $val : array_merge($this->args, $val);

		return $this;
	}

	/**
	 * 获取短标签
	 *
	 * @return [type] [description]
	 */
	public function getSopt($key = null, $defalue = null)
	{
		if(is_null($key)){
			return $this->sOpts;
		}
		
		return isset($this->sOpts[$key]) ? $this->sOpts[$key] : $defalue;
	}

	/**
	 * 设置短标签
	 *
	 * @param array   $val     [description]
	 * @param boolean $replace [description]
	 */
	public function setSopt(array $val, $replace = false)
	{
		$this->sOpts = $replace ? $val : array_merge($this->sOpts, $val);

		return $this;
	}

	/**
	 * 获取长标签
	 *
	 * @return [type] [description]
	 */
	public function getlopt($key = null, $defalue = null)
	{
		if(is_null($key)){
			return $this->lOpts;
		}
		
		return isset($this->lOpts[$key]) ? $this->lOpts[$key] : $defalue;
	}

	/**
	 * 设置长标签
	 *
	 * @param array   $val     [description]
	 * @param boolean $replace [description]
	 */
	public function setlopt(array $val, $replace = false)
	{
		$this->lOpts = $replace ? $val : array_merge($this->lOpts, $val);
	}

	/**
	 * 获取指令
	 *
	 * @return [type] [description]
	 */
	public function getCommand($argv = null)
	{
		return $this->command;
	}

	/**
	 * 设置指令
	 *
	 * @param [type] $command [description]
	 */
	public function setCommand($command)
	{
		$this->command = $command;

		return $this;
	}
}