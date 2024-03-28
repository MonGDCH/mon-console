<?php

namespace command;

use mon\console\Command;
use mon\console\Output;

class Spin extends Command
{
	protected static $defaultName = 'spin';

	protected static $defaultDescription = 'This is Spin Command!';

	protected static $defaultGroup = 'aaa';

	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute($input, Output $output)
	{
		$output->block('Spinning...');
		$output->spinBegiin();

		for ($i = 0; $i < 100; $i++) {
			if ($i % 10 == 0) {
				$output->spin();
			}
			usleep(1000);
		}

		$output->spinEnd();
		$output->block('Done!', 'SUCCESS');
	}
}
