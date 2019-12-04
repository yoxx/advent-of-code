<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2019D4 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                [$start, $end] = explode("-", $line);
            }
            $amount_of_pw = $this->checkPwInRange((int) $start, (int) $end);
            $output->writeln("P1: Amount of passwords that meet the criteria: " . $amount_of_pw);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                [$start, $end] = explode("-", $line);
            }
            $amount_of_pw = $this->checkPwInRangeP2((int) $start, (int) $end);
            $output->writeln("P2: Amount of passwords that meet the criteria: " . $amount_of_pw);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    private function checkPwInRange(int $start, int $end): int
    {
        $amount_of_valid_pw = 0;
        /**
         * Rules:
         * It is a six-digit number.
         * The value is within the range given in your puzzle input.
         * Two adjacent digits are the same (like 22 in 122345).
         * Going from left to right, the digits never decrease; they only ever increase or stay the same (like 111123 or 135679).
         * 111111 meets these criteria (double 11, never decreases).
         * 223450 does not meet these criteria (decreasing pair of digits 50).
         * 123789 does not meet these criteria (no double).
         */
        while($start < $end) {
            $numbers = str_split((string) $start);
            $has_double = false;
            $is_valid_ordering = true;

            foreach ($numbers as $key => $value) {
                if($key === 0) {
                    continue;
                }
                if ((int) $value < $numbers[$key -1]) {
                    $is_valid_ordering = false;
                    break;
                }
                if ((int) $value === (int) $numbers[$key -1]) {
                    $has_double = true;
                }
            }

            if ($has_double && $is_valid_ordering) {
                $amount_of_valid_pw++;
            }

            $start++;
        }

        return $amount_of_valid_pw;
    }

    private function checkPwInRangep2(int $start, int $end): int
    {
        $amount_of_valid_pw = 0;
        /**
         * Rules:
         * It is a six-digit number.
         * The value is within the range given in your puzzle input.
         * Two adjacent digits are the same (like 22 in 122345).
         * Going from left to right, the digits never decrease; they only ever increase or stay the same (like 111123 or 135679).
         * 111111 meets these criteria (double 11, never decreases).
         * 223450 does not meet these criteria (decreasing pair of digits 50).
         * 123789 does not meet these criteria (no double).
         */
        while($start < $end) {
            $numbers = str_split((string) $start);
            $has_double = false;
            $double_numbers = [];
            $is_valid_ordering = true;

            foreach ($numbers as $key => $value) {
                if($key === 0) {
                    continue;
                }
                if ((int) $value < $numbers[$key -1]) {
                    $is_valid_ordering = false;
                    break;
                }
                if ((int) $value === (int) $numbers[$key -1]) {
                    if (isset($double_numbers[$value])) {
                        $double_numbers[$value] = $double_numbers[$value] + 1;
                    } else {
                        $double_numbers[$value] = 1;
                    }
                }
            }

            // Should have aleast 1 set of doubles
            foreach ($double_numbers as $number_count) {
                if ($number_count == 1 && $is_valid_ordering) {
                    $amount_of_valid_pw++;
                    break;
                }
            }

            $start++;
        }

        return $amount_of_valid_pw;
    }
}
