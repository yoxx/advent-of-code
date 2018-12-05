<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D5 extends Day
{
    public function run(OutputInterface $logger, int $part): void
    {
        // Part 1
        // We get 1 polymer devided in several lines.
        // Input like aA && bB etc will dissolve itself. However aa && aB or ab wil not.
        // First we create a large character array from all of the lines.
        $logger->writeln("Dissolving chars this may take a while");
        $character_array = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // If we find a match we dissolve the two units and start from the begginning.
            $remaining_count = $this->dissolve($logger, $character_array);

            $logger->writeln("Remaining polymer length: " . $remaining_count);
        } elseif ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // Part 2
            $unique_char_values = array_unique(array_map('strtolower', array_unique($character_array)));

            $character_array_length = \count($character_array);

            $lowest = null;
            $lowest_char = null;
            foreach ($unique_char_values as $unique_char) {
                $logger->writeln("Checking array without: " . $unique_char);

                $params = [
                    "object" => $this,
                    "logger" => $logger,
                    "character_array" => $character_array,
                    "character_array_length" => $character_array_length,
                    "unique_char" => $unique_char,
                ];

                // Todo make sure we update to PHP7.2
//                Utils::runAsyncOperation($logger, $this, "checkArrayWithoutChar", $params);

                $this->checkArrayWithoutChar($params);
                if ($lowest === null) {
                    $lowest = $poly_length;
                    $lowest_char = $unique_char;
                } elseif ($lowest > $poly_length) {
                    $lowest = $poly_length;
                    $lowest_char = $unique_char;
                }
            }

            $logger->writeln("Lowest polymer length: " . $lowest . " without char: " . $lowest_char);
        }
    }

    public function checkArrayWithoutChar(array $params): void
    {
        // Copy current array
        $array_without_char = array_values($params["character_array"]);
        for ($count = 0; $count < $params["character_array_length"]; $count++) {
            if (strtolower($array_without_char[$count]) === $params["unique_char"]) {
                unset($array_without_char[$count]);
            }
        }
        $array_without_char = array_values($array_without_char);

        $poly_length = $params["object"]->dissolve($params["logger"], $array_without_char);
        $params["logger"]->writeln("Char: " . $params["unique_char"] . " Poly_length: " . $poly_length);
    }

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $original_input = array_merge($original_input, str_split(trim($line)));
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function dissolve(OutputInterface $logger, array $char_array): int
    {
        $arr_length = \count($char_array);
        $count = 0;
        while ($count < $arr_length-1) {
            $unit1 = $char_array[$count];
            $unit2 = $char_array[$count+1];

            // Check if we have a upper and a lower char otherwise skip
            if (($this->checkLower($unit1) && $this->checkUpper($unit2)) || ($this->checkUpper($unit1) && $this->checkLower($unit2))) {
                // Check if the char is the same by making them both lowercase
                if (strtolower($unit1) === strtolower($unit2)) {
                    // Remove the two from the array and reindex the array
                    unset($char_array[$count],$char_array[$count+1]);
                    $char_array = array_values($char_array);
                    // Recount the length
                    $arr_length = \count($char_array);
                    $logger->writeln("Arr_lenght: " . $arr_length, OutputInterface::VERBOSITY_VERBOSE);
                    // Reset the for-loop
                    $count = 0;
                }
            }

            $count++;
        }

        return \count($char_array);
    }

    private function checkUpper($char): bool
    {
        return strtolower($char) !== $char;
    }

    private function checkLower($char): bool
    {
        return strtolower($char) === $char;
    }
}