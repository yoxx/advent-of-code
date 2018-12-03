<?php

namespace yoxx\Advent;

use Symfony\Component\Console\Output\OutputInterface;

abstract class Day
{
    protected $input_file;

    abstract public function run(OutputInterface $output);

    public function setInput(string $input_file): void
    {
        $this->input_file = $input_file;
    }
}