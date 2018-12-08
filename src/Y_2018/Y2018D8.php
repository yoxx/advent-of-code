<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D8 extends Day
{
    public function run(OutputInterface $logger, int $part): void
    {
        $formatted_input = $this->getFormattedInput($logger);
        // First we gather all the nodes
        $nodes = $this->findInnerNodes($formatted_input);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // Then we add up all the metadata entries
            $sum = $this->calcSumMetadata($nodes);
            $logger->writeln("The sum of the metadata: " . $sum);
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $node_with_headers = $this->findHeaderAndChildren($nodes, $formatted_input);
//            $logger->writeln("Amount of area points that can reach all positions under 10000 steps: " . $area_under_10000_steps);
        }
    }

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $original_input = explode(" ", $line);
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function findInnerNodes(array $full_input): array
    {
        $output = [];
        /**
         * We know a header consists of 2 numbers 1 being the amount fo nodes and 2 being the amount of metadata said node has
         * So lets go down the rabbit hole and try to find each branch
         *
         * Data will be formatted in the form of an array
         * Each entry will contain a node
         * [
         *  "id" => [
         *          "parent_id" => int
         *          "full_node_string" => ""
         *          "children" => int,
         *          "metadata" => []
         *      ]
         * ]
         *
         * We start by looking for the most inner node we can find and work our way up
         * The most inner node is the node without any child nodes that string we simply remove and put in the array
         */
        $done = false;
        while(!$done) {
            $length = \count($full_input);
            for ($index = 0; $index < $length; $index+=2) {
                // found the most inner node
                if ((int) $full_input[$index] === 0) {
                    $amount_metadata = (int) $full_input[$index + 1];
                    $node["node_string_length"] = 2 + $amount_metadata;
                    $node["header"] = [$full_input[$index],$full_input[$index + 1]];
                    $node["metadata"] = [];

                    for ($nindex = $index; $nindex <= ($index + 1 + $amount_metadata); $nindex++) {
                        // Grab the metadata
                        if ($nindex > $index+1) {
                            $node["metadata"][] = (int) $full_input[$nindex];
                        }

                        // Unset this from the array
                        unset($full_input[$nindex]);
                    }

                    // Change the header before this to show we have taken away a child node but only if this is not the last node!
                    if ($index !== 0) {
                        $node["parent_header"] = [$full_input[$index - 2] , $full_input[$index - 1]];
                        $full_input[$index - 2] = (int) $full_input[$index - 2] - 1;
                    } else {
                        $node["parent_header"] = [];
                    }

                    // Save the node to our output
                    $output[] = $node;
                    break;
                }
            }

            // Reset the array
            $full_input = array_values($full_input);

            if(\count($full_input) === 0) {
                $done = true;
            }
        }

        return $output;
    }

    private function findHeaderAndChildren(array $nodes, array $original_input): array
    {
        /**
         * We already know all the nodes in this array... however we need to find the correct link
         */
        $output = [];

        $done = false;
        while(!$done) {
            $length = \count($original_input);
            for ($index = 0; $index < $length; $index += 2) {
                // found the most inner node
                if ((int) $original_input[$index] === 0) {
                    $amount_metadata = (int) $original_input[$index + 1];
                    $node["node_string_length"] = 2 + $amount_metadata;
                    $node["header"] = [$original_input[$index], $original_input[$index + 1]];
                    $node["metadata"] = [];

                    for ($nindex = $index; $nindex <= ($index + 1 + $amount_metadata); $nindex++) {
                        // Grab the metadata
                        if ($nindex > $index + 1) {
                            $node["metadata"][] = (int) $original_input[$nindex];
                        }

                        // Unset this from the array
                        unset($original_input[$nindex]);
                    }

                    // Change the header before this to show we have taken away a child node but only if this is not the last node!
                    if ($index !== 0) {
                        $node["parent_header"] = [$original_input[$index - 2], $original_input[$index - 1]];
                        $full_input[$index - 2] = (int) $original_input[$index - 2] - 1;
                    } else {
                        $node["parent_header"] = [];
                    }

                    // Save the node to our output
                    $output[] = $node;
                    break;
                }
            }

            // Reset the array
            $full_input = array_values($full_input);

            if (\count($full_input) === 0) {
                $done = true;
            }
        }
    }

    private function calcSumMetadata($nodes): int
    {
        $total_sum = 0;
        foreach($nodes as $node) {
            $total_sum += array_sum($node["metadata"]);
        }
        return $total_sum;
    }
}