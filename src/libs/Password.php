<?php

namespace mon\console\libs;

use mon\console\libs\Util;
use mon\console\exception\ConsoleException;

/**
 * 输入密码，隐藏字符
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Password
{
    /**
     * 发起密码输入
     *
     * @param string $tips  提示信息
     * @return string   输入的密码
     */
    public static function interaction($tips = 'Enter Password:')
    {
        // windows通过vb脚本获取输入的密码
        if (Util::isWindows()) {
            $prompt = '';
            $vbScript = self::getTempDir() . '/hidden_prompt_input.vbs';
            file_put_contents($vbScript, 'wscript.echo(InputBox("' . $prompt . '", "", "Enter your password"))');
            $command = 'cscript //nologo ' . escapeshellarg($vbScript);
            $password = rtrim(shell_exec($command));
            unlink($vbScript);

            return $password;
        }

        // liunx通过bash脚本获取输入的密码
        if (Util::bashIsAvailable()) {
            $command = sprintf('bash -c "read -p \'%s\' -s user_input && echo $user_input"', $tips);
            $password = Util::execute($command, false);
            echo "\n";
            return $password;
        }

        throw new ConsoleException('Can not invoke bash shell env');
    }

    /**
     * 获取临时文件目录
     *
     * @return string
     */
    public static function getTempDir()
    {
        if (function_exists('sys_get_temp_dir')) {
            $tmp = sys_get_temp_dir();
        } elseif (!empty($_SERVER['TMP'])) {
            $tmp = $_SERVER['TMP'];
        } elseif (!empty($_SERVER['TEMP'])) {
            $tmp = $_SERVER['TEMP'];
        } elseif (!empty($_SERVER['TMPDIR'])) {
            $tmp = $_SERVER['TMPDIR'];
        } else {
            $tmp = getcwd();
        }

        return $tmp;
    }
}
