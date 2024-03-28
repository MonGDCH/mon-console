<?php

declare(strict_types=1);

namespace mon\console;

use STDERR;
use mon\console\libs\Show;
use mon\console\libs\Table;
use mon\console\libs\Spinner;

/**
 * 输出操作类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Output
{
    /**
     * 单例实体
     *
     * @var Output
     */
    protected static $instance = null;

    /**
     * 错误输出流
     *
     * @var STDERR
     */
    protected $errorStream = STDERR;

    /**
     * 获取实例
     *
     * @return Output
     */
    public static function instance(): Output
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 开启Buffer
     *
     * @return void
     */
    public function startBuffer()
    {
        Show::startBuffer();
    }

    /**
     * 清空Buffer
     *
     * @return void
     */
    public function clearBuffer()
    {
        Show::clearBuffer();
    }

    /**
     * 关闭Buffer
     *
     * @param  boolean $flush 是否刷新
     * @param  boolean $nl    是否换行
     * @param  boolean $quit  是否退出
     * @param  array   $opts  其他参数
     * @return void
     */
    public function stopBuffer(bool $flush = true, bool $nl = false, bool $quit = false, array $opts = [])
    {
        Show::stopBuffer($flush, $nl, $quit, $opts);
    }

    /**
     * 输出Buffer
     *
     * @param  boolean $nl   是否换行
     * @param  boolean $quit 是否退出
     * @param  array   $opts 其他参数
     * @return void
     */
    public function flush(bool $nl = false, bool $quit = false, array $opts = [])
    {
        $this->stopBuffer(true, $nl, $quit, $opts);
    }

    /**
     * 输出错误信息
     *
     * @param  string  $text 错误信息
     * @param  boolean $nl   是否换行
     * @return integer
     */
    public function error(string $text = '', bool $nl = true): int
    {
        fwrite($this->errorStream, $text . ($nl ? "\n" : null));

        return 0;
    }

    /**
     * 写入
     * 
     * @param  string  $messages 写入内容
     * @param  boolean $nl       是否换行
     * @param  boolean $quit     是否退出
     * @return integer
     */
    public function write(string $messages, bool $nl = true, bool $quit = false): int
    {
        return Show::write($messages, $nl, $quit, true);
    }

    /**
     * 块状文本
     *
     * @param string $messages  内容
     * @param string $type      样式
     * @param boolean $quit     是否退出
     * @return integer
     */
    public function block(string $messages, string $type = 'INFO', bool $quit = false): int
    {
        return Show::block($messages, $type, $quit);
    }

    /**
     * 分割线
     * 
     * @param string $title     标题
     * @param string $char      分割符
     * @param integer $width    宽度
     * @return integer
     */
    public function splitLine(string $title = null, string $char = '-', int $width = 0): int
    {
        return Show::splitLine($title, $char, $width);
    }

    /**
     * 列表
     *
     * @param  array       $data    一维数组
     * @param  string|null  $title  标题
     * @param  boolean $sequence    是否是有序列表
     * @param  array        $opts   额外配置参数
     * @return integer
     */
    public function dataList(array $data, string $title = null, bool $sequence = false, array $opts = []): int
    {
        return Show::dataList($data, $title, $sequence, $opts);
    }

    /**
     * 表格
     *
     * @param  array  $data  二维数组
     * @param  string $title 表标题
     * @param  array  $opts  表列名称
     * @return integer
     */
    public function table(array $data, string $title = 'Data Table', array $columns = []): int
    {
        return Table::create($data, $title, ['columns' => $columns]);
    }

    /**
     * json输出
     *
     * @param array $data       数据
     * @param boolean $echo     是否输出
     * @param integer $flags    json_encode参数
     * @return integer|string
     */
    public function json(array $data, bool $echo = true, $flags = JSON_UNESCAPED_UNICODE)
    {
        $string = json_encode($data, $flags);

        if ($echo) {
            return Show::write($string);
        }

        return $string;
    }

    /**
     * dump打印数据
     *
     * @param mixed $args   数据集
     * @return integer
     */
    public function dump(...$args): int
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return Show::write(preg_replace("/=>\n\s+/", '=> ', trim($string)));
    }

    /**
     * 开始渲染loading
     *
     * @return void
     */
    public function spinBegiin(): void
    {
        Spinner::instance()->begin();
    }

    /**
     * 渲染loading
     *
     * @return void
     */
    public function spin(): void
    {
        Spinner::instance()->spin();
    }

    /**
     * 渲染loading结束
     *
     * @return void
     */
    public function spinEnd(): void
    {
        Spinner::instance()->end();
    }
}
