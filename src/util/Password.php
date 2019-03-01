<?php
namespace Mon\FCli\util;

use Mon\FCli\CliException;

/**
 * 输入密码，隐藏字符
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Password
{
	/**
	 * 发起密码输入
	 *
	 * @param  string $tips [description]
	 * @return [type]       [description]
	 */
	public static function interaction($tips = 'Enter Password:')
	{
		// liunx通过bash脚本获取输入的密码
		if (self::bashIsAvailable()) {
            $command = sprintf('bash -c "read -p \'%s\' -s user_input && echo $user_input"', $tips);
            $password = self::runCommand($command, false);
            echo "\n";
            return $password;
        }

        // windows通过vb脚本获取输入的密码
        if(self::isWindows()){
        	$vbScript = self::getTempDir() . '/hidden_prompt_input.vbs';
            file_put_contents($vbScript, 'wscript.echo(InputBox("' . $prompt . '", "", "Enter your password"))');
            $command = 'cscript //nologo ' . escapeshellarg($vbScript);
            $password = rtrim(shell_exec($command));
            unlink($vbScript);

            return $password;
        }

        throw new CliException('Can not invoke bash shell env');
	}

	/**
	 * 获取临时文件目录
	 *
	 * @return [type] [description]
	 */
	public static function getTempDir()
    {
        if (\function_exists('sys_get_temp_dir')) {
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

    /**
     * 判断bash脚本是否可用
     *
     * @return bool
     */
    public static function bashIsAvailable()
    {
        $checkCmd = "bash -c 'echo OK'";
        return self::runCommand($checkCmd, false) === 'OK';
    }

    /**
     * 判断是否为windows环境
     *
     * @return boolean [description]
     */
    public static function isWindows()
    {
        return stripos(PHP_OS, 'WIN') !== false;
    }

    /**
     * PHP拉取新进程执行脚本
     * 1. system
     * 2. passthru
     * 3. exec
     * 4. shell_exec
     * 
     * @param $command
     * @param bool $returnStatus
     * @return array|string
     */
    public static function runCommand($command, $returnStatus = true)
    {
        $return_var = 1;
        //system
        if (function_exists('system')) {
            ob_start();
            system($command, $return_var);
            $output = ob_get_contents();
            ob_end_clean();
            // passthru
        } elseif (function_exists('passthru')) {
            ob_start();
            passthru($command, $return_var);
            $output = ob_get_contents();
            ob_end_clean();
            //exec
        } else {
            if (function_exists('exec')) {
                exec($command, $output, $return_var);
                $output = implode("\n", $output);
                //shell_exec
            } else {
                if (function_exists('shell_exec')) {
                    $output = shell_exec($command);
                } else {
                    $output = 'Command execution not possible on this system';
                    $return_var = 0;
                }
            }
        }
        if ($returnStatus) {
            return ['output' => trim($output), 'status' => $return_var];
        }

        return trim($output);
    }
}