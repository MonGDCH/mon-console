<?php
namespace Mon\console\libs;

/**
 * 解析类库
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
final class Parse
{
	/**
     * 定义boolean值对照
     */
    const TRUE_WORDS = '|on|yes|true|';
    const FALSE_WORDS = '|off|no|false|';

    /**
     * 解析argv参数
     *
     * @param  array  $argv    [description]
     * @return [type]          [description]
     */
	public static function parseArgv(array $argv)
	{
        $args = $short_opts = $long_opts = [];
        $isCommand = true;
        $command = null;
        while(null !== $token = array_shift($argv))
        {
            // 第一个标签为指令
            if($isCommand){
                $command = $token;
                $isCommand = false;
                continue;
            }

            // 处理options
            if($token[0] == '-'){
                $isLong = false;
                $option = substr($token, 1);
                $value = true;

                // 长标签处理
                if($option[0] === '-'){
                    // 处理长option, --option
                    $isLong = true;
                    $option = substr($option, 1);

                    // 判断是否存在option值定义, --option:123
                    if (strpos($option, ':') !== false) {
                        list($option, $value) = explode(':', $option, 2);
                    }
                }
                // 短标签处理
                elseif(strlen($option) > 2 && $option[1] === ':'){
                    list($option, $value) = explode(':', $option, 2);
                }
                // 保存标签
                if($isLong){
                    $long_opts[$option] = self::parseBool($value);
                }
                else{
                    $short_opts[$option] = self::parseBool($value);
                }
            }
            else{
                // 处理参数
                if(strpos($token, ':') !== false){
                    list($name, $value) = explode(':', $token, 2);
                    $args[$name] = self::parseBool($value);
                }
                else{
                    $args[] = $token;
                }
            }
        }

        return [$command, $args, $short_opts, $long_opts];
	}

	/**
	 * 解析转义boolean值
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public static function parseBool($val)
	{
		$sVal = strtolower($val);
        if(false !== strpos(self::TRUE_WORDS, "|{$sVal}|")){
            return true;
        }
        if(false !== strpos(self::FALSE_WORDS, "|{$sVal}|")){
            return false;
        }
        // 默认返回原值
        return $val;
	}
}