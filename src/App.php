<?php

namespace mon\console;

use mon\console\Console;

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
     * 获取实例
     *
     * @param  [type] $option [description]
     * @return [type]         [description]
     */
    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 初始化
     *
     * @param array $commands [description]
     */
    protected function __construct()
    {
        $this->console = new Console();
    }

    /**
     * 注册指令
     *
     * @param String $command [description]
     */
    public function add($command, $handle, $option = [])
    {
        $this->commands[] = $command;
        $this->console->addCommand($command, $handle, $option);
        return $this;
    }

    /**
     * 执行指令
     *
     * @return [type] [description]
     */
    public function run()
    {
        return $this->console->run();
    }

    /**
     * 获取自定义指令列表
     *
     * @return [type] [description]
     */
    public function getCommand()
    {
        return $this->commands;
    }
}
