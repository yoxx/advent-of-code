<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D9 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->part1());
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->part2());
    }

    public function part1(): int
    {
        return array_sum($this->getLowestPoints()["lowest_points"]) + count($this->getLowestPoints()["lowest_points"]);
    }

    public function part2(): int
    {
        $basins = $this->findBasinSizes();
        rsort($basins);
        $three_highest_values = array_slice($basins, 0, 3);
        return $three_highest_values[0] * $three_highest_values[1] * $three_highest_values[2];
    }

    public function getLowestPoints(): array
    {
        $input = $this->getInputArray(true);
        $metadata = [];
        $metadata["lowest_points"] = [];
        $metadata["lowest_points_with_cords"] = [];
        foreach ($input as $r_key => $row) {
            $input[$r_key] = array_map("intval", $row);
        }
        $metadata["matrix"] = $input;
        foreach ($input as $r_key => $row) {
            foreach ($row as $i_key => $item) {
                // down / up / left / right
                $item_with_surroundings = [$item, $input[$r_key - 1][$i_key] ?? 9, $input[$r_key + 1][$i_key] ?? 9, $row[$i_key - 1] ?? 9, $row[$i_key + 1] ?? 9];
                if ($item !== 9 && $item === min($item_with_surroundings)) {
                    $metadata["lowest_points"][] = $item;
                    $metadata["lowest_points_with_cords"][] = [$item, $i_key, $r_key];
                }
            }
        }
        return $metadata;
    }

    public function findBasinSizes(): array
    {
        $checked_points = [];
        $basins = [];
        $metadata = $this->getLowestPoints();
        // foreach lowest point count up to a border
        foreach($metadata["lowest_points_with_cords"] as $lowest_point) {
            $basins[] = $this->findStepsToBorder($lowest_point[1], $lowest_point[2], $checked_points, $metadata["matrix"]);
        }

        return $basins;
    }

    public function findStepsToBorder(int $point_x, int $point_y, array &$checked_points, array $matrix): int
    {
        $checked_points[] = $point_x . "," .$point_y;
        // We start with our current item which counts as ATLEAST 1 step;
        $steps = 1;
        // foreach point you are checking count up to a border recursion
        // Check if we have a border in a certain direction. If NOT do a new findSteps for the cords in that direction.
        // We have 4 directions; up/down/left/right
        // UP
        if (isset($matrix[$point_y-1][$point_x]) && $matrix[$point_y-1][$point_x] !== 9 && !in_array($point_x . "," . ($point_y-1), $checked_points, true)) {
            $steps += $this->findStepsToBorder($point_x, $point_y-1, $checked_points, $matrix);
        }
        // DOWN
        if (isset($matrix[$point_y+1][$point_x]) && $matrix[$point_y+1][$point_x] !== 9 && !in_array($point_x . "," . ($point_y+1), $checked_points, true)) {
            $steps += $this->findStepsToBorder($point_x, $point_y+1, $checked_points, $matrix);
        }
        // LEFT
        if (isset($matrix[$point_y][$point_x-1]) && $matrix[$point_y][$point_x-1] !== 9 && !in_array(($point_x-1) . "," . $point_y, $checked_points, true)) {
            $steps += $this->findStepsToBorder($point_x-1, $point_y, $checked_points, $matrix);
        }
        // RIGHT
        if (isset($matrix[$point_y][$point_x+1]) && $matrix[$point_y][$point_x+1] !== 9 && !in_array(($point_x+1) . "," . $point_y, $checked_points, true)) {
            $steps += $this->findStepsToBorder($point_x+1, $point_y, $checked_points, $matrix);
        }

        return $steps;
    }
}
