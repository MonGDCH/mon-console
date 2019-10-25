<?php
class Demo extends \mon\console\Command
{
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