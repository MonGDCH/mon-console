<?php

namespace mon\console\libs;

use STDOUT;

/**
 * CLI染色库
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Style
{
    const NORMAL = 0;
    // 前缀颜色
    const FG_BLACK = 30;
    const FG_RED = 31;
    const FG_GREEN = 32;
    const FG_BROWN = 33;
    const FG_BLUE = 34;
    const FG_CYAN = 36;
    const FG_WHITE = 37;
    const FG_DEFAULT = 39;
    // 扩展的前缀颜色
    const FG_DARK_GRAY = 90;
    const FG_LIGHT_RED = 91;
    const FG_LIGHT_GREEN = 92;
    const FG_LIGHT_YELLOW = 93;
    const FG_LIGHT_BLUE = 94;
    const FG_LIGHT_MAGENTA = 95;
    const FG_LIGHT_CYAN = 96;
    const FG_WHITE_EXTRA = 97;
    // 背景颜色
    const BG_BLACK = 40;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_BROWN = 43;
    const BG_BLUE = 44;
    const BG_CYAN = 46;
    const BG_WHITE = 47;
    const BG_DEFAULT = 49;
    // 扩展的背景颜色
    const BG_DARK_GRAY = 100;
    const BG_LIGHT_RED = 101;
    const BG_LIGHT_GREEN = 102;
    const BG_LIGHT_YELLOW = 103;
    const BG_LIGHT_BLUE = 104;
    const BG_LIGHT_MAGENTA = 105;
    const BG_LIGHT_CYAN = 106;
    const BG_WHITE_EXTRA = 107;
    // 加粗
    const BOLD = 1;
    // 模糊(不是所有的终端仿真器都支持)
    const FUZZY = 2;
    // 斜体(不是所有的终端仿真器都支持)
    const ITALIC = 3;
    // 下划线
    const UNDERSCORE = 4;
    // 闪烁
    const BLINK = 5;
    // 颠倒的 交换背景色与前景色
    const REVERSE = 7;
    // 隐匿的
    const CONCEALED = 8;

    /**
     * 正则规则
     */
    const COLOR_TAG = '/<([a-z=;]+)>(.*?)<\\/\\1>/s';

    /**
     * 样式
     */
    const STYLES = [
        'yellow' => '1;33',
        'magenta' => '1;35',
        'white' => '1;37',
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'brown' => '0;33',
        'blue' => '0;34',
        'cyan' => '0;36',
        'light_red' => '1;31',
        'light_blue' => '1;34',
        'light_gray' => '37',
        'light_green' => '1;32',
        'light_cyan' => '1;36',
        'dark_gray' => '90',
        'light_red_ex' => '91',
        'light_green_ex' => '92',
        'light_yellow' => '93',
        'light_blue_ex' => '94',
        'light_magenta' => '95',
        'light_cyan_ex' => '96',
        'white_ex' => '97',
        'bold' => '1',
        'underscore' => '4',
        'reverse' => '7',
        'suc' => '1;32',
        'success' => '1;32',
        'info' => '0;32',
        'comment' => '0;33',
        'warning' => '0;30;43',
        'danger' => '0;31',
        'error' => '30;41',
    ];

    /**
     * 文本染色
     * 
     * @param string $text
     * @param string|int|array $style
     * @return string
     */
    public static function color($text, $style = null)
    {
        if (!$text) {
            return $text;
        }
        if (!self::supportColor()) {
            return self::clearColor($text);
        }
        if (is_string($style)) {
            $color = array_key_exists($style, self::STYLES) ? self::STYLES[$style] : '0';
        } elseif (is_int($style)) {
            $color = $style;
        } elseif (is_array($style)) {
            $color = implode(';', $style);
        } elseif (strpos($text, '<') !== false) {
            return self::renderColor($text);
        } else {
            return $text;
        }

        return "\33[{$color}m{$text}\33[0m";
    }

    /**
     * 渲染颜色标签到颜色样式
     *
     * @param string $text
     * @return mixed|string
     */
    public static function renderColor($text)
    {
        if (!$text || false === strpos($text, '<')) {
            return $text;
        }
        if (!self::supportColor()) {
            return static::clearColor($text);
        }
        if (!preg_match_all(self::COLOR_TAG, $text, $matches)) {
            return $text;
        }
        foreach ((array) $matches[0] as $i => $m) {
            if ($style = array_key_exists($matches[1][$i], self::STYLES) ? self::STYLES[$matches[1][$i]] : null) {
                $tag = $matches[1][$i];
                $match = $matches[2][$i];
                $replace = sprintf("\33[%sm%s\33[0m", $style, $match);
                $text = str_replace("<{$tag}>{$match}</{$tag}>", $replace, $text);
            }
        }

        return $text;
    }

    /**
     * 清除染色
     *
     * @param string $text
     * @return string
     */
    public static function clearColor($text)
    {
        return preg_replace('/\\033\\[(?:\\d;?)+m/', '', strip_tags($text));
    }

    /**
     * 如果STDOUT支持着色，返回true
     *
     * @return boolean
     */
    public static function supportColor()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return '10.0.10586' === PHP_WINDOWS_VERSION_MAJOR . '.' . PHP_WINDOWS_VERSION_MINOR . '.' . PHP_WINDOWS_VERSION_BUILD || false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI') || 'xterm' === getenv('TERM');
        }
        if (!defined('STDOUT')) {
            return false;
        }

        return self::isInteractive(STDOUT);
    }

    /**
     * 是否是交互式终端。
     *
     * @return boolean
     */
    public static function isInteractive($fileDescriptor)
    {
        return function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }
}
