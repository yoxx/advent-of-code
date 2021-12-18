<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D18 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $current_snailfish_number = "";
        $input = $this->getInputArray();
        foreach ($input as $snailfishnum) {
            $current_snailfish_number = $this->addition($current_snailfish_number, trim($snailfishnum));
            $current_snailfish_number = $this->reduce($current_snailfish_number);
        }
        $output->writeln("P1: The solution is: " . $this->calculateMagnitude($current_snailfish_number));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $largest_magnitude = 0;
        $input = $this->getInputArray();
        foreach ($input as $num1) {
            foreach ($input as $num2) {
                $current_magnitude = $this->calculateMagnitude($this->reduce($this->addition(trim($num1), trim($num2))));
                if ($current_magnitude > $largest_magnitude) {
                    $largest_magnitude = $current_magnitude;
                }
            }
        }
        $output->writeln("P2: The solution is: " . $largest_magnitude);
    }

    public function calculateMagnitude(string $snailfish_num): int
    {
        // Find all numbers that are like so [x,x]
        preg_match_all("/\[\d+,\d+\]/", $snailfish_num, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            $nums = explode(",", str_replace(["[", "]"], "", $matches[0][0]));
            $num = ((int)$nums[0] * 3) + ((int)$nums[1] * 2);
            $snailfish_num = preg_replace("/" . str_replace(array("[", "]"), array("\[", "\]"), $matches[0][0]) . "/", (string)$num, $snailfish_num, 1);
            return $this->calculateMagnitude($snailfish_num);
        }
        return (int)$snailfish_num;
    }

    public function reduce(string $snailfish_num): string
    {
        // Check if a pair is nested inside four pairs if so explode
        $snailfish_num = $this->explodePair($snailfish_num);
        // split any number above 10
        $snailfish_num = $this->splitHighNums($snailfish_num);
        // Make sure to keep reducing till you are all done
        return $snailfish_num;
    }

    public function splitHighNums(string $snailfish_num): string
    {
        // First we find any double digit numbers
        preg_match_all("/\d\d/m", $snailfish_num, $matches, PREG_SET_ORDER, 0);
        // If there are none simply return our input
        if (!empty($matches)) {
            // We split only the first item recursion will handle the rest
            $split_num = "[" . floor((int)$matches[0][0] / 2) . "," . ceil((int)$matches[0][0] / 2) . "]";
            $snailfish_num = preg_replace("/" . $matches[0][0] . "/", $split_num, $snailfish_num, 1);
            // After the first split we simply call explodePairs again just to check if anything needs to go BOOM
            $snailfish_num = $this->explodePair($snailfish_num);
            return $this->splitHighNums($snailfish_num);
        }
        return $snailfish_num;
    }

    public function explodePair(string $snailfish_num): string
    {
        $count_opening_brackets = 0;
        foreach (str_split($snailfish_num) as $index => $char) {
            if ($count_opening_brackets === 5) {
                // We have found a pair that should be exploded
                // Get the next closing bracket
                $closing_index = strpos($snailfish_num, "]", $index);
                $contents = explode(",", substr($snailfish_num, $index, $closing_index - $index));
                $pre_segment = substr(strrev(substr($snailfish_num, 0, $index)), 1);
                $pre_segment_str = str_replace(["[", "]"], "", $pre_segment);
                $pre_segment_number = (str_contains($pre_segment_str, ",")) ? explode(",", $pre_segment_str)[1] : null;
                $after_segment = substr($snailfish_num, $closing_index + 1);
                $after_segment_str = str_replace(["[", "]"], "", $after_segment);
                $after_segment_number = (str_contains($after_segment_str, ",")) ? explode(",", $after_segment_str)[1] : null;
                if ($pre_segment_number !== null && $pre_segment_number !== "") {
                    $pre_segment_number_solution = (int)strrev($pre_segment_number) + (int)$contents[0];
                    $pre_segment = strrev(preg_replace("/" . $pre_segment_number . "/", strrev((string)$pre_segment_number_solution), $pre_segment, 1));
                }
                if ($after_segment_number !== null && $after_segment_number !== "") {
                    $after_segment_number_solution = (int)$after_segment_number + (int)$contents[1];
                    $after_segment = preg_replace("/" . $after_segment_number . "/", (string)$after_segment_number_solution, $after_segment, 1);
                }
                $snailfish_num = $pre_segment . "0" . $after_segment;
                // Dont forget to recursse #Yolo
                return $this->explodePair($snailfish_num);
            }
            if ($char === "[") {
                $count_opening_brackets++;
            }
            if ($char === "]") {
                $count_opening_brackets--;
            }
        }
        // No more exploding to do
        return $snailfish_num;
    }

    public function addition(string $original, string $new_number): string
    {
        if ($original === "") {
            return $new_number;
        }
        // We have 2 actual numbers lets add them
        return "[" . $original . "," . $new_number . "]";
    }
}
