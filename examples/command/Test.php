<?php

class Test extends \mon\console\Command
{
	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute($input, $output)
	{
		$name = $input->read('What\'s your name?  ');
		$password = $input->password();
		
		return $output->write('Hello '.$name.', Your password is '.$password);
	}
}