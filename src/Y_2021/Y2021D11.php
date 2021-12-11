<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D11 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $matrix = $this->getInputArray(true);
        foreach ($matrix as $r_key => $row) {
            $matrix[$r_key] = array_map("intval", $row);
        }

        $flash_count = 0;
        for ($steps = 0; $steps < 100; $steps++) {
            $flashed_points = [];
            $all_initial_larger_than_9 = [];
            // First all jelly's have their energy increase by 1
            foreach ($matrix as $r_key => $row) {
                $matrix[$r_key] = array_map(static function($jelly) {
                    return $jelly+1;
                }, $row);
                // Gather only the ones that are currently above 9
                $all_initial_larger_than_9[] = array_filter($matrix[$r_key], static function($jelly) {
                    return $jelly > 9;
                });
            }
            // Then all jelly with an energy level of > 9 flash (they can flash only once)
            foreach ($all_initial_larger_than_9 as $y => $row) {
                foreach ($row as $x => $jelly) {
                    $flash_count += $this->flash($matrix, $flashed_points, $y, $x);
                }
            }
            // Set all greater than 9 to 0
            foreach ($matrix as $r_key => $row) {
                $matrix[$r_key] = array_map(static function($jelly) {
                    return ($jelly > 9) ? 0: $jelly;
                }, $row);
            }
        }
        $output->writeln("P1: The solution is: " . $flash_count);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $matrix = $this->getInputArray(true);
        foreach ($matrix as $r_key => $row) {
            $matrix[$r_key] = array_map("intval", $row);
        }
        $synced = false;
        $steps = 0;
        while (!$synced) {
            $flashed_points = [];
            $all_initial_larger_than_9 = [];
            // First all jelly's have their energy increase by 1
            foreach ($matrix as $r_key => $row) {
                $matrix[$r_key] = array_map(static function ($jelly) {
                    return $jelly + 1;
                }, $row);
                // Gather only the ones that are currently above 9
                $all_initial_larger_than_9[] = array_filter($matrix[$r_key], static function ($jelly) {
                    return $jelly > 9;
                });
            }
            // Then all jelly with an energy level of > 9 flash (they can flash only once)
            foreach ($all_initial_larger_than_9 as $y => $row) {
                foreach ($row as $x => $jelly) {
                    $this->flash($matrix, $flashed_points, $y, $x);
                }
            }
            // Set all greater than 9 to 0
            foreach ($matrix as $r_key => $row) {
                $matrix[$r_key] = array_map(static function ($jelly) {
                    return ($jelly > 9) ? 0 : $jelly;
                }, $row);
            }
            if (count($flashed_points) === 10*10) {
                $synced = true;
            }
            $steps++;
        }

        $output->writeln("P2: The solution is: " . $steps);
    }

    public function flash(array &$matrix, array &$flashed_points, $point_y, $point_x): int
    {
        // Early out for points we might have already done, don't bother checking these further
        if (in_array($point_y . "," . $point_x, $flashed_points, true)) {
            return 0;
        }
        $flashed_points[] = $point_y . "," . $point_x;
        // We start with our current item which counts as ATLEAST 1 flash;
        $flash_count = 1;

        /**
         * This increases adjacent octopuses by 1
         * We have 8 directions with offsets (y,x);
         * - upper-corner-left (-1,-1)
         * - up (-1,0)
         * - upper-corner-right (-1,1)
         * - left (0,-1)
         * - right (0,1)
         * - lower-corner-left (1,-1)
         * - down (1,0)
         * - lower-corner-right (1,1)
         */
        foreach ([[-1,-1], [-1,0], [-1,1], [0,-1], [0,1], [1,-1], [1,0], [1,1]] as [$offset_y, $offset_x]) {
            // Don't bother with non-existing jelly
            if (!isset($matrix[$point_y+$offset_y][$point_x+$offset_x])) {
                continue;
            }
            // Up the adjacent jelly
            $matrix[$point_y+$offset_y][$point_x+$offset_x]++;
            if ($matrix[$point_y+$offset_y][$point_x+$offset_x] > 9) {
                $flash_count += $this->flash($matrix, $flashed_points, $point_y + $offset_y, $point_x + $offset_x);
            }
        }

        return $flash_count;
    }
}
