<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D10 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->calculateIllegalCharScore($this->getInputArray(true)));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->completeIncompleteStringsAndCalculateScore($this->getInputArray(true)));
    }

    public function calculateIllegalCharScore(array $input): int
    {
        $char_points = [
            ")" => 3,
            "]" => 57,
            "}" => 1197,
            ">" => 25137
        ];
        $total_points = 0;

        foreach ($input as $line) {
            $opening_brackets = [];
            foreach ($line as $char) {
                if (in_array($char, ["{","[","(","<"])) {
                    // We found an opening bracket, noice
                    $opening_brackets[] = $char;
                    continue;
                }
                // Now we only have closing brackets
                // If the last closing bracket is NOT the equal of the opening bracket. Bork.
                $last_opening_bracket = array_pop($opening_brackets);
                $expected_closing_bracket = null;
                switch ($last_opening_bracket) {
                    case "{":
                        $expected_closing_bracket = "}";
                        break;
                    case "(":
                        $expected_closing_bracket = ")";
                        break;
                    case "<":
                        $expected_closing_bracket = ">";
                        break;
                    case "[":
                        $expected_closing_bracket = "]";
                        break;
                }

                if ($expected_closing_bracket !== $char) {
                    $total_points += $char_points[$char];
                    break;
                }
            }
        }

        return $total_points;
    }

    public function completeIncompleteStringsAndCalculateScore(array $input): int
    {
        $opening_brackets_of_incomplete_lines = [];
        // First we remove all corrupt lines and create an array of opening brackets
        foreach ($input as $line_key => $line) {
            $opening_brackets = [];
            foreach ($line as $char) {
                if (in_array($char, ["{","[","(","<"])) {
                    // We found an opening bracket, noice
                    $opening_brackets[] = $char;
                    continue;
                }
                // Now we only have closing brackets
                // If the last closing bracket is NOT the equal of the opening bracket. Bork.
                $last_opening_bracket = array_pop($opening_brackets);
                $expected_closing_bracket = null;
                switch ($last_opening_bracket) {
                    case "{":
                        $expected_closing_bracket = "}";
                        break;
                    case "(":
                        $expected_closing_bracket = ")";
                        break;
                    case "<":
                        $expected_closing_bracket = ">";
                        break;
                    case "[":
                        $expected_closing_bracket = "]";
                        break;
                }

                if ($expected_closing_bracket !== $char) {
                    // Corrupted line remove from the input array
                    unset($input[$line_key]);
                    continue 2;
                }
            }
            // If we end up here we have an incomplete line
            $opening_brackets_of_incomplete_lines[] = $opening_brackets;
        }

        $char_points = [
            ")" => 1,
            "]" => 2,
            "}" => 3,
            ">" => 4
        ];
        // Now we loop through our incomplete lines and create a completion string of which we count the points
        $total_points_per_line = [];
        foreach ($opening_brackets_of_incomplete_lines as $incomplete_line) {
            $completion_line = "";
            $total_points = 0;
            for ($index = count($incomplete_line) - 1; $index >= 0; $index--) {
                $expected_closing_bracket = null;
                switch ($incomplete_line[$index]) {
                    case "{":
                        $expected_closing_bracket = "}";
                        break;
                    case "(":
                        $expected_closing_bracket = ")";
                        break;
                    case "<":
                        $expected_closing_bracket = ">";
                        break;
                    case "[":
                        $expected_closing_bracket = "]";
                        break;
                }
                $completion_line .= $expected_closing_bracket;
            }
            // NOW we calculate the points of the completion line
            foreach (str_split($completion_line) as $char) {
                $total_points = $total_points * 5 + $char_points[$char];
            }
            $total_points_per_line[] = $total_points;
        }
        sort($total_points_per_line);
        return $total_points_per_line[(floor(count($total_points_per_line)/2))];
    }
}
