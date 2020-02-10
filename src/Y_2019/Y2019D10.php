<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D10 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray();

    }

    public function runAssignment2(OutputInterface $output): void
    {
        return;
        if ($this->test) {
            $output->writeln("P2 has no test");

            return;
        }

        $instrutionset = array_map('intval', $this->getInputLine(true, ","));

        $int_code_computer = new IntCodeComputer();
        $int_code_computer->setStartInput(2);
        [$opcode_cache, $output_code] = $int_code_computer->runOpcode($instrutionset, $output);
        $last_output_code = $output_code;

        $output->writeln("P2: The coordinates are: " . $last_output_code);
    }

    private function determineAsteroidsInView(array $input_array, int $cor_x, int $cor_y): int
    {
        /**
         * To determine the line we need to check we use:
         * https://en.wikipedia.org/wiki/Bresenham%27s_line_algorithm
         *
         * We go from starting point x,y folowing up the line til we see a asteroid,
         * if we se an asteroid we break and check the next line
         */
        $edges = $this->determinEdgePoints($input_array);
        $lines_from_asteroid_to_edge = $this->calculateLines($input_array, $cor_x, $cor_y, $edges);
        // #.........
        //...A......
        //...B..a...
        //.EDCG....a
        //..F.c.b...
        //.....c....
        //..efd.c.gb
        //.......c..
        //....f...c.
        //...e..d..c

    }

    private function determinEdgePoints(array $input_array): array
    {
        $width = \count($input_array[0]);
        $height = \count($input_array);
        $edge_keys = [];
        /** 0 => top, 1 => left, 2 => right, 3 => bottom */
        for ($side = 0; $side < 4; $side++) {
            $count = 0;
            while ($count < $width) {
                switch ($side) {
                    case 0:
                        $x = 0;
                        $y = $count;
                        break;
                    case 1:
                        $x = $count;
                        $y = 0;
                        break;
                    case 2:
                        $x = $count;
                        $y = $width - 1;
                        break;
                    case 3:
                        $x = $height -1;
                        $y = $count;
                }
                $edge_keys[] = [$x,$y];
                $count++;
            }
        }

        return $edge_keys;
    }

    private function calculateLines(array $input_array, int $x, int $y, array $edges): array
    {
        $lines = [];

        return $lines;
    }
}
