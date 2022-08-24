<?php

declare(strict_types=1);

namespace mon\console\libs;

/**
 * 系统辅助库
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Util
{
    /**
     * 判断bash脚本是否可用
     *
     * @return boolean
     */
    public static function bashIsAvailable(): bool
    {
        $checkCmd = "bash -c 'echo OK'";
        return self::execute($checkCmd) === 'OK';
    }

    /**
     * 判断sh脚本是否可用
     *
     * @return boolean
     */
    public static function shIsAvailable(): bool
    {
        $checkCmd = "sh -c 'echo OK'";

        return self::execute($checkCmd) === 'OK';
    }

    /**
     * 判断是否为windows环境
     *
     * @return boolean
     */
    public static function isWindows(): bool
    {
        return stripos(PHP_OS, 'WIN') !== false;
    }

    /**
     * 获取屏幕大小
     *
     * ```php
     * list($width, $height) = Self::getScreenSize();
     * ```
     * 
     * @param boolean $refresh  是否刷新
     * @return array|false
     */
    public static function getScreenSize(bool $refresh = false)
    {
        static $size;
        if ($size !== null && !$refresh) {
            return $size;
        }

        if (self::isWindows()) {
            $output = [];
            exec('mode con', $output);

            if (isset($output[1]) && strpos($output[1], 'CON') !== false) {
                return ($size = [
                    (int) preg_replace('~\D~', '', $output[3]),
                    (int) preg_replace('~\D~', '', $output[4])
                ]);
            }
        }

        if (self::shIsAvailable()) {
            $stty = [];
            if (exec('stty -a 2>&1', $stty) && preg_match('/rows\s+(\d+);\s*columns\s+(\d+);/mi', implode(' ', $stty), $matches)) {
                return ($size = [$matches[2], $matches[1]]);
            }

            if (($width = (int) exec('tput cols 2>&1')) > 0 && ($height = (int) exec('tput lines 2>&1')) > 0) {
                return ($size = [$width, $height]);
            }

            if (($width = (int) getenv('COLUMNS')) > 0 && ($height = (int) getenv('LINES')) > 0) {
                return ($size = [$width, $height]);
            }
        }

        return ($size = false);
    }

    /**
     * 标签化字符串
     *
     * @param string $string    内容
     * @param string $tag       标签
     * @return string
     */
    public static function wrapTag(string $string, string $tag): string
    {
        if (!$string) {
            return '';
        }

        if (!$tag) {
            return $string;
        }

        return "<$tag>$string</$tag>";
    }

    /**
     * 获取字符串长度
     *
     * @param string $string    字符串
     * @return integer
     */
    public static function strLen(string $string): int
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }

    /**
     * 获取键值最大宽度
     *
     * @param  array $data  数据数组
     * [
     *     'key1'      => 'value1',
     *     'key2-test' => 'value2',
     * ]
     * @param boolean $expectInt  转数字
     * @return integer
     */
    public static function getKeyMaxWidth(array $data, bool $expectInt = false): int
    {
        $keyMaxWidth = 0;

        foreach ($data as $key => $value) {
            if (!$expectInt || !is_numeric($key)) {
                $width = mb_strlen((string)$key, 'UTF-8');
                $keyMaxWidth = $width > $keyMaxWidth ? $width : $keyMaxWidth;
            }
        }

        return $keyMaxWidth;
    }

    /**
     * 拼接数组
     *
     * @param  array $data  数据数组
     * e.g [
     *     'system'  => 'Linux',
     *     'version'  => '4.4.5',
     * ]
     * @param  array $opts  样式参数
     * @param  boolean $sequence    开启间隔
     * @return string
     */
    public static function spliceKeyValue(array $data, array $opts = [], bool $sequence = false): string
    {
        $text = '';
        $opts = array_merge([
            'leftChar' => '',
            'sepChar' => ' ',
            'keyStyle' => '',
            'valStyle' => '',
            'keyMinWidth' => 8,
            'keyMaxWidth' => null,
            'ucFirst' => true,
        ], $opts);

        if (!is_numeric($opts['keyMaxWidth'])) {
            $opts['keyMaxWidth'] = self::getKeyMaxWidth($data);
        }

        if ((int) $opts['keyMinWidth'] > $opts['keyMaxWidth']) {
            $opts['keyMaxWidth'] = $opts['keyMinWidth'];
        }

        $keyStyle = trim($opts['keyStyle']);

        $i = 1;
        foreach ($data as $key => $value) {
            $hasKey = !is_int($key);

            if ($sequence == true) {
                $text .= ' ' . $i . '. ';
            } else {
                $text .= $opts['leftChar'];
            }

            if ($hasKey && $opts['keyMaxWidth']) {
                $key = str_pad($key, $opts['keyMaxWidth'], ' ');
                $text .= self::wrapTag($key, $keyStyle) . $opts['sepChar'];
            }

            if (is_array($value)) {
                $temp = '';

                foreach ($value as $k => $val) {
                    if (is_bool($val)) {
                        $val = $val ? '(true)' : '(false)';
                    } else {
                        $val = is_scalar($val) ? (string) $val : gettype($val);
                    }

                    $temp .= (!is_numeric($k) ? "$k: " : '') . "$val, ";
                }

                $value = rtrim($temp, ' ,');
            } else {
                if (is_bool($value)) {
                    $value = $value ? '(true)' : '(false)';
                } else {
                    $value = (string) $value;
                }
            }

            $value = $hasKey && $opts['ucFirst'] ? ucfirst($value) : $value;
            $text .= self::wrapTag($value, $opts['valStyle']) . "\n";
            $i++;
        }

        return $text;
    }

    /**
     * 执行命令行指令
     *
     * @param string $command   指令
     * @param string $cwd       指令路径
     * @return string
     */
    public static function execute(string $command, string $cwd = null): string
    {
        $return_var = 1;

        if ($cwd) {
            // 切换目录
            chdir($cwd);
        }

        if (function_exists('system')) {
            ob_start();
            system($command, $return_var);
            $output = ob_get_contents();
            ob_end_clean();
        } elseif (function_exists('passthru')) {
            ob_start();
            passthru($command, $return_var);
            $output = ob_get_contents();
            ob_end_clean();
        } else {
            if (function_exists('exec')) {
                exec($command, $output, $return_var);
                $output = implode("\n", $output);
            } else {
                if (function_exists('shell_exec')) {
                    $output = shell_exec($command);
                } else {
                    $output = 'Command execution not possible on this system';
                    $return_var = 0;
                }
            }
        }
        return trim($output);
    }
}
