<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D6 extends Day
{
    public function run(OutputInterface $logger, int $part, bool $test): void
    {
        $field = [];
        // First we read the array coordinates to an array [[x,y],[x,y]]
        $formatted_input =$this->getFormattedInput($logger);
        // We will be creating an array field filled to do this we first determinate the outer boundries which are the largest X & Y
        $minmax_xy = array_pop($formatted_input);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // Next we will plot the formatted input on our field array
            $finite_array = $this->plotValuesForLargest($field, $formatted_input, $minmax_xy);
            // Calculate the largest area
            $largest_area = $this->calcLargestFieldNonInfinte($field, $finite_array);
            $logger->writeln("The largest area is: " . $largest_area);
        } elseif ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL){
            $area_under_10000_steps = $this->plotValuesForClosest($formatted_input, $minmax_xy);
            $logger->writeln("Amount of area points that can reach all positions under 10000 steps: " . $area_under_10000_steps);
        }
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $min_x = PHP_INT_MAX;
        $max_x = PHP_INT_MIN;
        $min_y = PHP_INT_MAX;
        $max_y = PHP_INT_MIN;
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $input = explode(",",$line);
                $original_input[] = [(int) $input[0], (int) $input[1]];
                // Check our min and max X values
                if ($input[0] > $max_x) {
                    $max_x = (int) $input[0];
                }
                if ($input[0] < $min_x){
                    $min_x = (int) $input[0];
                }
                // Check our min and max Y values
                if ($input[1] > $max_y) {
                    $max_y = (int) $input[1];
                }
                if ($input[1] < $min_y){
                    $min_y = (int) $input[1];
                }
            }
            $original_input[] = [[$min_x, $max_x], [$min_y, $max_y]];
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function calcLargestFieldNonInfinte(array $field, array $finite_index): int
    {
        $largest_area = 0;
        foreach ($finite_index as $key => $index) {
            $sum = 0;
            foreach ($field as $x_row) {
                $sum += \count(array_keys($x_row, $key));
            }

            if ($sum > $largest_area) {
                $largest_area = $sum;
            }
        }

        return $largest_area;
    }

    /**
     * Plot the values on the field and return an array of indexes of original coordinates that cannot be infinte
     * The positions that have locations at the edge of our field must be considered infinite
     */
    private function plotValuesForLargest(array &$field, array $values_to_plot, array $minmax_xy): array
    {
        $output = array_values($values_to_plot);
        // Once we start looking through the field we need to calculate to which coordinate its the closest
        // First we fill the field
        for ($y = $minmax_xy[1][0]; $y <= $minmax_xy[1][1]; $y++) {
            $field[$y] = [];
            for ($x = $minmax_xy[0][0]; $x <= $minmax_xy[0][1]; $x++) {
                if ($x === $minmax_xy[0][0] || $y === $minmax_xy[1][0] || $x === $minmax_xy[0][1] || $y === $minmax_xy[1][1]) {
                    $position_that_must_be_infinite = $this->calculateClosestPoint([$x, $y], $values_to_plot);
                    // Safe the position
                    $field[$y][$x] = $position_that_must_be_infinite;
                    // Remove the infinite coordinate from our possible solutions
                    unset($output[$position_that_must_be_infinite]);
                } else {
                    // Calculate the closest point
                    $field[$y][$x] = $this->calculateClosestPoint([$x, $y], $values_to_plot);
                }
            }
        }

        // Return our possible finite fields
        return $output;
    }

    /**
     * Simply calculate the amount of spots that reach all points within a distance of 10000
     */
    private function plotValuesForClosest(array $values_to_plot, array $minmax_xy): int
    {
        $spots_under_10000 = 0;
        // Once we start looking through the field we need to calculate to which coordinate its the closest
        // First we fill the field
        for ($y = $minmax_xy[1][0]; $y <= $minmax_xy[1][1]; $y++) {
            for ($x = $minmax_xy[0][0]; $x <= $minmax_xy[0][1]; $x++) {
                $total_distance = $this->calculateDistancetoAllPoints([$x, $y], $values_to_plot);
                if ($total_distance < 10000) {
                    $spots_under_10000++;
                }
            }
        }
        return $spots_under_10000;
    }

    /**
     * Returns the index of the closest coordinate or -1 for a tied position
     */
    private function calculateClosestPoint(array $current_cor, array $values_to_plot): int
    {
        // Lets calculate all the distances to the other values
        $cor_length = \count($values_to_plot);
        $distances = [];
        for ($count = 0; $count < $cor_length; $count++) {
            if ($current_cor[0] === $values_to_plot[$count][0] && $current_cor[1] === $values_to_plot[$count][1]) {
                // This is a main position
                $distances[$count] = 0;
            } else {
                $distances[$count] = $this->calcManhattanDistance($current_cor, $values_to_plot[$count]);
            }
        }

        $keys = array_keys($distances, min($distances));
        return (\count($keys) > 1)? -1 : $keys[0];
    }

    /**
     * Returns the index of the closest coordinate or -1 for a tied position
     */
    private function calculateDistancetoAllPoints(array $current_cor, array $values_to_plot): int
    {
        // Lets calculate all the distances to the other values
        $cor_length = \count($values_to_plot);
        $distances = [];
        for ($count = 0; $count < $cor_length; $count++) {
            if ($current_cor[0] === $values_to_plot[$count][0] && $current_cor[1] === $values_to_plot[$count][1]) {
                // This is a main position
                $distances[$count] = 0;
            } else {
                $distances[$count] = $this->calcManhattanDistance($current_cor, $values_to_plot[$count]);
            }
        }

        return array_sum($distances);
    }

    /**
     * Calcuate the distance between the coordinates
     */
    private function calcManhattanDistance(array $cor1, array $cor2): int
    {
        return abs($cor1[0] - $cor2[0]) + abs($cor1[1] - $cor2[1]);
    }
}
