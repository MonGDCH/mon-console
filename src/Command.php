<?php
 namespace Mon\FCli;

use Mon\FCli\Console;

/**
 * 指令基类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
abstract class Command
{
    /**
     * 指令名称
     *
     * @var string
     */
    abstract protected $name = 'Test';

    /**
     * 指令描述
     *
     * @var string
     */
    abstract protected $desc = 'This is User Command';

    /**
     * 控制台实例
     *
     * @var [type]
     */
    protected $console;


 	/**
 	 * 执行指令的接口方法
 	 *
 	 * @return [type] [description]
 	 */
 	abstract public function execute();

    /**
     * 设置控制台
     *
     * @param [type] $console [description]
     */
    final public function setConsole(Console $console)
    {
        $this->console = $console;
    }

    /**
     * 获取指令描述
     *
     * @return [type] [description]
     */
    final public function getDesc()
    {
        return $this->desc;
    }

    /**
     * 获取指令名称
     *
     * @return [type] [description]
     */
    final public function getName()
    {
        return $this->name;
    }
}