<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D12 extends Day
{
    public function run(OutputInterface $logger, int $part, bool $test): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            /**
             * The plants have to match the different rules. If they maych the current plant sticks around the next generation.
             * If they do not match the plant plot will be empty.
             */

            $output = $this->runSimulation($logger, $formatted_input, 20);
            $logger->writeln("P1 The amount of plants after 20 years is " . $output);
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $output = $this->runSimulation($logger, $formatted_input, 50000000000);
            $logger->writeln("P2 The amount of plants after 50000000000 years is " . $output);
        }
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $original_input["rules"] = [];
        $handle = fopen($this->input_file, "rb");
        $count = 0;
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if($count === 0) {
                    $original_input["starting_input"] = str_split(explode(" ", trim($line))[2]);
                    $count++;
                } else {
                    $rule = explode(" ", trim($line));
                    if (\count($rule) > 1 && $rule[2] === "#") {
                        $original_input["rules"][] = $rule[0];
                    }
                }
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function runSimulation(OutputInterface $logger, array $input, int $rounds): int
    {
        $starting_plant_order = $input["starting_input"];
        $logger->writeln("0. " . $this->countPlants($starting_plant_order));
        $previous_order_string = "";
        for($round = 1; $round <= $rounds; $round++) {
            $previous_order_array = $starting_plant_order;
            // Make sure we have 2 empty pots at every end
            $this->checkArrayLimits($starting_plant_order);
            $this->applyRules($starting_plant_order, $input["rules"]);

            // Check if the orders are the same. If so this is not going to change after this state.
            // Thus we can break out of the loop and calculate what we need.
            if ($this->checkForRepetition($starting_plant_order, $previous_order_string)) {
                $logger->writeln($round . ". found repetition!");

                // If we found a repetition we should return the count but also account for the years we did not even try.
                $countedPlants = $this->countPlants($starting_plant_order);
                return $countedPlants + (($countedPlants - $this->countPlants($previous_order_array)) * ($rounds - $round));
            }
            $logger->writeln($round . ".");
        }

        return $this->countPlants($starting_plant_order);
    }

    private function applyRules(array &$input, array $rules): void
    {
        $new_crops = [];
        // Set pointer to first element and get the key
        reset($input);
        $first_key = key($input);
        end($input);
        $last_key = key($input);

        foreach ($input as $key => $pot) {
            // We know the first 2 pots should be empty thus we skip these
            if ($key === $first_key || $key === $first_key + 1 || $key === $last_key - 1 || $key === $last_key) {
                $new_crops[$key] = ".";
                continue;
            }

            $pot_string = "" . $input[$key-2] . $input[$key-1] . $input[$key] . $input[$key+1] . $input[$key+2];
            // Check if the pot applies to the rules given
            foreach ($rules as $rule) {
                if ($rule === $pot_string) {
                    $new_crops[$key] = "#";
                    break;
                }

                $new_crops[$key] = ".";
            }
        }
        $input = $new_crops;
    }

    private function checkArrayLimits(array &$input): void
    {
        // Make sure we set the pointer to the start of the array
        reset($input);

        // We will check if the first 2 of the array are empty pots, if so let it be
        $res = array_slice($input, 0, 4, true);
        reset($res);
        $key = key($res);
        if ($input[$key] !== ".") {
            $input[$key-1] = ".";
            $input[$key-2] = ".";
            $input[$key-3] = ".";
            $input[$key-4] = ".";
        } elseif ($input[$key+1] !== ".") {
            $input[$key-1] = ".";
            $input[$key-2] = ".";
            $input[$key-3] = ".";
        } elseif ($input[$key+2] !== ".") {
            $input[$key-1] = ".";
            $input[$key-2] = ".";
        } elseif ($input[$key+3] !== ".") {
            $input[$key-1] = ".";
        }

        // Make sure the array is sorted by keys
        ksort($input);

        // Check the last 2 of the array
        $res = array_slice($input, \count($input)-4, 4, true);
        end($res);
        $key = key($res);
        if ($input[$key] !== ".") {
            $input[$key+1] = ".";
            $input[$key+2] = ".";
            $input[$key+3] = ".";
            $input[$key+4] = ".";
        } elseif ($input[$key-1] !== ".") {
            $input[$key+1] = ".";
            $input[$key+2] = ".";
            $input[$key+3] = ".";
        } elseif ($input[$key-2] !== ".") {
            $input[$key+1] = ".";
            $input[$key+2] = ".";
        } elseif ($input[$key-3] !== ".") {
            $input[$key+1] = ".";
        }
    }

    private function countPlants(array $input): int
    {
        $total_amount_of_plants = 0;

        foreach ($input as $key => $pot) {
            if ($pot === "#") {
                $total_amount_of_plants += $key;
            }
        }

        return $total_amount_of_plants;
    }

    private function checkForRepetition(array $current, string &$previous): bool
    {
        // Find the first plant in the array
        $first_plant = array_search("#", $current, true);

        $plants_order = substr(implode("", $current), $first_plant);
        if ($plants_order && $previous === $plants_order) {
            return true;
        }

        $previous = $plants_order;
        return false;
    }
}
