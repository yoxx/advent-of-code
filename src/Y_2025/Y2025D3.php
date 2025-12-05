<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2025;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2025D3 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputArray();
        $total = 0;
        foreach ($input as $battery) {
            $total += (int) $this->parseLargestJoltageValueWithBatterys($battery);
        }

        $output->writeln("P1: The solution is: " . $total);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();
        $total = 0;
        foreach ($input as $battery) {
            $total += (int) $this->parseLargestJoltageValueWithBatterys($battery, 12);
        }

        $output->writeln("P2: The solution is: " . $total);
    }

    private function parseLargestJoltageValueWithBatterys(string $input, int $amount_of_batteries = 2, $battery_string = ""): string {
        // strip the input of new lines and spaces
        $input = str_replace(["\n", " "], "", $input);
        // we need to find the largest value we can find in the string
        for ($i = 9; $i > 0; $i--) {
            // we found all batteries
            if (strlen($battery_string) === $amount_of_batteries) {
                break;
            }
            $index = strpos($input, (string) $i);
            $str_length = strlen($input);
            if ($index !== false && $battery_string === "" && $index !== ($str_length-1)) {
                $battery_string .= $i;
                // now remove the found value from the string
                $battery_array = str_split($input);
                // throw away everything before the found value
                array_splice($battery_array, 0, $index+1);
                $battery_string = $this->parseLargestJoltageValueWithBatterys( implode("", $battery_array), $amount_of_batteries, $battery_string);
            } else if ($index !== false && $battery_string !== "") {
                $battery_string .= $i;
                // now remove the found value from the string
                $battery_array = str_split($input);
                // throw away everything before the found value
                array_splice($battery_array, 0, $index+1);
                $battery_string = $this->parseLargestJoltageValueWithBatterys(implode("", $battery_array), $amount_of_batteries, $battery_string);
            }
        }
        return $battery_string;
    }
}
