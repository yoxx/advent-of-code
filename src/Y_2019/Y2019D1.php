<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D1 extends Day
{
    public function run(OutputInterface $output, int $part): void
    {
        $this->runAssignment1($output);
        $this->runAssignment2($output);

    }

    public function runAssignment1(OutputInterface $output): void
    {
        $fuel_requirement = 0;
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $fuel_requirement += $this->calculateFuelNeeded((int) $line);
            }

            $output->writeln("P1: Fuel needed for all modules: " . $fuel_requirement);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
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

    private function calculateFuelNeeded(int $mass): int
    {
        /**
         * Fuel required to launch a given module is based on its mass.
         * Specifically, to find the fuel required for a module, take its mass, divide by three, round down, and subtract 2.
         */
        $fuel_requirement = $mass / 3;
        $fuel_requirement = (int) $fuel_requirement - 2;
        return $fuel_requirement;
    }
}
