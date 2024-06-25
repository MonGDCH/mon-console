<?php

declare(strict_types=1);

namespace mon\console;

use mon\console\Console;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * 应用驱动
 *
 * @author Mon <98558837@qq.om>
 * @version 1.1.4
 */
class App
{
    /**
     * 版本号
     * 
     * @var string
     */
    const VERSION = '1.1.4';

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
    public static function instance(): App
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->console = new Console();
    }

    /**
     * 获取版本号
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * 加载目录下所有类文件，生成指令
     *
     * @param string $path  目录路径
     * @param string $namspace  基础命名空间，结合文件名赋予命名空间
     * @return App
     */
    public function load(string $path, string $namspace = ''): App
    {
        // 递归获取文件
        $dir_iterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            // 过滤非PHP文件
            if ($file->isDir() || $file->getExtension() != 'php') {
                continue;
            }

            // 获取对象名称
            $dirname = dirname(str_replace($path, '', $file->getPathname()));
            $beforName = str_replace(DIRECTORY_SEPARATOR, '\\', $dirname);
            $beforNamespace = $beforName == '\\' ? '' : $beforName;
            $className = $namspace . $beforNamespace . '\\' . $file->getBasename('.php');
            if (!is_subclass_of($className, '\\mon\\console\\interfaces\\Command')) {
                continue;
            }
            // 判断是否存在定义指令名方法
            if (method_exists($className, 'getCommandName')) {
                $command = $className::getCommandName();
                if (!$command) {
                    continue;
                }
                // 获取指令别名及指令描述
                $alias = $desc = null;
                $group = 'available';
                if (method_exists($className, 'getCommandAliasName')) {
                    $alias = $className::getCommandAliasName();
                }
                if (method_exists($className, 'getCommandDesc')) {
                    $desc = $className::getCommandDesc();
                }
                if (method_exists($className, 'getCommandGroup')) {
                    $group = $className::getCommandGroup();
                }
                // 注册指令
                $this->add($command, $className, ['alias' => $alias, 'desc' => $desc, 'group' => $group]);
            }
        }

        return $this;
    }

    /**
     * 注册指令
     *
     * @param string $command       指令名称
     * @param mixed $handle         指令回调
     * @param array|string $option  额外参数
     * @return App
     */
    public function add(string $command, $handle, $option = []): App
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
     * 设置控制台标题
     *
     * @param string $title
     * @return App
     */
    public function setTitle(string $title): App
    {
        $this->console->setTitle($title);
        return $this;
    }

    /**
     * 设置默认是否显示help指令
     *
     * @param boolean $show
     * @return App
     */
    public function setShowHelpCommand(bool $show): App
    {
        $this->console->setShowHelpCommand($show);
        return $this;
    }

    /**
     * 获取自定义指令列表
     *
     * @return array
     */
    public function getCommand(): array
    {
        return $this->commands;
    }
}
