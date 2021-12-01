<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D1 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray();
        $output->writeln("P1: The solution is: " . $this->countNumberOfTimesDepthIncreases($input));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        $output->writeln("P2: The solution is: " . $this->countNumberOfTimesDepthIncreasesTreeCountWindow($input));
    }

    private function countNumberOfTimesDepthIncreases(array $input): int
    {
        $number_of_increases = 0;
        foreach ($input as $key => $depth) {
            // Count the number of times a depth measurement increases from the previous measurement
            if (isset($input[$key - 1]) && $input[$key - 1] < $depth) {
                $number_of_increases++;
            }
        }
        return $number_of_increases;
    }

    private function countNumberOfTimesDepthIncreasesTreeCountWindow(array $input): int
    {
        $number_of_increases = 0;
        foreach ($input as $key => $depth) {
            // Count the number of times a depth measurement + the next two increases from the previous three measurements
            if (isset($input[$key - 3], $input[$key - 2], $input[$key - 1])) {
                $previous_sliding_window = $input[$key - 3] + $input[$key - 2] + $input[$key - 1];
                $current_sliding_window = $input[$key - 2] + $input[$key - 1] + $depth;
                if ($previous_sliding_window < $current_sliding_window) {
                    $number_of_increases++;
                }
            }
        }
        return $number_of_increases;
    }
}
