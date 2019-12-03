<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D3 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $input[] = explode(",", $line);
            }

            $grid = $this->fillWireGrid($input);
            $manhattan_dist = $this->parseGridandReturnNearestManhattanDistance($grid);

            $output->writeln("P1: Lowest ManhattanDistance is: " . $manhattan_dist);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        return;
        $opcode_cache = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $opcode_cache = array_map('intval', explode(",",$line));
            }

            $original_input = $opcode_cache;
            for ($noun = 0;$noun < 100; $noun += 1) {
                for ($verb = 0;$verb < 100; $verb += 1) {
                    // Use original input
                    $opcode_cache = $original_input;
                    // Before running the program override values given by noun and verb
                    $opcode_cache[1] = $noun;
                    $opcode_cache[2] = $verb;
                    // Get the lenght
                    $instructionset_length = \count($opcode_cache);
                    for ($opcode = 0; $opcode < $instructionset_length; $opcode += 4) {
                        if ($opcode_cache[$opcode] === 99) {
                            break;
                        }
                        $opcode_cache = $this->handleOpcode($opcode_cache, $opcode_cache[$opcode], $opcode_cache[$opcode + 1], $opcode_cache[$opcode + 2], $opcode_cache[$opcode + 3]);
                    }
                    if ($opcode_cache[0] === 19690720) {
                        break 2;
                    }
                }
            }

            $output->writeln("P2: The noun: " . $noun . " the verb: " . $verb);
            $output->writeln("P2: puzzle to solve is 100 * noun + verb = " . (100 * $noun + $verb));

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    private function parseGridandReturnNearestManhattanDistance($grid): int
    {
        $lowest_manhattan_dist = null;

        ksort($grid);
        foreach($grid as $x => $y_line) {
            foreach ($y_line as $y => $val) {
                if ($val === "X") {
                    // Basecamp ar 0,0 to X,Y
                    $manhattan_dist = $this->calcManhattanDistance([0, 0], [$x, $y]);

                    if($manhattan_dist < $lowest_manhattan_dist) {
                        $lowest_manhattan_dist = $manhattan_dist;
                    } else if($lowest_manhattan_dist === null){
                        $lowest_manhattan_dist = $manhattan_dist;
                    }
                }
            }
        }

        return $lowest_manhattan_dist;
    }

    private function fillWireGrid($input): array
    {
        /**
         * Few base rules
         * - We have an infanite grid as possibility
         * - We have two arrays the following way [x => [y => value, y => value]]
         * - We set basecamp as 0,0
         * - Intersecting points will be filled with X non intersecting with line number
         */
        $grid = [0 => [0 => "B"]];

        foreach($input as $line_number => $line) {
            $last_x_cor = 0;
            $last_y_cor = 0;
            foreach ($line as $instruction) {
                // A letter as dir and a number as cor the direction determines X or Y the number how much
                $direction = substr($instruction, 0, 1);
                $cor = (int) substr($instruction, 1);

                switch ($direction) {
                    case "U":

                        for ($new_x_cor = $last_x_cor + 1; $new_x_cor <= $last_x_cor + $cor; $new_x_cor++) {
                            $grid = $this->fillGridPoints($line_number, $grid, $new_x_cor, $last_y_cor);
                        }
                        $last_x_cor = $last_x_cor + $cor;
                        break;
                    case "D":
                        for ($new_x_cor = $last_x_cor - 1; $new_x_cor >= $last_x_cor - $cor; $new_x_cor--) {
                            $grid = $this->fillGridPoints($line_number, $grid, $new_x_cor, $last_y_cor);
                        }
                        $last_x_cor = $last_x_cor - $cor;
                        break;
                    case "R":
                        for ($new_y_cor = $last_y_cor + 1; $new_y_cor <= $last_y_cor + $cor; $new_y_cor++) {
                            $grid = $this->fillGridPoints($line_number, $grid, $last_x_cor, $new_y_cor);
                        }
                        $last_y_cor = $last_y_cor + $cor;
                        break;
                    case "L":
                        for ($new_y_cor = $last_y_cor - 1; $new_y_cor >= $last_y_cor - $cor; $new_y_cor--) {
                            $grid = $this->fillGridPoints($line_number, $grid, $last_x_cor, $new_y_cor);
                        }
                        $last_y_cor = $last_y_cor - $cor;
                        break;
                }
            }
        }

        return $grid;
    }

    private function fillGridPoints(int $line_number, array $grid, int $x_cor, int $y_cor): array
    {
        // Check if we have the X cor already if not create
        if (isset($grid[$x_cor])) {
            // We have the x-cor check if we have the y-cor
            if (isset($grid[$x_cor][$y_cor]) && $grid[$x_cor][$y_cor] !== "B" && $grid[$x_cor][$y_cor] !== $line_number) {
                // WE HAVE A MATCH ZOMG
                $grid[$x_cor][$y_cor] = "X";
            } else {
                $grid[$x_cor][$y_cor] = $line_number;
            }
        } else {
            $grid[$x_cor] = [$y_cor => $line_number];
        }
        return $grid;
    }

    /**
     * Calcuate the distance between the coordinates
     * $corX[x,y]
     */
    private function calcManhattanDistance(array $cor1, array $cor2): int
    {
        return abs($cor1[0] - $cor2[0]) + abs($cor1[1] - $cor2[1]);
    }
}
