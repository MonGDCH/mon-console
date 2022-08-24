<?php

use mon\console\Command;

class Test extends Command
{
	protected static $defaultName = 'tests';


	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute($input, $output)
	{
		$name = $input->read('What\'s your name?  ');
		$password = $input->password();

		return $output->write('Hello ' . $name . ', Your password is ' . $password);
	}
}
