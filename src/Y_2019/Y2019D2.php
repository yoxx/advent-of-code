<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D2 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $opcode_cache = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $opcode_cache = array_map('intval', explode(",",$line));
            }

            // Before running the program override values given by advent of code
            if (!$this->test) {
                $opcode_cache[1] = 12;
                $opcode_cache[2] = 2;
            }

            $int_code_computer = new IntCodeComputer();
            [$opcode_cache, $output_code] = $int_code_computer->runOpcode($opcode_cache, $output);

            $output->writeln("P1: The result in \$opcode_cache is: " . $opcode_cache[0]);

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

            $original_input = $opcode_cache;
            for ($noun = 0;$noun < 100; $noun += 1) {
                for ($verb = 0;$verb < 100; $verb += 1) {
                    // Use original input
                    $opcode_cache = $original_input;
                    // Before running the program override values given by noun and verb
                    $opcode_cache[1] = $noun;
                    $opcode_cache[2] = $verb;
                    // Get the lenght
                    $int_code_computer = new IntCodeComputer();
                    [$opcode_cache, $output_code] = $int_code_computer->runOpcode($opcode_cache, $output);

                    if ($opcode_cache[0] === 19690720) {
                        break 2;
                    }
                }
            }

            $output->writeln("P2: The noun: " . $noun . " the verb: " . $verb);
            $output->writeln("P2: puzzle to solve is 100 * noun + verb = " . (100 * $noun + $verb));

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }
}
