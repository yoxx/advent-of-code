<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D8 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->parseUniqueDigitsInOutputP1());
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->part2());
    }

    public function parseUniqueDigitsInOutputP1(): int
    {
        $input = $this->getInputArray(true, " | ");

        $amount_of_unique_digit_appearances = 0;
        foreach ($input as $seven_segement_values) {
            $output_part = explode(" ", $seven_segement_values[1]);
            foreach ($output_part as $digits) {
                if (in_array(strlen(trim($digits)), [1 => 2, 4 => 4, 7 => 3, 8 => 7])) {
                    $amount_of_unique_digit_appearances++;
                }
            }
        }
        return $amount_of_unique_digit_appearances;
    }

    public function part2(): int
    {
        $input = $this->getInputArray(true, " | ");
        $output_values = 0;
        foreach($input as $segment_list) {
            $digit_list = $this->determineDigits($segment_list[0]);
            $output_values += $this->parseDigitsInOutput($digit_list, $segment_list[1]);
        }

        return $output_values;
    }

    public function determineDigits(array $unknown_digit_inputs): array
    {
        /**
         * Create a digit config
         *  1111
         * 2    3
         * 2    3
         *  4444
         * 5    6
         * 5    6
         *  7777
         */
        $digit_config = array_fill(0, 8, null);
        // List in wich we save our digits
        $digit_list = [];
        /** Loop through our unknown digit inputs
         * IF we find a digit we can use that to determine other digits
         * KNOWN things:
         * - 1,4,7,8 are unique values
         * - if we know digit 1 and 7 we know config line 1
         *
         */
        return [];
    }

    public function parseDigitsInOutput(array $digit_list, array $output): int
    {
        // Add as string return as int
        return 0;
    }
}
