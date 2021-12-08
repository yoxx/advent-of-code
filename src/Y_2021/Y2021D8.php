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
            $digit_list = $this->determineDigits(explode(" ", $segment_list[0]));
            $output_values += $this->parseDigitsInOutput($digit_list, explode(" ", $segment_list[1]));
        }

        return $output_values;
    }

    public function determineDigits(array $unknown_digit_inputs): array
    {
        /**
         * Create a digit config
         *  AAAA        0000
         * B    C      1    2
         * B    C      1    2
         *  DDDD   ===  3333
         * E    F      4    5
         * E    F      4    5
         *  GGGG        6666
         */
        $digit_config = array_fill(0, 7, null);
        // List in which we save our digits
        $digit_list = [];
        while(!empty($unknown_digit_inputs)) {
            // Get the digits 1,4,7 and 8
            foreach ($unknown_digit_inputs as $key => $digit) {
                $trimmed_digit = trim($digit);
                // Digit 1
                if (strlen($trimmed_digit) === 2) {
                    $digit_list[1] = str_split($trimmed_digit);
                    unset($unknown_digit_inputs[$key]);
                    continue;
                }
                // Digit 4
                if (strlen($trimmed_digit) === 4) {
                    $digit_list[4] = str_split($trimmed_digit);
                    unset($unknown_digit_inputs[$key]);
                    continue;
                }
                // Digit 7
                if (strlen($trimmed_digit) === 3) {
                    $digit_list[7] = str_split($trimmed_digit);
                    unset($unknown_digit_inputs[$key]);
                    continue;
                }
                // Digit 8
                if (strlen($trimmed_digit) === 7) {
                    $digit_list[8] = str_split($trimmed_digit);
                    unset($unknown_digit_inputs[$key]);
                    continue;
                }

                // Deductions:
                // If we know digit 1 and 7 we know config line A with 100%
                if (!isset($digit_config[0]) && isset($digit_list[1], $digit_list[7])) {
                    $diff = array_diff($digit_list[7], $digit_list[1]);
                    $digit_config[0] = array_pop($diff);
                }
                // If you know 3/4 and A you know G 100% and B 100%
                if (!isset($digit_config[1]) && isset($digit_config[0], $digit_list[3], $digit_list[4])) {
                    $diff_g = array_diff(array_diff($digit_list[3], $digit_list[4]), [$digit_config[0]]);
                    $digit_config[6] = array_pop($diff_g);
                    $diff_b = array_diff($digit_list[4],$digit_list[3]);//, [$digit_config[0]]);
                    $digit_config[1] = array_pop($diff_b);
                }
                // Now you know D 100% from 4
                if (!isset($digit_config[3]) && isset($digit_config[1], $digit_config[2], $digit_config[5])) {
                    $diff = array_diff($digit_list[4], [$digit_config[1], $digit_config[2], $digit_config[5]]);
                    $digit_config[3] = array_pop($diff);
                }
                // If you know D you MUST know 9 as you now have ALL the digits
                if (isset($digit_config[3]) && count(array_intersect(str_split($trimmed_digit), [$digit_config[0], $digit_config[1], $digit_config[2], $digit_config[3], $digit_config[5], $digit_config[6]])) === 6) {
                    $digit_list[9] = str_split($trimmed_digit);
                    unset($unknown_digit_inputs[$key]);
                    // IF we have 9 and 8 we can determine E
                    $diff = array_diff($digit_list[8], $digit_list[9]);
                    $digit_config[4] = array_pop($diff);
                    continue;
                }
                // We should now know 0/6
                if (isset($digit_config[4])) {
                    // We have a 0
                    if (count(array_intersect(str_split($trimmed_digit), [$digit_config[0], $digit_config[1], $digit_config[2], $digit_config[4], $digit_config[5], $digit_config[6]])) === 6) {
                        $digit_list[0] = str_split($trimmed_digit);
                        unset($unknown_digit_inputs[$key]);
                    } else {
                        $digit_list[6] = str_split($trimmed_digit);
                        unset($unknown_digit_inputs[$key]);
                    }
                }
                // The string with strlen 5
                if (strlen($trimmed_digit) === 5) {
                    $digit_arr = str_split($trimmed_digit);
                    // containing the letters of digit 7 is digit 3
                    if (isset($digit_list[7]) && count(array_intersect($digit_arr, $digit_list[7])) === 3) {
                        $digit_list[3] = $digit_arr;
                        unset($unknown_digit_inputs[$key]);
                        continue;
                    }
                    // If you know B and set it to 5 string len you know 5 and C 100%
                    if (!isset($digit_config[2]) && isset($digit_config[1], $digit_list[1])) {
                        if (in_array($digit_config[1], $digit_arr, false)) {
                            $digit_list[5] = $digit_arr;
                            unset($unknown_digit_inputs[$key]);
                            if (!isset($digit_list[1])) {
                                //what...
                                $a = 1;
                            }
                            $diff = array_diff($digit_list[1], $digit_list[5]);
                            $digit_config[2] = array_pop($diff);
                            continue;
                        }
                    }
                    // We know 3/5 and if we check for length 5 and we know 2 and F 100% as its the only one left
                    if (isset($digit_list[3], $digit_list[5])) {
                        if (in_array($digit_config[2], $digit_arr, false)) {
                            $digit_list[2] = $digit_arr;
                            unset($unknown_digit_inputs[$key]);
                            $diff = array_diff($digit_list[1], $digit_list[2]);
                            $digit_config[5] = array_pop($diff);
                            continue;
                        }
                    }
                    ksort($digit_list);
                }
            }
        }
        return $digit_list;
    }

    public function parseDigitsInOutput(array $digit_list, array $output): int
    {
        $string_output = "";
        foreach($output as $digit) {
            foreach($digit_list as $digit_num => $parsed_digit) {
                $input = str_split(trim($digit));
                if (count($input) == count($parsed_digit) && count(array_intersect($parsed_digit, $input)) === count($input)) {
                    $string_output .= $digit_num;
                    break;
                }
            }
        }
        // Add as string return as int
        return (int) $string_output;
    }
}
