<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D9 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $instrutionset = array_map('intval', $this->getInputLine(true, ","));

        $int_code_computer = new IntCodeComputer();
        if ($this->test) {
            $int_code_computer->setPrintAllOutput(true);
        } else {
            $int_code_computer->setStartInput(1);
        }
        [$opcode_cache, $output_code] = $int_code_computer->runOpcode($instrutionset, $output);
        $last_output_code = $output_code;

        $output->writeln("P1: The BOOST key code is: " . $last_output_code);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        if ($this->test) {
            $output->writeln("P2 has no test");
            return;
        }

        $instrutionset = array_map('intval', $this->getInputLine(true, ","));

        $int_code_computer = new IntCodeComputer();
        $int_code_computer->setStartInput(2);
        [$opcode_cache, $output_code] = $int_code_computer->runOpcode($instrutionset, $output);
        $last_output_code = $output_code;

        $output->writeln("P2: The coordinates are: " . $last_output_code);
    }
}
