<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D7 extends Day
{
    public function run(OutputInterface $logger, int $part, bool $test): void
    {
        // First we read the instructions to an array [["A","B"],["C","D"]]
        $formatted_input = $this->getFormattedInput($logger);
        $unique_array = array_pop($formatted_input);

        $output = $this->determineOrder($formatted_input, $unique_array);
        $logger->writeln("The order in which to complete the instructions is: " . $output);
        $output2 = $this->calculateAmountOfTimeToComplete($formatted_input, $unique_array);
        $logger->writeln("The time it took to complete is: " . $output2["completion_time"] . " new order: " . $output2["order_string"]);
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    /**
     * Determines the order with multiple workersbut also the time to complete the work
     */
    private function calculateAmountOfTimeToComplete(array $formatted_input, array $unique_array): array
    {
        // Outputstring
        $output = ["order_string" => "", "completion_time" => 0];

        // Variables p2
        $amount_of_workers = 5;
        $time_to_assemble = 60;
        $alphabet = range('A', 'Z');

        /** To determine the order we first create the rules we expect to find in the following format
         * To reach X we create an array where we place all the letters and the rules that have to be met before they can be done
         *
         * For example:
         * "Step C must be finished before step A can begin."
         * "Step B must be finished before step E can begin."
         * "Step D must be finished before step E can begin."
         * "Step F must be finished before step E can begin."
         * Results in:
         * [
         *      [
         *          "A" => ["C"],
         *      ],
         *      [
         *          "E" => ["B","D","F"],
         *      ],
         */
        $solution_per_char = [];
        foreach ($unique_array as $char) {
            $solution_per_char[$char] = [];
            foreach ($formatted_input as $input) {
                if ($char === $input[1]) {
                    $solution_per_char[$char][] = $input[0];
                }
            }
        }

        $complete = false;
        $workers = [];
        for ($count=0; $count < $amount_of_workers; $count++) {
            $workers[$count] = [];
        }

        /** Next we need to determine what is our starting point.
         * This will be the only char_key that has no pre-requisites.
         */
        while (!$complete) {
            // Check if the workers have work to do and if so do this.
            foreach ($workers as $worker_id => $worker) {
                if (!empty($worker)) {
                    foreach ($worker as $next_in_order => $task) {
                        // Work some time on the task
                        $workers[$worker_id][$next_in_order]--;
                        // If the work is done, remove the letter from the arrays and reset the worker
                        if ($workers[$worker_id][$next_in_order] === 0) {
                            $workers[$worker_id] = [];

                            // Unset all instances of the letter
                            foreach ($solution_per_char as $char => $pre_req) {
                                if ($char === $next_in_order) {
                                    unset($solution_per_char[$char]);
                                } else {
                                    foreach ($pre_req as $key => $req) {
                                        if ($next_in_order === $req) {
                                            unset($solution_per_char[$char][$key]);
                                        }
                                    }
                                }
                            }

                            // Save our letter to the output string
                            $output["order_string"] .= $next_in_order;
                        }
                    }
                }
            }

            // Check if there is any new work available
            $tmp_output = [];
            foreach ($solution_per_char as $char => $pre_req) {
                // There are not more prereqs
                if (\count($pre_req) === 0) {
                    $tmp_output[] = $char;
                }
            }

            // There is work available
            if (!empty($tmp_output)) {
                // Sort our things alphabetically
                sort($tmp_output);

                // Dispatch work to worker
                foreach ($tmp_output as $work) {
                    for($count=0;$count<$amount_of_workers;$count++) {
                        if(empty($workers[$count])) {
                            $workers[$count][$work] = (int) array_search($work, $alphabet)+1 + $time_to_assemble;

                            // Fill the instances with our letter to show that we are working on them
                            foreach ($solution_per_char as $char => $pre_req) {
                                if ($char === $work) {
                                    $solution_per_char[$char][] = $work;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // We should be done with determining the order now
            if (\count($solution_per_char) === 0) {
                $complete = true;
            } else {
                $output["completion_time"] += 1;
            }
        }

        return $output;
    }

    /**
     * Return the order in which to execute
     */
    private function determineOrder(array $formatted_input, array $uniques): string
    {
        // Outputstring
        $output = "";

        /** To determine the order we first create the rules we expect to find in the following format
         * To reach X we create an array where we place all the letters and the rules that have to be met before they can be done
         *
         * For example:
         * "Step C must be finished before step A can begin."
         * "Step B must be finished before step E can begin."
         * "Step D must be finished before step E can begin."
         * "Step F must be finished before step E can begin."
         * Results in:
         * [
         *      [
         *          "A" => ["C"],
         *      ],
         *      [
         *          "E" => ["B","D","F"],
         *      ],
         */
        $solution_per_char = [];
        foreach ($uniques as $char) {
            $solution_per_char[$char] = [];
            foreach ($formatted_input as $input) {
                if ($char === $input[1]) {
                    $solution_per_char[$char][] = $input[0];
                }
            }
        }

        /** Next we need to determine what is our starting point.
         * This will be the only char_key that has no pre-requisites.
         */
        $complete = false;
        while (!$complete) {
            $tmp_output = [];
            foreach ($solution_per_char as $char => $pre_req) {
                // There are not more prereqs
                if (\count($pre_req) === 0) {
                    $tmp_output[] = $char;
                }
            }

            // Sort our things alphabetically
            sort($tmp_output);
            // Return our first letter
            $next_in_order = reset($tmp_output);

            // Unset all instances of the letter
            foreach ($solution_per_char as $char => $pre_req) {
                if ($char === $next_in_order) {
                    unset($solution_per_char[$char]);
                } else {
                    foreach ($pre_req as $key => $req) {
                        if ($next_in_order === $req) {
                            unset($solution_per_char[$char][$key]);
                        }
                    }
                }
            }

            // Save our letter to the output string
            $output .= $next_in_order;

            // We should be done with determining the order now
            if (\count($solution_per_char) === 0) {
                $complete = true;
            }
        }

        return $output;
    }

    /**
     * Return our puzzle input
     * [
     *  ["A","B]
     * ]
     */
    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $unique_array = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $matches = [];
                // Get all the capital letters
                if (preg_match_all("/[A-Z]+/", $line, $matches)) {
                    // Ignore the first match since that is only the first letter capital and has no use for our case
                    $original_input[] = [$matches[0][1], $matches[0][2]];
                    $unique_array[] = $matches[0][1];
                    $unique_array[] = $matches[0][2];
                }
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        $unique_array = array_unique($unique_array);
        sort($unique_array);
        $original_input[] = $unique_array;

        return $original_input;
    }
}
