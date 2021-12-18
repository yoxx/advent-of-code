<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D17 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $finish_zone = $this->parseInput();
        $min_x = 0;
        $min_x_volocity = 0;
        for ($i = 1; $min_x < $finish_zone["x"][0]; $i++) {
            $min_x += $i;
            $min_x_volocity = $i;
        }

        $running = true;
        $max_y_pos = 0;
        $init_y_veloc = 0;
        while ($running) {
            $x_pos = 0;
            $y_pos = 0;
            $x_veloc = $min_x_volocity;
            $y_veloc = $init_y_veloc;
            for ($steps = 0; $steps < $min_x_volocity; $steps++) {
                [$x_pos, $y_pos, $x_veloc, $y_veloc] = $this->runStep($x_pos, $y_pos, $x_veloc, $y_veloc);
                if ($y_pos > $max_y_pos) {
                    $max_y_pos = $y_pos;
                }
            }
            if ($y_pos <= $finish_zone["y"][1] && $y_pos >= $finish_zone["y"][0] && $y_pos !== $max_y_pos) {
                $running = false;
            }
            $init_y_veloc++;
        }
        $output->writeln("P1: The solution is: " . $min_x_volocity . "," . $init_y_veloc - 1 . " highest step:" . $max_y_pos);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        [$value, $version_sum] = $this->parseBinaryString($this->parseInput());
        $output->writeln("P2: The solution is: " . $value);
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
