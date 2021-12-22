<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D22 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->parseInput();
        $matrix = $this->runInstructions($output, $input);
        $output->writeln("P1: The solution is: " . array_count_values($matrix)[1]);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        Utils::memoryIntensive16G();
        $input = $this->parseInput();
        $matrix = $this->runInstructions($output, $input, false);
        $output->writeln("P2: The solution is: " . array_count_values($matrix)[1]);
    }

    public function runInstructions(OutputInterface $output, array $instructions, bool $enforce_bounds = true): array
    {
        $matrix = [];
        foreach($instructions as $reboot_operation) {
            $output->writeln(implode(",", [$reboot_operation["operation"], implode("-", $reboot_operation["x"]), implode("-", $reboot_operation["y"]), implode("-", $reboot_operation["z"])]));
            // check bounds
            if ($enforce_bounds && ($reboot_operation["x"][0] < -50 || $reboot_operation["x"][1] > 50 ||
                $reboot_operation["y"][0] < -50 || $reboot_operation["y"][1] > 50 ||
                $reboot_operation["z"][0] < -50 || $reboot_operation["z"][1] > 50)) {
                continue;
            }
            for ($x = $reboot_operation["x"][0]; $x <= $reboot_operation["x"][1]; $x++) {
                for ($y = $reboot_operation["y"][0]; $y <= $reboot_operation["y"][1]; $y++) {
                    for ($z = $reboot_operation["z"][0]; $z <= $reboot_operation["z"][1]; $z++) {
                        $matrix[implode(",", [$x, $y, $z])] = $reboot_operation["operation"];
                    }
                }
            }
        }
        return $matrix;
    }

    public function parseInput(): array
    {
        $input = $this->getInputArray();
        $output = [];
        foreach ($input as $line) {
            $blob = [];
            $line_arr = explode(" ", $line);
            $blob["operation"] = ($line_arr[0] === "on") ? 1 : 0;
            preg_match_all("/(-\d+|\d+)/", $line, $matches, PREG_SET_ORDER, 0);
            $blob["x"] = [(int) $matches[0][0], (int) $matches[1][0]];
            $blob["y"] = [(int) $matches[2][0], (int) $matches[3][0]];
            $blob["z"] = [(int) $matches[4][0], (int) $matches[5][0]];
            $output[] = $blob;
        }
        return $output;
    }
}
