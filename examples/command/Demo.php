<?php
class Demo extends \Mon\console\Command
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