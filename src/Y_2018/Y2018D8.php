<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D8 extends Day
{
    public function run(OutputInterface $logger, int $part, bool $test): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // First we gather all the nodes
            $nodes = $this->findInnerNodes($formatted_input);
            // Then we add up all the metadata entries
            $sum = $this->calcSumMetadata($nodes);
            $logger->writeln("The sum of the metadata: " . $sum);
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $root = $this->readNode($formatted_input,0);
            $logger->writeln("Root value: " . $root["metadata_value"]);
        }
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

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
                    $node["metadata"] = \array_slice($full_input,$index + 2, $node["header"][1]);

                    for ($nindex = $index; $nindex <= ($index + 1 + $amount_metadata); $nindex++) {
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

    /**
     * Read the nodes (again) this time from start to end...
     * Return the root node and his children and the metadata_value...
     */
    private function readNode(array $original_input, int $offset): array
    {
        // Get the header info
        $node["header"] = [(int) $original_input[$offset], (int) $original_input[$offset + 1]];

        // start size of 2 since we did the header
        $size = 2;

        // Time to look for the children
        $children=[];
        for($count = 0;$count < $node["header"][0]; $count++){
            $children[$count] = $this->readNode($original_input, $offset + $size);
            $size += $children[$count]['size'];
        }

        // Fill node with info
        $node['size'] = $size + $node["header"][1];
        $node['index'] = $offset;
        $node['children'] = $children;
        $node["metadata"] = \array_slice($original_input,$offset + $size, $node["header"][1]);

        // Check for children and calculate the metadatavalue for this
        if(\count($children) === 0){
            $node['metadata_value'] = \array_sum($node["metadata"]);
        }else{
            $value_from_children = 0;
            foreach($node["metadata"] as $meta){
                // The metadata value is the index of the child. However it starts counting at 1 instead of 0
                $index = (int) $meta - 1;

                // If the index is larger than 0 and makes sense (aka the child array has a key for that)
                if($index >= 0 && \array_key_exists($index, $children)) {
                    $value_from_children += $children[$index]['metadata_value'];
                }
            }
            // Save the values
            $node['metadata_value'] = $value_from_children;
        }

        // return the node
        return $node;
    }

    /**
     * Calculate the total sum of metadata for the nodes
     */
    private function calcSumMetadata($nodes): int
    {
        $total_sum = 0;
        foreach($nodes as $node) {
            $total_sum += array_sum($node["metadata"]);
        }
        return $total_sum;
    }
}
