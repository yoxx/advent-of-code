<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D2 extends Day
{
    public function run(OutputInterface $output, int $part): void
    {
        switch($part) {
            case 1:
                $this->runAssignment1($output);
                break;
            case 2:
                $this->runAssignment2($output);
                break;
            default:
                $this->runAssignment1($output);
                $this->runAssignment2($output);
        }

    }

    public function runAssignment1(OutputInterface $output): void
    {
        $opcode_cache = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $opcode_cache = array_map('intval', explode(",",$line));
            }

            // Before running the program override values given by advent of code
            $opcode_cache[1] = 12;
            $opcode_cache[2] = 2;

            $instructionset_length = \count($opcode_cache);
            for ($opcode = 0; $opcode < $instructionset_length; $opcode += 4) {
                if ($opcode_cache[$opcode] === 99) {
                    break;
                }
                $opcode_cache = $this->handleOpcode($opcode_cache, $opcode_cache[$opcode], $opcode_cache[$opcode + 1], $opcode_cache[$opcode + 2], $opcode_cache[$opcode + 3]);
            }

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
                    $instructionset_length = \count($opcode_cache);
                    for ($opcode = 0; $opcode < $instructionset_length; $opcode += 4) {
                        if ($opcode_cache[$opcode] === 99) {
                            break;
                        }
                        $opcode_cache = $this->handleOpcode($opcode_cache, $opcode_cache[$opcode], $opcode_cache[$opcode + 1], $opcode_cache[$opcode + 2], $opcode_cache[$opcode + 3]);
                    }
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

    /**
     * @throws Error
     */
    private function handleOpcode(array $opcode_cache, int $opcode, int $val1, int $val2, int $target): array
    {
        switch($opcode) {
            case 1:
                $opcode_cache[$target] = $opcode_cache[$val1] + $opcode_cache[$val2];
                break;
            case 2:
                $opcode_cache[$target] = $opcode_cache[$val1] * $opcode_cache[$val2];
                break;
            case 99:
                throw new Error("Halt Program");
                break;
            default:
                throw new Error("Undefined opcode encountered: " . $opcode);
                break;
        }

        return $opcode_cache;
    }
}
