<?php

namespace Mon\console\libs;

use STDOUT;
use Mon\console\libs\Util;
use Mon\console\libs\Style;
use Mon\console\libs\StrBuffer;

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
     * @param string|array $messages 输出的消息
     * @param boolean $nl True 会添加换行符, False 原样输出，不添加换行符
     * @param int|boolean $quit 如果是int，则设置为退出代码。“True”转换为代码0并退出，“False”将不退出
     * @param bool $flush 刷新流数据
     * @return int
     */
    public static function write($messages, $nl = true, $quit = false, $flush = true)
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
     * @param mixed $messages
     * @param string|null $type
     * @param string $style
     * @param int|boolean $quit If is int, setting it is exit code.
     * @return int
     */
    public static function block($messages, $type = 'INFO', $quit = false)
    {
        $messages = is_array($messages) ? array_values($messages) : array($messages);

        // add type
        if (null !== $type) {
            $messages[0] = sprintf('[%s] %s', strtoupper($type), $messages[0]);
        }

        $text = implode(PHP_EOL, $messages);

        return self::write($text, true, $quit);
    }

    /**
     * 分割线
     * 
     * @param string $title
     * @param string $char
     * @param int $width
     * @return int
     */
    public static function splitLine($title = null, $char = '-', $width = 0)
    {
        if ($width <= 0) {
            list($width,) = Util::getScreenSize();
            $width -= 2;
        }

        if (!$title) {
            return self::write(str_repeat($char, $width));
        }

        $strLen = ceil(($width - Util::strLen($title) - 2) / 2);
        $padStr = $strLen > 0 ? str_repeat($char, $strLen) : '';

        return self::write($padStr . ' ' . ucwords($title) . ' ' . $padStr);
    }

    /**
     * 单个列表
     * ```
     * $title = 'list title';
     * $data = [
     *      'name'  => 'value text',
     *      'name2' => 'value text 2',
     * ];
     * ```
     * @param array $data
     * @param string $title
     * @return int|string
     */
    public static function dataList($data, $title = null, $sequence = false, array $opts = [])
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
            $title = ucwords(trim($title));
            $string .= Util::wrapTag($title, $opts['titleStyle']) . PHP_EOL;
        }

        $string .= Util::spliceKeyValue((array) $data, $opts, $sequence);

        return self::write($string, $opts['lastNewline']);
    }

    /**
     * 是否启用Buffer
     * 
     * @return bool
     */
    public static function isBuffering()
    {
        return self::$buffering;
    }

    /**
     * 开启Buffer
     */
    public static function startBuffer()
    {
        self::$buffering = true;
    }

    /**
     * 清空Buffer
     */
    public static function clearBuffer()
    {
        self::$buffer = null;
    }

    /**
     * 关闭Buffer
     *
     * @see Show::write()
     * @return null|string If flush = False, will return all buffer text.
     */
    public static function stopBuffer($flush = true, $nl = false, $quit = false)
    {
        self::$buffering = false;

        if ($flush && self::$buffer) {
            // flush to stream
            self::write(self::$buffer, $nl, $quit);
            // clear buffer
            self::$buffer = null;
        }

        return self::$buffer;
    }

    /**
     * 关闭Buffer，并输入Buffer内容
     *
     * @see Show::write()
     */
    public static function flushBuffer($nl = false, $quit = false)
    {
        self::stopBuffer(true, $nl, $quit);
    }
}
