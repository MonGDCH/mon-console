<?php
namespace Mon\FCli;

use Mon\FCli\Console;

/**
 * 应用驱动
 *
 * @author Mon <98558837@qq.om>
 * @version v1.0.0
 */
class App
{
    /**
     * 对象单例
     *
     * @var [type]
     */
    protected static $instance;

    /**
     * 控制台实例
     *
     * @var [type]
     */
    protected $console;

    /**
     * 指令列表
     *
     * @var array
     */
    protected $commands = [];

    /**
     * 获取实例
     *
     * @param  [type] $option [description]
     * @return [type]         [description]
     */
    public static function instance($option = [])
    {
        if(is_null(static::$instance)){
            static::$instance = new static($option);
        }

        return static::$instance;
    }

    /**
     * 初始化
     *
     * @param array $commands [description]
     */
    protected function __construct($commands = [])
    {
        $this->setCommand($commands);
        $this->console = new Console();
        $this->init();
    }

    /**
     * 初始化指令库
     *
     * @return [type] [description]
     */
    public function init()
    {
        // 加载指令
        foreach($this->commands as $command => $callback)
        {
            if(is_string($command) && class_exists($command) && is_subclass_of($command, "\\Mon\\FCli\\Command")){
                // $this->addCommand
            }
        }
    }

    /**
     * 注册指令
     * 
     * @param [type]         $command   指令名称
     * @param string|Closure $handler   指令回调
     * @param string         $desc      指令描述
     */
    public function add($command, $handler, $desc = '')
    {
        $this->console->addCommand($command, $handler, $desc);
        return $this;
    }

    /**
     * 获取指令
     *
     * @return [type] [description]
     */
    public function getCommand()
    {
        return $this->commands;
    }

    /**
     * 设置指令
     *
     * @param [type] $command  [description]
     * @param [type] $callback [description]
     */
    public function setCommand($command, $callback = null)
    {
        if(is_array($command)){
            $this->commands = array_merge($this->commands, $commands);
        }
        elseif(is_string($command)){
            if(!is_null($callback)){

            }
        }
    }
}