<?php

namespace Mon\console;

use STDERR;
use Mon\console\libs\Show;
use Mon\console\libs\Table;

/**
 * 输出操作类
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0.0
 */
class Output
{
    /**
     * 单例实体
     *
     * @var null
     */
    protected static $instance = null;

    /**
     * 错误输出流
     *
     * Property errorStream.
     */
    protected $errorStream = STDERR;

    /**
     * 获取单例
     *
     * @see __construct
     * @return static
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造方法
     *
     * @param [type] $stream [description]
     */
    public function __construct()
    { }

    /**
     * 开启Buffer
     *
     * @return [type] [description]
     */
    public function startBuffer()
    {
        Show::startBuffer();
    }

    /**
     * 清空Buffer
     *
     * @return [type] [description]
     */
    public function clearBuffer()
    {
        Show::clearBuffer();
    }

    /**
     * 关闭Buffer
     *
     * @param  boolean $flush [description]
     * @param  boolean      $nl    [description]
     * @param  boolean      $quit  [description]
     * @param  array        $opts  [description]
     * @return [type]              [description]
     */
    public function stopBuffer($flush = true, $nl = false, $quit = false, array $opts = [])
    {
        Show::stopBuffer($flush, $nl, $quit, $opts);
    }

    /**
     * 输出Buffer
     *
     * @param  boolean $nl   [description]
     * @param  boolean      $quit [description]
     * @param  array        $opts [description]
     * @return [type]             [description]
     */
    public function flush($nl = false, $quit = false, array $opts = [])
    {
        $this->stopBuffer(true, $nl, $quit, $opts);
    }

    /**
     * 输出错误信息
     *
     * @param  string  $text [description]
     * @param  boolean $nl   [description]
     * @return [type]        [description]
     */
    public function error($text = '', $nl = true)
    {
        fwrite($this->errorStream, $text . ($nl ? "\n" : null));

        return 0;
    }

    /**
     * 写入
     * 
     * @param  [type]  $messages [description]
     * @param  boolean $nl       [description]
     * @param  boolean $quit     [description]
     * @return [type]            [description]
     */
    public function write($messages, $nl = true, $quit = false)
    {
        return Show::write($messages, $nl, $quit, true);
    }

    /**
     * 块状文本
     *
     * @see Show::block()
     */
    public function block($messages, $type = 'INFO', $quit = false)
    {
        return Show::block($messages, $type, $quit);
    }

    /**
     * 分割线
     * 
     * @param string $title
     * @param string $char
     * @param int $width
     * @return int
     */
    public function splitLine($title = null, $char = '-', $width = 0)
    {
        return Show::splitLine($title, $char, $width);
    }

    /**
     * 列表
     *
     * @param  [type]       $data     一维数组
     * @param  string|null  $title    标题
     * @param  boolean $sequence 是否是有序列表
     * @param  array        $opts     额外配置参数
     * @return [type]                 [description]
     */
    public function dataList($data, $title = null, $sequence = false, array $opts = [])
    {
        return Show::dataList($data, $title, $sequence, $opts);
    }

    /**
     * 表格
     *
     * @param  array  $data  二维数组
     * @param  string $title 表标题
     * @param  array  $opts  表列名称
     * @return [type]        [description]
     */
    public function table(array $data, $title = 'Data Table', array $columns = [])
    {
        return Table::create($data, $title, ['columns' => $columns]);
    }

    /**
     * json输出
     */
    public function json($data, $echo = true, $flags = JSON_UNESCAPED_UNICODE)
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
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function dump(...$args)
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return Show::write(preg_replace("/=>\n\s+/", '=> ', trim($string)));
    }
}
