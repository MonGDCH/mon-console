<?php

namespace command;

use mon\console\Command;

class Demo extends Command
{
	protected static $defaultName = 'demos';

	protected static $defaultDescription = 'This is Demo Command!';


	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute($input, $output)
	{
		return $output->dump($input->getArgs());
	}
}
