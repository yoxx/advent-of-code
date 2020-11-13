<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2018D2 extends Day
{
    public function run(OutputInterface $output, int $part, bool $test): void
    {
        $input = [];
        $handle = fopen($this->input_file, "rb");
        $string_length = null;
        if ($handle) {
            $two_count = 0;
            $three_count = 0;
            while (($line = fgets($handle)) !== false) {
                $string_array = \str_split($line);
                $string_length = \count($string_array);

                if ($this->countIfXLetters($string_array, $string_length, 2)) {
                    $two_count++;
                }
                if ($this->countIfXLetters($string_array, $string_length, 3)) {
                    $three_count++;
                }

                $input[] = $string_array;
            }
            $output->writeln("Answer1: " . $two_count . "*" . $three_count . "=" . $two_count * $three_count);

            // Part 2
            $input_length = \count($input);
            $compaired_true = false;
            foreach ($input as $string_array) {
                for ($count = 0; $count < $input_length; $count++) {
                    $compaired = $this->compareStrings($string_array, $string_length, $input[$count]);
                    if ($compaired) {
                        $compaired_true = true;
                        $output->writeln("Two strings matched! The matched letters are: " . $compaired);
                        break;
                    }
                }
                if ($compaired_true) {
                    break;
                }
            }

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    private function countIfXLetters(array $string_array, int $string_length, int $expected): bool
    {
        foreach( $string_array as $char) {
            $char_count = 0;
            for ($count = 0; $count < $string_length; $count++) {
                if ($char === $string_array[$count]) {
                    $char_count++;
                }
            }

            if ($char_count === $expected) {
                return true;
            }
        }

        return false;
    }


    private function compareStrings(array $string1, int $string_length, array $string2): ?string
    {
        $correct_count = 0;
        $wrong_letter = 0;
        for ($count = 0; $count < $string_length; $count++) {
            if ($string1[$count] === $string2[$count]) {
                $correct_count++;
            } else {
                $wrong_letter = $count;
            }
        }

        // We have a match on the letters
        if ($correct_count === $string_length - 1) {
            unset($string1[$wrong_letter]);
            return implode("", $string1);
        }
        return null;
    }
}
