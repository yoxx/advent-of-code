<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D2 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray(true, " ");
        $output->writeln("P1: The solution is: " . $this->manoeuvreSubmarine($input));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray(true, " ");
        $output->writeln("P2: The solution is: " . $this->manoeuvreSubmarineWithAim($input));
    }

    private function manoeuvreSubmarine(array $input): int
    {
        $depth = 0;
        $horizontal_pos = 0;
        foreach ($input as $command) {
            switch ($command[0]) {
                case "forward":
                    $horizontal_pos += (int) $command[1];
                    break;
                case "down":
                    $depth += (int) $command[1];
                    break;
                case "up":
                    $depth -= (int) $command[1];
                    break;
            }
        }
        return $horizontal_pos * $depth;
    }

    private function manoeuvreSubmarineWithAim(array $input): int
    {
        $aim = 0;
        $depth = 0;
        $horizontal_pos = 0;
        foreach ($input as $command) {
            switch ($command[0]) {
                case "forward":
                    $horizontal_pos += (int) $command[1];
                    $depth += ($aim * (int) $command[1]);
                    break;
                case "down":
                    $aim += (int) $command[1];
                    break;
                case "up":
                    $aim -= (int) $command[1];
                    break;
            }
        }
        return $horizontal_pos * $depth;
    }
}
