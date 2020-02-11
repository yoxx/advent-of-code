<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2015;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;

class Y2015D5 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        if ($this->test) {
            $this->testAssignment1($input, $output);
        } else {
            $number_of_nice_strings = 0;
            foreach ($input as $inputline) {
                if ($this->checkIfStringIsNiceP1($inputline)) {
                    $number_of_nice_strings++;
                }
            }
            $output->writeln("P1 Number of nice strings: " . $number_of_nice_strings);
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        if ($this->test) {
            $this->testAssignment2($input, $output);
        } else {
            $number_of_nice_strings = 0;
            foreach ($input as $inputline) {
                if ($this->checkIfStringIsNiceP2($inputline)) {
                    $number_of_nice_strings++;
                }
            }
            $output->writeln("P2 Number of nice strings: " . $number_of_nice_strings);
        }
    }

    private function testAssignment1(array $input, OutputInterface $output): void
    {
        $solution = $this->checkIfStringIsNiceP1($input[0]);
        if ($solution !== true) {
            $output->writeln("P1 Test1 Failed! String: " . $input[0] . " should have been nice! - is nice because it has at least three vowels (u...i...o...), a double letter (...dd...), and none of the disallowed substrings.");
        } else {
            $output->writeln("P1 Test1 success");
        }

        $solution = $this->checkIfStringIsNiceP1($input[1]);
        if ($solution !== true) {
            $output->writeln("P1 Test1 Failed! String: " . $input[1] . " should have been nice! - is nice because it has at least three vowels and a double letter");
        } else {
            $output->writeln("P1 Test1 success");
        }

        $solution = $this->checkIfStringIsNiceP1($input[2]);
        if ($solution !== false) {
            $output->writeln("P1 Test1 Failed! String: " . $input[2] . " should have been naughty! - is naughty because it has no double letter");
        } else {
            $output->writeln("P1 Test1 success");
        }

        $solution = $this->checkIfStringIsNiceP1($input[3]);
        if ($solution !== false) {
            $output->writeln("P1 Test1 Failed! String: " . $input[3] . " should have been naughty! - is naughty because it contains the string xy");
        } else {
            $output->writeln("P1 Test1 success");
        }

        $solution = $this->checkIfStringIsNiceP1($input[4]);
        if ($solution !== false) {
            $output->writeln("P1 Test1 Failed! String: " . $input[3] . " should have been naughty! - is naughty because it contains only one vowel");
        } else {
            $output->writeln("P1 Test1 success");
        }
    }

    private function testAssignment2(array $input, OutputInterface $output): void
    {
        $solution = $this->checkIfStringIsNiceP2($input[5]);
        if ($solution !== true) {
            $output->writeln("P2 Test2 Failed! String: " . $input[5] . " should have been nice! - is nice because is has a pair that appears twice (qj) and a letter that repeats with exactly one letter between them (zxz)");
        } else {
            $output->writeln("P2 Test2 success");
        }

        $solution = $this->checkIfStringIsNiceP2($input[6]);
        if ($solution !== true) {
            $output->writeln("P2 Test2 Failed! String: " . $input[6] . " should have been nice! - is nice because it has a pair that appears twice and a letter that repeats with one between, even though the letters used by each rule overlap.");
        } else {
            $output->writeln("P2 Test2 success");
        }

        $solution = $this->checkIfStringIsNiceP2($input[7]);
        if ($solution !== false) {
            $output->writeln("P2 Test2 Failed! String: " . $input[7] . " should have been naughty! - is naughty because it has a pair (tg) but no repeat with a single letter between them");
        } else {
            $output->writeln("P2 Test2 success");
        }

        $solution = $this->checkIfStringIsNiceP2($input[8]);
        if ($solution !== false) {
            $output->writeln("P2 Test2 Failed! String: " . $input[8] . " should have been naughty! - is naughty because it has a repeating letter with one between (odo), but no pair that appears twice.");
        } else {
            $output->writeln("P2 Test2 success");
        }
    }

    private function checkIfStringIsNiceP1(string $inputline): bool
    {
        /**
         * It does not contain the strings ab, cd, pq, or xy, even if they are part of one of the other requirements.
         */
        if (strpos($inputline, "ab") !== false || strpos($inputline, "cd") !== false || strpos($inputline, "pq") !== false || strpos($inputline, "xy") !== false) {
            // Contains one of the substrings that are not allowed.
            return false;
        }

        /**
         * It contains at least three vowels (aeiou only), like aei, xazegov, or aeiouaeiouaeiou.
         */
        $vowel_count = substr_count($inputline,"a") + substr_count($inputline,"e") + substr_count($inputline,"i") + substr_count($inputline,"o") + substr_count($inputline,"u");
        if ($vowel_count < 3) {
            // Should contain atleast 3 vowels
            return false;
        }

        /**
         * It contains at least one letter that appears twice in a row, like xx, abcdde (dd), or aabbccdd (aa, bb, cc, or dd).
         */
        if(!preg_match('/(.)\1/i', $inputline)) {
            return false;
        }

        return true;
    }

    private function checkIfStringIsNiceP2(string $inputline): bool
    {
        $check = false;
        /**
         * It contains a pair of any two letters that appears at least twice in the string without overlapping, like xyxy (xy) or aabcdefgaa (aa), but not like aaa (aa, but it overlaps).
         */
        $letters = range("a", "z");
        foreach ($letters as $letter) {
            $combo_to_check = $letter.$letter;
            if (substr_count($inputline, $combo_to_check) >= 2 ) {
                $check = true;
                break;
            }
            foreach ($letters as $letter2) {
                $combo_to_check = $letter.$letter2;
                if (substr_count($inputline, $combo_to_check) >= 2 ) {
                    $check = true;
                    break 2;
                }
            }
        }

        /**
         * It contains at least one letter which repeats with exactly one letter between them, like xyx, abcdefeghi (efe), or even aaa.
         */
        if ($check) {
            $check = false;
            foreach ($letters as $letter) {
                $str_chars = str_split($inputline);
                foreach ($str_chars as $key => $char) {
                    if ($char === $letter && isset($str_chars[$key+2]) && $str_chars[$key+2] === $letter) {
                        // We have a repeating char!
                        $check = true;
                        break;
                    }
                }
            }
        }

        return $check;
    }
}
