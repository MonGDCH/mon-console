<?php

namespace mon\console;

use mon\console\Console;

/**
 * 应用驱动
 *
 * @author Mon <98558837@qq.om>
 * @version 1.0.0
 */
class App
{
    /**
     * 对象单例
     *
     * @var App
     */
    protected static $instance;

    /**
     * 控制台实例
     *
     * @var Console
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
     * @return App
     */
    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 构造方法
     */
    protected function __construct()
    {
        $this->console = new Console();
    }

    /**
     * 注册指令
     *
     * @param string $command   指令名称
     * @param mixed $handle     指令回调
     * @param array $option     额外参数
     * @return App
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
     * @return void
     */
    public function run()
    {
        return $this->console->run();
    }

    /**
     * 获取自定义指令列表
     *
     * @return array
     */
    public function getCommand()
    {
        return $this->commands;
    }
}
