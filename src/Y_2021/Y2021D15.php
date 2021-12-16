<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D15 extends Day
{
    /** https://en.wikipedia.org/wiki/Dijkstra's_algorithm */
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->calcultatePathWithLowestTotalRisk());
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

    public function calcultatePathWithLowestTotalRisk(): int
    {
        // Get our input
        $input = $this->getInputArray(true);
        // Create a matrix the same with and size as our input
        $visited_paths = [];
        $y_max = count($input);
        $x_max = count($input[0]);
        for($y = 0; $y < $y_max; $y++) {
            $visited_paths[] = array_fill(0, $x_max, null);
        }
        // Calculate the lowest risk to each node and the cheapest previous neighbor
        $this->step($input, $visited_paths, 0, 0);
        // When we are done our $visited_paths will be filled with distances
        // We can now start at the endpoint and follow the cheapest route back
        return $visited_paths[$y_max-1][$x_max-1]["total_risk"];
    }

    public function step(array $matrix, array &$visitedPoints, $point_y, $point_x): void
    {
        // Early out for points we might have already done, don't bother checking these further
        if (isset($visitedPoints[$point_y][$point_x])) {
            return;
        }

        /**
         * We have 4 directions with offsets (y,x);
         * - up (-1,0)
         * - left (0,-1)
         * - right (0,1)
         * - down (1,0)
         */

        // First check next neighbors for the cheapest and then add you own risk to their risk
        // The previous neighbors we always see as up or left
        $neighbors = [];
        foreach ([[-1,0], [0,-1], [0,1], [1,0]] as [$offset_y, $offset_x]) {
            // Don't bother with non-existing points
            if (!isset($matrix[$point_y+$offset_y][$point_x+$offset_x])) {
                continue;
            }
            $neighbors[$matrix[$point_y+$offset_y][$point_x+$offset_x]] = [
                "y" => $point_y+$offset_y,
                "x" => $point_x+$offset_x,
                "risk_val" => $matrix[$point_y+$offset_y][$point_x+$offset_x],
                "total_risk_val" => $visitedPoints[$point_y+$offset_y][$point_x+$offset_x]["total_risk"]];
        }
        if (empty($neighbors)) {
            // start
            $visitedPoints[$point_y][$point_x] = [
                "total_risk_val" => 0,
                "risk_val" => $matrix[$point_y+$offset_y][$point_x+$offset_x],
                "cheapest_neighbor_x" => null,
                "cheapest_neighbor_y" => null
            ];
        } else {
            sort($neighbors);
            $cheapest_neighbor = reset($neighbors);
            $visitedPoints[$point_y][$point_x] = [
                "total_risk_val" => $cheapest_neighbor[2] + $matrix[$point_y][$point_x],
                "risk_val" => $matrix[$point_y+$offset_y][$point_x+$offset_x],
                "cheapest_neighbor_x" => $cheapest_neighbor[1],
                "cheapest_neighbor_y" => $cheapest_neighbor[0]
            ];
        }
        foreach ([[-1,0], [0,-1], [0,1], [1,0]] as [$offset_y, $offset_x]) {
            // Don't bother with non-existing points
            if (!isset($matrix[$point_y+$offset_y][$point_x+$offset_x])) {
                continue;
            }
            $this->step($matrix, $visitedPoints, $point_y+$offset_y, $point_x+$offset_x);
        }
        return;
    }
}
