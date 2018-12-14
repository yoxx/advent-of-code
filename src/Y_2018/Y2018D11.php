<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D11 extends Day
{
    protected $grid = [];

    public function run(OutputInterface $logger, int $part): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $this->createGrid($formatted_input[0]);
            $cor = $this->findSquare(3);
            $logger->writeln("P1 X,Y = " . $cor["cor"][0] . "," . $cor["cor"][1] . " value:" . $cor["value"]);
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $largest = [
                "value"=> 0,
                "size" => 0,
                "cor" => []
            ];

            for ($size = 1; $size <= 300; $size++) {
                $logger->writeln("Running size: " . $size);
                $output = $this->findSquare($size);

                if ($output["value"] >= $largest["value"]) {
                    $largest = $output;

                    $logger->writeln("p2 X,Y,SIZE " . $largest["cor"][0] . "," . $largest["cor"][1] . "," . $largest["size"]  . " value:" . $largest["value"]);
                }
            }
            $logger->writeln("p2 X,Y,SIZE " . $largest["cor"][0] . "," . $largest["cor"][1] . "," . $largest["size"]  . " value:" . $largest["value"]);
        }
    }

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $original_input[] = (int) trim($line);
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function findSquare(int $size): array
    {
        /**
         * We need to find the top left cor of the square with the largest amount of power.
         * To do this we loop through the the array and check all the valid levels
         */
        $cor_with_highest_yield = [
            "value"=> 0,
            "size" => 0,
            "cor" => []
        ];
        $heighest_yield = 0;

        // Loop through the array Y & X
        for($y_row = 1; $y_row <= 300 - $size; $y_row++) {
            for($x_row = 1; $x_row <= 300 - $size; $x_row++) {
                $value = 0;
                $tmp_arrray = \array_slice($this->grid, $y_row, $size);
                foreach ($tmp_arrray as $row) {
                    $value += \array_sum(\array_slice($row, $x_row, $size));
                }
                if ($value > $heighest_yield) {
                    $heighest_yield = $value;
                    $cor_with_highest_yield = [
                        "value"=> $heighest_yield,
                        "size" => $size,
                        "cor" => [$x_row+1,$y_row+1] // Add one to get the first square block we test
                    ];
                }
            }
        }

        return $cor_with_highest_yield;
    }

    private function createGrid(int $grid_serial): void
    {
        for($y_row = 1; $y_row <= 300; $y_row++) {
            $this->grid[$y_row] = [];
            for($x_row = 1; $x_row <= 300; $x_row++) {
                $this->grid[$y_row][$x_row] = $this->calculatePowerlevel($grid_serial, $x_row, $y_row);
            }
        }
    }

    private function calculatePowerlevel(int $grid_serial, int $x, int $y)
    {
        /**
         * Couple of rules to calculating the power level:
         * 1. The rack ID is $x + 10
         * 2. Begin with a power level of the rack ID times the Y coordinate.
         * 3. Increase the power level by the value of the grid serial number (your puzzle input).
         * 4. Set the power level to itself multiplied by the rack ID.
         * 5. Keep only the hundreds digit of the power level (so 12345 becomes 3; numbers with no hundreds digit become 0).
         * 6. Subtract 5 from the power level.
         */
        // Calc the rack id
        $rack_id = $x + 10;
        // Get the starting powerlevel
        $tmp_powerlevel = $rack_id * $y;
        //increase with grid serial
        $tmp_powerlevel += $grid_serial;
        // Multiply with rack_id
        $tmp_powerlevel *= $rack_id;
        // Get the 3th number
        $powerlevel = (int) substr((string) $tmp_powerlevel, -3, 1);

        return $powerlevel - 5;
    }
}