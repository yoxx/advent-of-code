<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2018D3 extends Day
{
    public function run(OutputInterface $output, int $part, bool $test): void
    {
        $all_input = [];
        $virtual_fabric = [];

        /**
         * Read puzzle input
         */
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $parsed_input = $this->parseInputToArray($line);
                $all_input[] = $parsed_input;
                $this->addInputToFabric($virtual_fabric, $parsed_input);
            }

            $output->writeln("The amount of overlapping inches is: " . $this->calculateOverlap($virtual_fabric));
            // Close the file
            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }

        /**
         * Part 2 check for a free plot
         */
        $output->writeln("The only non overlapping ID is: " . $this->findFreeClaim($all_input, $virtual_fabric));

    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    /** First we need to parse the input into viable numbers
     * Input like #1 @ 236,827: 24x17
     * Should perhaps be [
     * "id" => "#1",
     * "dist_to_left" => 236,
     * "dist_to_top" => 827,
     * "width" => 24,
     * "height" => 17
     * ]
     */
    private function parseInputToArray(string $input): array
    {
        $output = [];

        // Parse the id
        $tmp = explode("@", $input);
        $output["id"] = $tmp[0];

        $tmp = $tmp[1];
        $tmp = explode(":", $tmp);

        // Parse the dist to the left & top
        $tmp_margin = explode(",", $tmp[0]);
        $output["dist_to_left"] = (int) $tmp_margin[0];
        $output["dist_to_top"] = (int) $tmp_margin[1];

        // Parse the size of the input
        $tmp_size = explode("x", $tmp[1]);
        $output["width"] = (int) $tmp_size[0];
        $output["height"] = (int) $tmp_size[1];

        return $output;
    }

    /**
     * Now we need to compare these numbers to the original square that is (atleast) 1000 by 1000
     * We need to find out how many inches overlap...
     *
     * To do this we create a virtual fabric.
     * Using the input from above we create an array and only toggle things that are set to 1
     * Then for the next line if it was set change up the number
     * [
     *  827 => [236 => 1], // line from top 827 dist from the left 236 etc...
     * ]
     */
    private function addInputToFabric(array &$virtual_fabric, array $input): void
    {
        // We start at the margin points and start with the top input
        for ($vertical = $input["dist_to_top"]; $vertical < ($input["dist_to_top"] + $input["height"]); $vertical++) {
            for ($horizontal = $input["dist_to_left"]; $horizontal < ($input["dist_to_left"] + $input["width"]); $horizontal++) {
                if (!isset($virtual_fabric[$vertical])) {
                    // Set a vertical line if there is no such line just now
                    $virtual_fabric[$vertical] = [];
                }

                if (isset($virtual_fabric[$vertical][$horizontal])) {
                    // The square inch is already taken simply up the taken count
                    $virtual_fabric[$vertical][$horizontal] += 1;
                } else {
                    // First time we set this square inch
                    $virtual_fabric[$vertical][$horizontal] = 1;
                }
            }
        }
    }

    /**
     * Calculate the overlapping square inches
     */
    private function calculateOverlap(array $virtual_fabric): int
    {
        // The var where we save a plot that is wanted twice
        $double_dibs = 0;

        // We start at the margin points and start with the top input
        foreach ($virtual_fabric as $key_vertical => $vertical) {
            foreach ($vertical as $key_horizontal => $horizontal) {
                if ($horizontal > 1) {
                    $double_dibs++;
                }
            }
        }

        return $double_dibs;
    }

    /**
     * Find the one claim that has no overlapping and return its ID
     */
    private function findFreeClaim(array $all_input, array $virtual_fabric): string
    {
        foreach ($all_input as $input) {
            $overlap = 0;

            // We start at the margin points and start with the top input
            for ($vertical = $input["dist_to_top"]; $vertical < ($input["dist_to_top"] + $input["height"]); $vertical++) {
                for ($horizontal = $input["dist_to_left"]; $horizontal < ($input["dist_to_left"] + $input["width"]); $horizontal++) {
                    if (isset($virtual_fabric[$vertical][$horizontal]) && $virtual_fabric[$vertical][$horizontal] > 1) {
                        // The square inch is already taken simply up the taken count
                        $overlap += 1;
                    }
                }
            }

            if ($overlap === 0) {
                return $input["id"];
            }
        }
    }
}
