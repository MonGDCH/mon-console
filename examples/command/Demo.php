<?php
namespace Mon\FCli\test;

use Mon\FCli\Command;

class Demo extends Command
{
	/**
	 * 执行指令
	 *
	 * @return [type] [description]
	 */
	public function execute()
	{
		$args = $this->input->getArgs();

		var_dump($args);
	}
}