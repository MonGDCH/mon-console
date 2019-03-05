<?php
namespace Mon\console;

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
    protected $errorStream = \STDERR;

    /**
     * 获取单例
     *
     * @see __construct
     * @return static
     */
    public static function instance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造方法
     *
     * @param [type] $stream [description]
     */
    public function __construct(){}

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
     * @param  bool|boolean $flush [description]
     * @param  boolean      $nl    [description]
     * @param  boolean      $quit  [description]
     * @param  array        $opts  [description]
     * @return [type]              [description]
     */
    public function stopBuffer(bool $flush = true, $nl = false, $quit = false, array $opts = [])
    {
        Show::stopBuffer($flush, $nl, $quit, $opts);
    }

    /**
     * 输出Buffer
     *
     * @param  bool|boolean $nl   [description]
     * @param  boolean      $quit [description]
     * @param  array        $opts [description]
     * @return [type]             [description]
     */
    public function flush(bool $nl = false, $quit = false, array $opts = [])
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
    public function error($text = '', $nl = true): self
    {
        fwrite($this->errorStream, $text . ($nl ? "\n" : null));

        return $this;
    }

    /**
     * 写入
     * 
     * @param  [type]  $messages [description]
     * @param  boolean $nl       [description]
     * @param  boolean $quit     [description]
     * @return [type]            [description]
     */
    public function write($messages, $nl = true, $quit = false): int
    {
        return Show::write($messages, $nl, $quit, true);
    }

    /**
     * 块状文本
     *
     * @see Show::block()
     */
    public function block($messages, $type = 'INFO', $quit = false): int
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
    public function splitLine(string $title = null, string $char = '-', int $width = 0): int
    {
        return Show::splitLine($title, $char, $width);
    }

    /**
     * 列表
     *
     * @param  [type]      $data  [description]
     * @param  string|null $title [description]
     * @param  array       $opts  [description]
     * @return [type]             [description]
     */
    public function list($data, string $title = null, array $opts = []): int
    {
        return Show::list($data, $title, $opts);
    }

    /**
     * 表格
     *
     * @return [type] [description]
     */
    public function table(array $data, string $title = 'Data Table', array $opts = []): int
    {
        return Table::create($data, $title, $opts);
    }

    /**
     * json输出
     */
    public function json($data, $echo = true, int $flags = JSON_UNESCAPED_UNICODE)
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
    public function dump(...$args): int
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return Show::write(preg_replace("/=>\n\s+/", '=> ', trim($string)));
    }
}