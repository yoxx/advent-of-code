<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D5 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $opcode_cache = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $opcode_cache = array_map('intval', explode(",",$line));
            }

            $output->write("P1: The result for input 1 is: ");
            $int_code_computer = new IntCodeComputer();
            $int_code_computer->setStartInput(1);
            $int_code_computer->runOpcode($opcode_cache, $output);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $opcode_cache = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $opcode_cache = array_map('intval', explode(",",$line));
            }

            $int_code_computer = new IntCodeComputer();
            if ($this->test) {
                $output->writeln("P2-test - we expect 999 if input > 8, 1000 input === 8, 1001 input > 8");
                $output->writeln("Running with 3");
                $int_code_computer->setStartInput(3);
                $int_code_computer->runOpcode($opcode_cache, $output);

                $output->writeln("Running with 8");
                $int_code_computer->setStartInput(8);
                $int_code_computer->runOpcode($opcode_cache, $output);

                $output->writeln("Running with 12");
                $int_code_computer->setStartInput(12);
                $int_code_computer->runOpcode($opcode_cache, $output);
            } else {
                $output->write("P2: The diagnostic code is: ");
                $int_code_computer->setStartInput(5);
                $int_code_computer->runOpcode($opcode_cache, $output);
            }

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }
}
