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
            [$length, $width, $height] = reset($input);
            $solution = $this->calculateWrappingPaperRequired((int) $length, (int) $width, (int) $height);
            if ($solution !== 58) {
                $output->writeln("Test1 Failed! Wrapping paper required should have been 58 but was: " . $solution);
            } else {
                $output->writeln("Test1 success");
            }

            [$length, $width, $height] = end($input);
            $solution = $this->calculateWrappingPaperRequired((int) $length, (int) $width, (int) $height);
            if ($solution !== 43) {
                $output->writeln("Test2 Failed! Wrapping paper required should have been 43 but was: " . $solution);
            } else {
                $output->writeln("Test2 success");
            }
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
        $fuel_requirement = 0;
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $total_fuel_requirement_for_module = 0;
                $input_mass = (int) $line;
                while ($input_mass > 0) {
                    $fuel_requirement_for_module = $this->calculateFuelNeeded($input_mass);
                    if ($fuel_requirement_for_module < 0) {
                        $fuel_requirement_for_module = 0;
                        $input_mass = 0;
                    } else {
                        $input_mass = $fuel_requirement_for_module;
                    }

                    $total_fuel_requirement_for_module += $fuel_requirement_for_module;
                }

                $output->writeln("Module: Total fuel needed: " . $total_fuel_requirement_for_module);
                $fuel_requirement += $total_fuel_requirement_for_module;
            }

            $output->writeln("P2: Total fuel needed: " . $fuel_requirement);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function calculateWrappingPaperRequired(int $length, int $width, int $height): int
    {
        return (2*$length*$width) + (2*$width*$height) + (2*$height*$length) + min([($length*$width),($width*$height),($height*$length)]);
    }
}
