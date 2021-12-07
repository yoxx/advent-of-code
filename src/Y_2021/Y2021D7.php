<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D7 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->determineAlignmentInMinimumAmountOfSteps());
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->determineAlignmentInMinimumAmountOfSteps(true));
    }

    public function determineAlignmentInMinimumAmountOfSteps(bool $factor_in_fuel_cost = false): int
    {
        $input = $this->getInputLine(true, ",");
        // Determine cost to each grid point (watch for min/max)
        $crabCount = count($input);
        $min = (int) min($input);
        $max = (int) max($input);
        $matrix = [];
        foreach ($input as $crab_pos) {
            // Our row of positions to fill
            $row = [];
            for($i = $min; $i <= $max; $i++) {
                if ($factor_in_fuel_cost) {
                    $row[$i] = array_sum(range(0, abs($i - $crab_pos)));
                } else {
                    $row[$i] = abs($i - $crab_pos);
                }
            }
            $matrix[] = $row;
        }
        $lowest_fuel_cost = PHP_INT_MAX;
        // Get each column and array sum the columns.
        for ($column = 0; $column <= $crabCount; $column++) {
            $fuel_cost_cur_column = array_sum(array_column($matrix, $column));
            if ($fuel_cost_cur_column < $lowest_fuel_cost) {
                $lowest_fuel_cost = $fuel_cost_cur_column;
            }
        }
        // Return cheapest answer.
        return $lowest_fuel_cost;
    }
}
