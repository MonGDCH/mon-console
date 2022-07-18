# mon-console

#### 项目介绍
PHP 命令行控制台工具，内置read及password获取等命令行交互工具，及table、line、list等控制台渲染工具

#### 版本说明

> 1.0.5

- 修正发布缺失的文件

> 1.0.4

- 优化注解


> 1.0.3

- 优化错误提示
- 调整命名空间


> 1.0.2

- 优化代码，优化在win环境下判断sh及bash脚本提示错误信息
- 条件PHP版本要求，降为PHP5.6以上


> 1.0.1

- 优化代码，完全使用STDOUT作为输出流
- 增加output对象list方法的有序列表支持
- 修复table渲染的BUG


> 1.0.0

- 发布第一个版本


#### 安装教程

```
composer require mongdch/mon-console
```
或者
```
git clone https://github.com/MonGDCH/mon-console
```
当然，使用git的话建议还是使用release的，当然给我提交issues，我也是非常欢迎的^_^。

#### 使用说明

```php
<?php

require '../vendor/autoload.php';

/**
 * 指令回调对象
 */
class Test extends \mon\console\Command
{
    /**
     * 执行指令
     *
     * @param \mon\console\Input $input		输入对象实例
	 * @param \mon\console\Output $output	输出对象实例
     * @return void
     */
    public function execute(Input $input, Output \mon\console\$output)
    {
        $name = $input->read('What\'s your name?  ');
        $password = $input->password();
        
        return $output->write('Hello '.$name.', Your password is '.$password);
    }
}


// 获取应用实例
$app = \mon\console\App::instance();

// 注册简单指令
$app->add('test', Test::class, 'This is Test Command!');

// 注册包含别名的指令
$app->add('demo', Test::class, ['alias' => 'd', 'desc' => 'This is Demo Command!']);

// 使用匿名方法作为指令回调
$app->add('hello', function($input, $output){
    return $output->write('This is a Closure Command!');
});

// 运行指令
$app->run();
```