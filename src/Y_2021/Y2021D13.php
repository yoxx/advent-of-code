<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D13 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        [$cords, $instructions] = $this->parseInput();
        $output->writeln("P1: The solution is: ". $this->countDots($this->fold($this->buildMatrix($cords, $instructions), $instructions[0])));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        Utils::memoryIntensive1G();
        [$cords, $instructions] = $this->parseInput();
        $matrix = $this->buildMatrix($cords, $instructions);
        foreach($instructions as $instruction) {
            $matrix = $this->fold($matrix, $instruction);
        }

        $output->writeln("P2: The solution is: ");
        $this->drawMatrix($matrix, $output);
    }

    public function drawMatrix(array $matrix, OutputInterface $output): void
    {
        foreach ($matrix as $line) {
            $output->writeln(str_replace("1", "#", str_replace("0", ".", implode($line))));
        }
    }

    public function countDots($matrix): int
    {
        $count = 0;
        foreach($matrix as $line) {
            $count += array_sum($line);
        }

        return $count;
    }

    public function fold(array $matrix, $instruction): array
    {
        if ($instruction[0] === "y") {
            // Fold instruction Y means fold up
            // For both we can split the matrix in two
            $fold = array_splice($matrix, $instruction[1]);
            // Our fold line no longer counts
            unset($fold[0]);
            // Then for Y flip the bottom part vertically
            krsort($fold);
            // Reset keys
            $fold = array_values($fold);
        } else {
            $fold = [];
            // Fold instruction X means fold left
            // For both we can split the matrix in two
            foreach ($matrix as $lkey => $line) {
                // For X flip the right side horizontally
                $fold[$lkey] = array_splice($matrix[$lkey], $instruction[1]);
                // Our fold line no longer counts
                unset($fold[$lkey][0]);
                krsort($fold[$lkey]);
                // Reset keys
                $fold[$lkey] = array_values($fold[$lkey]);
            }
        }
        // Array merge
        foreach($matrix as $line_key => $line) {
            foreach($line as $ikey => $item) {
                if ($item !== $fold[$line_key][$ikey]) {
                    $matrix[$line_key][$ikey] = 1;
                }
            }
        }
        return $matrix;
    }

    public function buildMatrix(array $cords, array $instructions): array
    {
        [$y_max, $x_max] = $this->determineXYMax($instructions);
        $matrix = [];
        for($i = 0; $i <= $y_max; $i++) {
            $matrix[] = array_fill(0, $x_max+1, 0);
        }

        foreach($cords as $cord) {
            $matrix[$cord[1]][$cord[0]] = 1;
        }

        return $matrix;
    }

    public function determineXYMax(array $instructions): array
    {
        $x_max = 0;
        $y_max = 0;
        foreach($instructions as $instruction) {
            if ($x_max !== 0 && $y_max !== 0) {
                break;
            }
            if ($instruction[0] === "x" && $x_max === 0) {
                $x_max = $instruction[1] * 2;
            }
            if ($instruction[0] === "y" && $y_max === 0) {
                $y_max = $instruction[1] * 2;
            }
        }

        return [$y_max, $x_max];
    }

    public function parseInput(): array
    {
        $cords = [];
        $input = $this->getInputArray();
        foreach($input as $key => $item) {
            if ($item === "\n") {
                unset($input[$key]);
                break;
            }
            // We will have [x,y]
            $cords[] = array_map("intval", explode(",", trim($item)));
            unset($input[$key]);
        }
        $instructions = [];
        foreach($input as $instruction) {
            $instr = str_replace("fold along ", "", $instruction);
            $instr_arr = explode("=", trim($instr));
            $instr_arr[1] = (int) $instr_arr[1];
            $instructions[] = $instr_arr;
        }
        return [$cords, $instructions];
    }
}
