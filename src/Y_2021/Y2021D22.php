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
        $input = $this->parseInput();
        $matrix = $this->runInstructionsP2($input);
        $output->writeln("P2: The solution is: " . array_count_values($matrix)[1]);
    }

    public function runInstructions(OutputInterface $output, array $instructions): array
    {
        $matrix = [];
        foreach($instructions as $reboot_operation) {
            $output->writeln(implode(",", [$reboot_operation["operation"], implode("-", $reboot_operation["x"]), implode("-", $reboot_operation["y"]), implode("-", $reboot_operation["z"])]));
            // check bounds
            if ($reboot_operation["x"][0] < -50 || $reboot_operation["x"][1] > 50 ||
                $reboot_operation["y"][0] < -50 || $reboot_operation["y"][1] > 50 ||
                $reboot_operation["z"][0] < -50 || $reboot_operation["z"][1] > 50) {
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

    public function runInstructionsP2(array $instructions): array
    {
        $cuboid_list = [];
        // Fill our initial list
        foreach($instructions as $intersection_cuboid) {
            $new_cubiod_list = [];
            // We check if we have an intersection. between cuboid a/b
            foreach($cuboid_list as $cubiod_list_item) {
                // There is an intersect when cube A x,y and z are within cube B's x,y, and z there is an intersect
                if ((($intersection_cuboid["x"][0] >= $cubiod_list_item["x"][0] && $intersection_cuboid["x"][0] <= $cubiod_list_item["x"][1]) ||
                    ($intersection_cuboid["x"][1] >= $cubiod_list_item["x"][0] && $intersection_cuboid["x"][1] <= $cubiod_list_item["x"][1])) &&
                    (($intersection_cuboid["y"][0] >= $cubiod_list_item["y"][0] && $intersection_cuboid["y"][0] <= $cubiod_list_item["y"][1]) ||
                    ($intersection_cuboid["y"][1] >= $cubiod_list_item["y"][0] && $intersection_cuboid["y"][1] <= $cubiod_list_item["y"][1])) &&
                    (($intersection_cuboid["z"][0] >= $cubiod_list_item["z"][0] && $intersection_cuboid["z"][0] <= $cubiod_list_item["x"][1]) ||
                    ($intersection_cuboid["z"][1] >= $cubiod_list_item["z"][0] && $intersection_cuboid["z"][1] <= $cubiod_list_item["x"][1]))) {
                    // We have an intersection!!
                    $new_cubiod_list[] = [
                        "operation" => $intersection_cuboid["operation"],
                        "x" => [min($intersection_cuboid["x"][0], $cubiod_list_item["x"][0]), max($intersection_cuboid["x"][1], $cubiod_list_item["x"][1])],
                        "y" => [min($intersection_cuboid["y"][0], $cubiod_list_item["y"][0]), max($intersection_cuboid["y"][1], $cubiod_list_item["y"][1])],
                        "z" => [min($intersection_cuboid["z"][0], $cubiod_list_item["z"][0]), max($intersection_cuboid["z"][1], $cubiod_list_item["z"][1])],
                    ];
                }
            }
            if (empty($new_cubiod_list)) {
                $new_cubiod_list[implode(",", [implode("-", $intersection_cuboid["x"]), implode("-", $intersection_cuboid["y"]), implode("-", $intersection_cuboid["z"])])] = $intersection_cuboid;
            }
            // Set the new items as cubiod list
            $cuboid_list = array_merge($cuboid_list, $new_cubiod_list);
        }
        return $cuboid_list;
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
