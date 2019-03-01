<?php
namespace Mon\FCli\test;

use Mon\FCli\Command;
use Mon\FCli\util\Password;

class Test extends Command
{
	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute()
	{
		$name = $this->input->read('What\'s your name?  ');
		$password = Password::interaction();
		echo 'Hello '.$name.', Your password is '.$password;
	}
}