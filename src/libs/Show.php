<?php

declare(strict_types=1);

namespace mon\console\libs;

use mon\console\libs\Util;
use mon\console\libs\Style;

/**
 * 展示内容
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0   2022-07-18
 */
class Show
{
    /**
     * 数据buffer块
     *
     * @var string
     */
    private static $buffer;

    /**
     * 启用数据buffer
     *
     * @var boolean
     */
    private static $buffering = false;

    /**
     * 消息写入标准输出流
     *
     * @param string|array $messages    输出的消息
     * @param boolean $nl               会添加换行符, false 原样输出，不添加换行符
     * @param integer|boolean $quit     如果是int，则设置为退出代码。“true”转换为代码0并退出，“false”将不退出
     * @param boolean $flush            刷新流数据
     * @return integer
     */
    public static function write($messages, bool $nl = true, $quit = false, bool $flush = true): int
    {
        if (is_array($messages)) {
            $messages = implode($nl ? PHP_EOL : '', $messages);
        }

        $messages = Style::renderColor((string) $messages);

        if (self::isBuffering()) {
            self::$buffer .= $messages . ($nl ? PHP_EOL : '');

            if (!$quit) {
                return 0;
            }

            $messages = self::$buffer;
            self::clearBuffer();
        } else {
            $messages .= $nl ? PHP_EOL : '';
        }

        fwrite(STDOUT, $messages);
        if ($flush) {
            fflush(STDOUT);
        }

        if ($quit !== false) {
            $code = true === $quit ? 0 : (int) $quit;
            exit($code);
        }

        return 0;
    }

    /**
     * 块状文本
     *
     * @param string|array $messages    输出的消息
     * @param string|null $type         消息类型
     * @param integer|boolean $quit     如果是int，则设置为退出代码。“true”转换为代码0并退出，“false”将不退出
     * @return integer
     */
    public static function block($messages, string $type = 'INFO', $quit = false): int
    {
        $messages = is_array($messages) ? array_values($messages) : array($messages);

        // 添加消息类型
        if (null !== $type) {
            $messages[0] = sprintf('[%s] %s', strtoupper($type), $messages[0]);
        }

        $text = implode(PHP_EOL, $messages);

        return self::write($text, true, $quit);
    }

    /**
     * 分割线
     * 
     * @param string $title 标题
     * @param string $char  分割符
     * @param int $width    宽度，默认整屏
     * @return integer
     */
    public static function splitLine(string $title = null, string $char = '-', int $width = 0): int
    {
        if ($width <= 0) {
            list($width,) = Util::getScreenSize();
            $width -= 2;
        }

        if (!$title) {
            return self::write(str_repeat($char, $width));
        }

        $strLen = ceil(($width - Util::strLen($title) - 2) / 2);
        $padStr = $strLen > 0 ? str_repeat($char, (int)$strLen) : '';

        return self::write($padStr . ' ' . ucwords($title) . ' ' . $padStr);
    }

    /**
     * 数据列表
     * ```
     * $title = 'list title';
     * $data = [
     *      'name'  => 'value text',
     *      'name2' => 'value text 2',
     * ];
     * ```
     * @param array $data   数据源
     * @param string $title 标题
     * @param boolean $sequence  是否是有序列表
     * @param array $opts  样式参数
     * @return integer
     */
    public static function dataList(array $data, string $title = null, bool $sequence = false, array $opts = []): int
    {
        $string = '';
        $opts = array_merge([
            'leftChar' => ' - ',
            'keyStyle' => 'info',
            'keyMinWidth' => 8,
            'titleStyle' => 'comment',
            'lastNewline' => true,
        ], $opts);

        if ($title) {
            $title = trim($title);
            $string .= Util::wrapTag($title, $opts['titleStyle']) . PHP_EOL;
        }

        $string .= Util::spliceKeyValue((array) $data, $opts, $sequence);

        return self::write($string, $opts['lastNewline']);
    }

    /**
     * 是否启用Buffer
     * 
     * @return boolean
     */
    public static function isBuffering()
    {
        return self::$buffering;
    }

    /**
     * 开启Buffer
     * 
     * @return void
     */
    public static function startBuffer()
    {
        self::$buffering = true;
    }

    /**
     * 清空Buffer
     * 
     * @return void
     */
    public static function clearBuffer()
    {
        self::$buffer = null;
    }

    /**
     * 关闭Buffer
     *
     * @param boolean $flush            刷新流数据     
     * @param boolean $nl               会添加换行符, false 原样输出，不添加换行符
     * @param integer|boolean $quit     如果是int，则设置为退出代码。“true”转换为代码0并退出，“false”将不退出
     * @return null|string
     */
    public static function stopBuffer(bool $flush = true, bool $nl = false, $quit = false)
    {
        self::$buffering = false;

        if ($flush && self::$buffer) {
            self::write(self::$buffer, $nl, $quit);
            self::$buffer = null;
        }

        return self::$buffer;
    }

    /**
     * 关闭Buffer，并输入Buffer内容
     *
     * @param boolean $nl               会添加换行符, false 原样输出，不添加换行符
     * @param integer|boolean $quit     如果是int，则设置为退出代码。“true”转换为代码0并退出，“false”将不退出
     * @return null|string
     */
    public static function flushBuffer($nl = false, $quit = false)
    {
        return self::stopBuffer(true, $nl, $quit);
    }
}
