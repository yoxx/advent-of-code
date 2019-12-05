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
            Y2019Utils::runOpcode($opcode_cache, $output, 1);

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

            if ($this->test) {
                $output->writeln("P2-test - we expect 999 if input > 8, 1000 input === 8, 1001 input > 8");
                $output->writeln("Running with 3");
                Y2019Utils::runOpcode($opcode_cache, $output, 3);

                $output->writeln("Running with 8");
                Y2019Utils::runOpcode($opcode_cache, $output, 8);

                $output->writeln("Running with 12");
                Y2019Utils::runOpcode($opcode_cache, $output, 12);
            } else {
                $output->write("P2: The diagnostic code is: ");
                Y2019Utils::runOpcode($opcode_cache, $output, 5);
            }

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
