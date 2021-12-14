<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D14 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        [$template, $rules] = $this->parseInput();

        for ($step = 0; $step < 10; $step++) {
            $template = $this->handlePairInsertion($template, $rules);
        }
        $output->writeln("P1: The solution is: " . $this->countChars($template));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        [$letter_counts, $pairs, $rules] = $this->parseInput2();

        for ($step = 0; $step < 40; $step++) {
            [$letter_counts, $pairs] = $this->handlePairInsertionBasedOnPairs($letter_counts, $pairs, $rules);
        }
        sort($letter_counts);
        $output->writeln("P2: The solution is: " . end($letter_counts) - reset($letter_counts));
    }

    public function countChars(string $input): int
    {
        $counts = array_count_values(str_split($input));
        sort($counts);
        return end($counts) - reset($counts);
    }

    public function handlePairInsertionBasedOnPairs(array $letter_counts, array $pairs, array $rules): array
    {
        $changed_pairs = [];
        foreach ($pairs as $pair => $value) {
            // You have a pair you know you need to add a letter in between and create 2 pairs.
            // For example NC will be come NBC which makes 2 paris NB and BC
            $letter_to_insert = $rules[$pair];
            // Up our character count
            if (!isset($letter_counts[$letter_to_insert])) {
                $letter_counts[$letter_to_insert] = $value;
            } else {
                $letter_counts[$letter_to_insert] += $value;
            }
            // Handle pairs
            $index_pair1 = $pair[0] . $letter_to_insert;
            if (!isset($changed_pairs[$index_pair1])) {
                $changed_pairs[$index_pair1] = $value;
            } else {
                $changed_pairs[$index_pair1] += $value;
            }
            $index_pair2 = $letter_to_insert . $pair[1];
            if (!isset($changed_pairs[$index_pair2])) {
                $changed_pairs[$index_pair2] = $value;
            } else {
                $changed_pairs[$index_pair2] += $value;
            }
        }
        return [$letter_counts, $changed_pairs];
    }

    public function handlePairInsertion(string $template, array $rules): string
    {
        // first map all matched rules to a changes array
        $chars_to_insert = [];
        foreach ($rules as $rule) {
            $matches = [];
            if ($rule[0][0] === $rule[0][1]) {
                $reg = "+/";
            } else {
                $reg = "/";
            }
            preg_match_all("/" . $rule[0] . $reg, $template, $matches, PREG_OFFSET_CAPTURE);
            foreach ($matches[0] as $match) {
                if (!empty($match)) {
                    $match_len = strlen($match[0]);
                    $pos = $match[1];
                    for ($count = 1; $count < $match_len; $count++) {
                        $chars_to_insert[$pos] = trim($rule[1]);
                        $pos++;
                    }
                }
            }
        }
        // sort on location (keys)
        ksort($chars_to_insert);
        // insert all changes
        // We start with an offset of 1 to account for the first location being the first of the two letters and we want to insert in between
        $offset = 1;
        foreach ($chars_to_insert as $location => $letter) {
            $template = substr($template, 0, $location + $offset) . $letter . substr($template, $location + $offset);
            $offset++;
        }

        return $template;
    }

    public function parseInput(): array
    {
        $rules = [];
        $input = $this->getInputArray();
        $template = $input[0];
        unset($input[0], $input[1]);

        foreach ($input as $item) {
            $rules[] = explode(" -> ", $item);
        }
        return [trim($template), $rules];
    }

    public function parseInput2(): array
    {
        $rules = [];
        $input = $this->getInputArray();
        $template = $input[0];
        unset($input[0], $input[1]);

        // First we split our template up into pairs
        $pairs = [];
        $letter_counts = [];
        $template_arr = str_split(trim($template));
        foreach (str_split($template) as $key => $char) {
            // Count the characters
            if (!isset($letter_counts[$char])) {
                $letter_counts[$char] = 1;
            } else {
                $letter_counts[$char]++;
            }
            // Early out for pairs
            if (!isset($template_arr[$key + 1])) {
                break;
            }
            // Setup pairs and count them if needed
            $pair_key = $char . $template_arr[$key + 1];
            if (!isset($pairs[$pair_key])) {
                $pairs[$pair_key] = 1;
            } else {
                $pairs[$pair_key]++;
            }
        }

        foreach ($input as $item) {
            $rule_arr = explode(" -> ", $item);
            $rules[$rule_arr[0]] = trim($rule_arr[1]);
        }
        return [$letter_counts, $pairs, $rules];
    }
}
