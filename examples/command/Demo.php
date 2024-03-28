<?php

namespace command;

use mon\console\Command;

class Demo extends Command
{
	protected static $defaultName = 'demo';

	protected static $defaultDescription = 'This is Demo Command!';

	protected static $defaultGroup = 'aaa';

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
