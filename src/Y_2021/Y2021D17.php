<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D17 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $finish_zone = $this->parseInput();

        // Use the highest value x+y in our input as a max for our brute force.
        $max_shooting_range = max(abs($finish_zone["x"][0]), abs($finish_zone["x"][1])) + max(abs($finish_zone["y"][0]), abs($finish_zone["y"][1]));
        $highest_reached_y_of_all_shots = 0;
        for ($x_init_veloc = 0; $x_init_veloc < $max_shooting_range;$x_init_veloc++) {
            for ($y_init_veloc = -$max_shooting_range; $y_init_veloc < $max_shooting_range; $y_init_veloc++) {
                $highest_y = 0;
                $x_pos = 0;
                $x_veloc = $x_init_veloc;
                $y_pos = 0;
                $y_veloc = $y_init_veloc;
                while ($x_pos < $finish_zone["x"][1] && $y_pos > $finish_zone["y"][0]) {
                    // Run our step
                    [$x_pos, $y_pos, $x_veloc, $y_veloc] = $this->runStep($x_pos, $y_pos, $x_veloc, $y_veloc);
                    // Update the highest value this round
                    if ($y_pos > $highest_y) {
                        $highest_y = $y_pos;
                    }
                    // Check if we finished?
                    if ($x_pos >= $finish_zone["x"][0] && $x_pos <= $finish_zone["x"][1] && $y_pos >= $finish_zone["y"][0] && $y_pos <= $finish_zone["y"][1]) {
                        // We are in our finish_zone check if this round had the highest y pos
                        if ($highest_y > $highest_reached_y_of_all_shots) {
                            $highest_reached_y_of_all_shots = $highest_y;
                            break;
                        }
                    }
                }
            }
        }
        $output->writeln("P1: The solution is: " . $highest_reached_y_of_all_shots);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $finish_zone = $this->parseInput();

        // Use the highest value x+y in our input as a max for our brute force.
        $max_shooting_range = max(abs($finish_zone["x"][0]), abs($finish_zone["x"][1])) + max(abs($finish_zone["y"][0]), abs($finish_zone["y"][1]));
        $valid_veloc = 0;
        for ($x_init_veloc = 0; $x_init_veloc < $max_shooting_range;$x_init_veloc++) {
            for ($y_init_veloc = -$max_shooting_range; $y_init_veloc < $max_shooting_range; $y_init_veloc++) {
                $x_pos = 0;
                $x_veloc = $x_init_veloc;
                $y_pos = 0;
                $y_veloc = $y_init_veloc;
                while ($x_pos < $finish_zone["x"][1] && $y_pos > $finish_zone["y"][0]) {
                    // Run our step
                    [$x_pos, $y_pos, $x_veloc, $y_veloc] = $this->runStep($x_pos, $y_pos, $x_veloc, $y_veloc);
                    // Check if we finished?
                    if ($x_pos >= $finish_zone["x"][0] && $x_pos <= $finish_zone["x"][1] && $y_pos >= $finish_zone["y"][0] && $y_pos <= $finish_zone["y"][1]) {
                        // We are in our finish_zone check if this round had the highest y pos
                        $valid_veloc++;
                        break;
                    }
                }
            }
        }
        $output->writeln("P2: The solution is: " . $valid_veloc);
    }

    public function runStep(int $x_pos, int $y_pos, int $x_veloc, int $y_veloc): array
    {
        /**
         * - The probe's x position increases by its x velocity.
         * - The probe's y position increases by its y velocity.
         * - Due to drag, the probe's x velocity changes by 1 toward the value 0;
         *   that is, it decreases by 1 if it is greater than 0, increases by 1 if it is less than 0, or does not change if it is already 0.
         * - Due to gravity, the probe's y velocity decreases by 1.
         */
        $x_pos += $x_veloc;
        $y_pos += $y_veloc;
        $x_veloc += 0 <=> $x_veloc;
        $y_veloc--;

        return [$x_pos, $y_pos, $x_veloc, $y_veloc];
    }

    public function parseInput(): array
    {
        $input = explode(", ", trim(str_replace("target area: ", "", $this->getInputLine()[0])));
        $x = explode("=", $input[0]);
        $x = explode("..", $x[1]);
        $y = explode("=", $input[1]);
        $y = explode("..", $y[1]);
        return ["x" => [(int)$x[0], (int)$x[1]], "y" => [(int)$y[0], (int)$y[1]]];
    }
}
