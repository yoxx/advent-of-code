<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D10 extends Day
{
    protected $height = 0;
    protected $last_round = [];

    public function run(OutputInterface $logger, int $part): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $this->height = $this->determineHeight($formatted_input);

            $done = false;
            $count = 0;
            while (!$done) {
                $height = $this->determineHeight($formatted_input);

                if ($height > $this->height) {
                    // If the height get bigger the previous iteration should be the right one
                    $done = true;
                } else {
                    $this->last_round = $formatted_input;
                    $this->updatePoints($formatted_input);
                    $this->height = $height;
                    $count++;
                }
            }

            $grid = $this->plotInGrid($this->last_round);
            $this->outputGrid($logger, $grid);

            $logger->writeln("After " . ($count-1) . " seconds the text should be:");
        }
    }

    /**
     * Parse our input string to an array as follows
     * input: position=< 9,  1> velocity=< 0,  2>
     * output:
     * [
     *  [
     *      "position" => [x,y], // the current position
     *      "velocity" => [x,y], // the way it is moving (we add this to the x,y of the position)
     *  ]
     * ]
     *
     * @param OutputInterface $logger
     *
     * @return array
     */
    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                preg_match("/^position=<\s*(-?\d+)\s*,\s*(-?\d+)\s*> velocity=<\s*(-?\d+)\s*,\s*(-?\d+)\s*>$/", $line, $match);
                // Create a single light point
                $light = [
                    "position" => [(int) $match[1], (int) $match[2]],
                    "velocity" => [(int) $match[3], (int) $match[4]]
                ];

                // Add to the global input
                $original_input[] = $light;
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    /**
     * Update the points using the velocity
     *
     * @param array $points
     */
    private function updatePoints(array &$points): void
    {
        $arr_length = \count($points);
        for ($count = 0; $count < $arr_length; $count++) {
            $points[$count]["position"][0] += $points[$count]["velocity"][0];
            $points[$count]["position"][1] += $points[$count]["velocity"][1];
        }
    }

    /**
     * Determine the max height and max width of the grid
     */
    private function determineMaxes(array $points): array
    {
        $min_x = 100000;
        $min_y = 100000;
        $max_x = 0;
        $max_y = 0;

        foreach ($points as $point) {
            if ($point["position"][0] < $min_x) {
                $min_x = $point["position"][0];
            }

            if ($point["position"][0] > $max_x) {
                $max_x = $point["position"][0];
            }

            if ($point["position"][1] < $min_y) {
                $min_y = $point["position"][1];
            }

            if ($point["position"][1] > $max_y) {
                $max_y = $point["position"][1];
            }
        }

        return [
            "min_x" => $min_x,
            "min_y" => $min_y,
            "max_x" => $max_x,
            "max_y" => $max_y
        ];

    }

    /**
     * If the height of the grid is
     */
    private function determineHeight(array $points): int
    {
        $min_y = 10000;
        $max_y = 0;

        foreach ($points as $point) {
            if ($point["position"][1] < $min_y) {
                $min_y = $point["position"][1];
            }

            if ($point["position"][1] > $max_y) {
                $max_y = $point["position"][1];
            }
        }

        return $max_y - $min_y;
    }

    /**
     * Plot the points in a grid
     */
    private function plotInGrid(array $points): array
    {
        $maxes = $this->determineMaxes($points);

        // Fill the grid
        $grid = [];
        // Run the Y rows
        for ($count_y = $maxes["min_y"]-1; $count_y <= $maxes["max_y"]+1; $count_y++) {
            $grid[$count_y] = [];
            // Run the X rows
            for ($count_x = $maxes["min_x"]-1; $count_x <= $maxes["max_x"]+1; $count_x++) {
                $grid[$count_y][$count_x] = ".";
            }
        }

        foreach ($points as $point) {
            $grid[$point["position"][1]][$point["position"][0]] = "#";
        }

        return $grid;
    }

    /**
     * Output the grid to the console
     */
    private function outputGrid(OutputInterface $logger, array $grid): void
    {
        foreach ($grid as $row) {
            foreach ($row as $point) {
                $logger->write($point);
            }
            $logger->write("\n");
        }
    }
}