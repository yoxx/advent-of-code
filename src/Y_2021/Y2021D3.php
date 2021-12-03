<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D3 extends Day
{
    private const MOST_COMMON = 1;
    private const LEAST_COMMON = 0;

    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray(true);
        $output->writeln("P1: The solution is: " . $this->determinePowerConsumption($input));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();
        $output->writeln("P2: The solution is: " . $this->determineLifeSupportRating($input));
    }

    private function determinePowerConsumption(array $input): int
    {
        $gamma = "";
        $epsilon = "";
        $input_subarray_count = count($input[0]);
        for ($key = 0; $key < $input_subarray_count; $key++) {
            $all_elements_of_current_key = array_map(static function ($i) use ($key) {
                return (int)$i[$key];
            }, $input);
            $array_counted = array_count_values($all_elements_of_current_key);
            // IF 0 is most common otherwise 1 is most common
            $gamma .= ($array_counted[0] > $array_counted[1]) ? "0" : "1";
            $epsilon .= ($array_counted[0] > $array_counted[1]) ? "1" : "0";
        }
        return bindec($gamma) * bindec($epsilon);
    }

    private function determineLifeSupportRating(array $input): int
    {
        $oxygen_rating = $this->determineRating(self::MOST_COMMON, $input);
        $co2_scrubber_rating = $this->determineRating(self::LEAST_COMMON, $input);

        return $oxygen_rating * $co2_scrubber_rating;
    }

    private function determineRating(int $most_or_least_common, array $input, $key = 0): int
    {
        $all_elements_of_current_key = array_map(static function ($i) use ($key) {
            return (int)$i[$key];
        }, $input);

        $array_counted = array_count_values($all_elements_of_current_key);
        foreach ($all_elements_of_current_key as $array_key => $value) {
            if ($array_counted[0] > $array_counted[1]) {
                // IF 0 is most common
                switch ($value) {
                    case 1:
                        // IF we want the most common value unset the 1 value element
                        if ($most_or_least_common === self::MOST_COMMON) {
                            unset($input[$array_key]);
                        }
                        break;
                    case 0:
                        // IF we want the least common value unset the 0 value element
                        if ($most_or_least_common === self::LEAST_COMMON) {
                            unset($input[$array_key]);
                        }
                }
            } else {
                // IF 1 is most common or they are the same
                switch ($value) {
                    case 1:
                        // IF we want the least common value unset the 1 value element
                        if ($most_or_least_common === self::LEAST_COMMON) {
                            unset($input[$array_key]);
                        }
                        break;
                    case 0:
                        // IF we want the most common value unset the 0 value element
                        if ($most_or_least_common === self::MOST_COMMON) {
                            unset($input[$array_key]);
                        }
                }
            }
        }

        if (count($input) > 1) {
            // lazy way to reset my keys
            $input = array_values($input);
            return $this->determineRating($most_or_least_common, $input, $key+1);
        }

        // We found the last rating standing
        return bindec(trim(reset($input)));
    }
}
