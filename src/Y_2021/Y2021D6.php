<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D6 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->determineAmountOfFishAfterXDays(80));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->determineAmountOfFishAfterUsingAlternateMethod(256));
    }

    public function determineAmountOfFishAfterXDays(int $days): int
    {
        $all_fish = array_map("intval", $this->getInputLine(true, ","));

        for ($day = 1; $day <= $days; $day++) {
            echo $day.PHP_EOL;
            $new_fish = [];
            $all_fish = array_map(static function(int $fish) use (&$new_fish){
                if ($fish === 0) {
                    $fish = 6;
                    $new_fish[] = 8;
                } else {
                    $fish--;
                }
                return $fish;
            },$all_fish);
            $all_fish = array_merge($all_fish, $new_fish);
        }

        return count($all_fish);
    }


    public function determineAmountOfFishAfterUsingAlternateMethod(int $days): int
    {
        $all_fish = array_map("intval", $this->getInputLine(true, ","));

        // We do not go fish by fish, we treat all the fish at the same stage at the same time.
        // Fill a array of 8 items with zeroes these represent stage 0-8 a fish can be in
        $fish_array = array_fill(0,8,0);
        // Fill our original input into that array (you will get multiple fish at the same stage)
        foreach ($all_fish as $fish) {
            $fish_array[$fish]++;
        }

        // Now we loop through the days
        for ($day = 1; $day <= $days; $day++) {
            // Get all adult fish at stage 0
            $adult_fish = $fish_array[0];
            // Move all OTHER stages (1-8) down a stage
            for($index = 1; $index <= 8; $index++) {
                $fish_array[$index-1] = $fish_array[$index];
            }
            // All adult fish at stage 0 go to 6 and combine with the fish already there
            $fish_array[6] += $adult_fish;
            // All adult fish also create 1 baby they go to 8
            $fish_array[8] = $adult_fish;
        }

        // Sum my fish to get a final count after the days
        return array_sum($fish_array);
    }
}
