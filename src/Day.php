<?php declare(strict_types=1);

namespace yoxx\Advent;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

abstract class Day
{
    protected string $input_file;
    protected bool $test = false;

    public function run(OutputInterface $output, int $part, bool $test): void
    {
        $this->test = $test;
        switch($part) {
            case RunAssignmentCommand::RUN_PART_1:
                $this->runAssignment1($output);
                break;
            case RunAssignmentCommand::RUN_PART_2:
                $this->runAssignment2($output);
                break;
            default:
                $this->runAssignment1($output);
                $this->runAssignment2($output);
        }
    }

    abstract public function runAssignment1(OutputInterface $output): void;

    abstract public function runAssignment2(OutputInterface $output): void;

    public function setInput(string $input_file): void
    {
        $this->input_file = $input_file;
    }
}
