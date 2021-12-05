<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D5 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->determineAmountOfOverlaps($this->filterDiagonalLinesFromInput($this->parseInput())));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->determineAmountOfOverlaps($this->parseInput()));
    }

    public function determineAmountOfOverlaps(array $filtered_input): int
    {
        $matrix_of_line_points = [];
        $overlapping_points = 0;
        foreach ($filtered_input as $cord_array) {
            $matrix_of_line_points = $this->plotLine($matrix_of_line_points, $cord_array[0], $cord_array[1]);
        }
        foreach ($matrix_of_line_points as $line) {
            foreach($line as $point) {
                if ($point > 1) {
                    $overlapping_points++;
                }
            }
        }

        return $overlapping_points;
    }

    public function plotLine(array $matrix_of_line_points, array $cord1, array $cord2): array
    {
        if($cord1[0] === $cord2[0]) {
            // Vertical line
            if ($cord1[1] < $cord2[1]) {
                for($y = $cord1[1]; $y <= $cord2[1]; $y++) {
                    if (!isset($matrix_of_line_points[$y][$cord1[0]])) {
                        $matrix_of_line_points[$y][$cord1[0]] = 1;
                    } else {
                        $matrix_of_line_points[$y][$cord1[0]]++;
                    }
                }
            } else {
                for($y = $cord1[1]; $y >= $cord2[1]; $y--) {
                    if (!isset($matrix_of_line_points[$y][$cord1[0]])) {
                        $matrix_of_line_points[$y][$cord1[0]] = 1;
                    } else {
                        $matrix_of_line_points[$y][$cord1[0]]++;
                    }
                }
            }
        } else if ($cord1[1] === $cord2[1]) {
            // Horizontal line
            if ($cord1[0] < $cord2[0]) {
                for($x = $cord1[0]; $x <= $cord2[0]; $x++) {
                    if (!isset($matrix_of_line_points[$cord1[1]][$x])) {
                        $matrix_of_line_points[$cord1[1]][$x] = 1;
                    } else {
                        $matrix_of_line_points[$cord1[1]][$x]++;
                    }
                }
            } else {
                for($x = $cord1[0]; $x >= $cord2[0]; $x--) {
                    if (!isset($matrix_of_line_points[$cord1[1]][$x])) {
                        $matrix_of_line_points[$cord1[1]][$x] = 1;
                    } else {
                        $matrix_of_line_points[$cord1[1]][$x]++;
                    }
                }
            }
        } else {
            $x = $cord1[0];
            $y = $cord1[1];
            // Diagonal
            if ($cord1[1] < $cord2[1]) {
                // y++
                if ($cord1[0] < $cord2[0]) {
                    // x++
                    $line_length = $cord2[0] - $cord1[0];
                    for($i = 0; $i <= $line_length; $i++) {
                        if (!isset($matrix_of_line_points[$y][$x])) {
                            $matrix_of_line_points[$y][$x] = 1;
                        } else {
                            $matrix_of_line_points[$y][$x]++;
                        }
                        $x++;
                        $y++;
                    }
                } else {
                    // x--
                    $line_length = $cord1[0] - $cord2[0];
                    for($i = 0; $i <= $line_length; $i++) {
                        if (!isset($matrix_of_line_points[$y][$x])) {
                            $matrix_of_line_points[$y][$x] = 1;
                        } else {
                            $matrix_of_line_points[$y][$x]++;
                        }
                        $x--;
                        $y++;
                    }
                }
            } else {
                // y--
                if ($cord1[0] < $cord2[0]) {
                    // x++
                    $line_length = $cord2[0] - $cord1[0];
                    for($i = 0; $i <= $line_length; $i++) {
                        if (!isset($matrix_of_line_points[$y][$x])) {
                            $matrix_of_line_points[$y][$x] = 1;
                        } else {
                            $matrix_of_line_points[$y][$x]++;
                        }
                        $x++;
                        $y--;
                    }
                } else {
                    // x--
                    $line_length = $cord1[0] - $cord2[0];
                    for($i = 0; $i <= $line_length; $i++) {
                        if (!isset($matrix_of_line_points[$y][$x])) {
                            $matrix_of_line_points[$y][$x] = 1;
                        } else {
                            $matrix_of_line_points[$y][$x]++;
                        }
                        $x--;
                        $y--;
                    }
                }
            }
        }

        return $matrix_of_line_points;
    }

    public function filterDiagonalLinesFromInput(array $input): array
    {
        $filtered_input = array_filter($input, static function ($cord_array) {
            return $cord_array[0][0] === $cord_array[1][0] || $cord_array[0][1] === $cord_array[1][1];
        });
        return $filtered_input;
    }

    public function parseInput(): array
    {
        $input = $this->getInputArray(true, " -> ");
        $output = array_map(static function ($cord_array) {
            return [array_map("intval", explode(",", trim($cord_array[0]))), array_map("intval", explode(",", trim($cord_array[1])))];
        }, $input);
        return $output;
    }
}
