<?php

namespace command;

use mon\console\interfaces\Command;

class Not implements Command
{
    public function execute($input, $output)
    {
        $output->write(Not::class);
    }
}
