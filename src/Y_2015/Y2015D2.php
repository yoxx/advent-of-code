<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2015;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;

class Y2015D2 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray(true, "x");

        if ($this->test) {
            $this->testAssignment1($input, $output);
        } else {
            $total_sq_feet = 0;
            foreach ($input as [$length, $width, $height]) {
                $total_sq_feet += $this->calculateWrappingPaperRequired((int) $length, (int) $width, (int) $height);
            }

            $output->writeln("Total sq feet of wraping paper they should order: " . $total_sq_feet);
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray(true, "x");

        if ($this->test) {
            $this->testAssignment2($input, $output);
        } else {
            $total_sq_feet = 0;
            foreach ($input as [$length, $width, $height]) {
                $total_sq_feet += $this->calculateRibbonRequired((int) $length, (int) $width, (int) $height);
            }

            $output->writeln("Total sq feet of ribbon they should order: " . $total_sq_feet);
        }
    }

    private function testAssignment1(array $input, OutputInterface $output): void
    {
        [$length, $width, $height] = reset($input);
        $solution = $this->calculateWrappingPaperRequired((int) $length, (int) $width, (int) $height);
        if ($solution !== 58) {
            $output->writeln("P1 Test1 Failed! Wrapping paper required should have been 58 but was: " . $solution);
        } else {
            $output->writeln("P1 Test1 success");
        }

        [$length, $width, $height] = end($input);
        $solution = $this->calculateWrappingPaperRequired((int) $length, (int) $width, (int) $height);
        if ($solution !== 43) {
            $output->writeln("P1 Test2 Failed! Wrapping paper required should have been 43 but was: " . $solution);
        } else {
            $output->writeln("P1 Test2 success");
        }
    }
    private function testAssignment2(array $input, OutputInterface $output): void
    {
        [$length, $width, $height] = reset($input);
        $solution = $this->calculateRibbonRequired((int) $length, (int) $width, (int) $height);
        if ($solution !== 34) {
            $output->writeln("P2 Test1 Failed! Ribbon required should have been 34 but was: " . $solution);
        } else {
            $output->writeln("P2 Test1 success");
        }

        [$length, $width, $height] = end($input);
        $solution = $this->calculateRibbonRequired((int) $length, (int) $width, (int) $height);
        if ($solution !== 14) {
            $output->writeln("P2 Test2 Failed! Ribbon required should have been 14 but was: " . $solution);
        } else {
            $output->writeln("P2 Test2 success");
        }
    }

    private function calculateRibbonRequired(int $length, int $width, int $height): int
    {
        $inputs = [$length, $width, $height];
        sort($inputs);
        return ($inputs[0] + $inputs[0] + $inputs[1] + $inputs[1]) + ($length * $width * $height);
    }

    private function calculateWrappingPaperRequired(int $length, int $width, int $height): int
    {
        return (2*$length*$width) + (2*$width*$height) + (2*$height*$length) + min([($length*$width),($width*$height),($height*$length)]);
    }
}
