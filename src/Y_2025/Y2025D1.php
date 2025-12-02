<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2025;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2025D1 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray();
        $output->writeln("P1: The solution is: " . $this->countNumberOfTimes0Ends($input));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        $output->writeln("P2: The solution is: " . $this->countNumberOfTimes0WasPassed($input));
    }

    private function countNumberOfTimes0Ends(array $input): int
    {
        $dail_pos = 50;
        $number_of_zero = 0;
        foreach ($input as $instruction) {
            // remove and parse the first letter of the line if L = - if R = +
            $direction = $instruction[0] === "L" ? -1 : 1;
            $steps = (int)substr($instruction, 1);
            // Set the new position
            $dail_pos += $steps * $direction;
            $dail_pos = ($dail_pos % 100 + 100) % 100;

            if ($dail_pos === 0) {
                $number_of_zero++;
            }
        }
        return $number_of_zero;
    }

    private function countNumberOfTimes0WasPassed(array $input): int
    {
        $dail_pos = 50;
        $number_of_zero = 0;
        foreach ($input as $instruction) {
            // remove and parse the first letter of the line if L = - if R = +
            $direction = $instruction[0] === "L" ? -1 : 1;
            $steps = (int)substr($instruction, 1);
            $prev_pos = $dail_pos;
            // Set the new position
            $dail_pos += $steps * $direction;
            // Determine how many times we passed 0
            if ($direction === 1) {
                $number_of_zero += (int) (floor($dail_pos / 100) - floor($prev_pos / 100));
            } else {
                $number_of_zero += (int) (ceil($prev_pos / 100) - ceil($dail_pos / 100));
            }
            // Wrap around 0-99 for the next iteration state
            $dail_pos = ($dail_pos % 100 + 100) % 100;
        }
        return $number_of_zero;
    }
}
