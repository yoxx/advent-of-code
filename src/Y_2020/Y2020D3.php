<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Y_2019\IntCodeComputer;

class Y2020D3 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $trees = $this->checkSlope($this->getInputLine(), 3, 1);
        $output->writeln("P1: amount of trees: " . $trees);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $t1 = $this->checkSlope($this->getInputLine(), 1, 1);
        $t2 = $this->checkSlope($this->getInputLine(), 3, 1);
        $t3 = $this->checkSlope($this->getInputLine(), 5, 1);
        $t4 = $this->checkSlope($this->getInputLine(), 7, 1);
        $t5 = $this->checkSlope($this->getInputLine(), 1, 2);

        $tree_total = $t1 * $t2 * $t3 * $t4 * $t5;

        $output->writeln("P1: amount of trees: " . $tree_total);
    }

    private function checkSlope(array $input, int $right, int $down): int
    {
        $grid = [];
        foreach ($input as $line) {
            $grid[] = str_split(trim($line));
        }

        $y_max = count($grid);
        $x_max = count($grid[0]);
        $x_pos = 0;
        $y_pos = 0;

        $trees = 0;
        for ($line_count = 0; $line_count < $y_max; $line_count++) {
            if (isset($input[$y_pos][$x_pos % $x_max]) && $input[$y_pos][$x_pos % $x_max] === "#") {
                $trees++;
            }
            $x_pos += $right;
            $y_pos += $down;
        }

        return $trees;
    }
}
