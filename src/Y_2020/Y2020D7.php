<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2020D7 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $bags = $this->parseBags($input);

        $bagcount_that_can_hold_gold = 0;
        foreach ($bags as $bag) {
            $contains = $this->findBag($bags, $bag, "shiny gold");
            if ($contains > 0) {
                $bagcount_that_can_hold_gold++;
            }
        }
        $output->writeln("P1: Amount of colors that can contain a golden bag: " . $bagcount_that_can_hold_gold);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $bags = $this->parseBags($input);
        $output->writeln("P2: Amount of bags in the shiny golden bag: " . $this->countContentsOfBag($bags, $bags["shiny gold"]));
    }

    /**
     * Input line contains:
     * - [<COLOR1> <COLOR2>] bags contain X [<COLOR1> <COLOR2>] bag(s)(,)(X [<COLOR1> <COLOR2>])
     */

    private function parseBags(array $input_line): array
    {
        $bags = [];
        foreach ($input_line as $line) {
            $bag_and_contents = explode(" bags contain ", $line);
            $bags_inside = explode(", ", $bag_and_contents[1]);
            foreach ($bags_inside as $key => $bag) {
                if ($bag === "no other bags.\n") {
                    $bags[$bag_and_contents[0]] = [];
                    continue;
                }
                $bag_arr = explode(" ", $bag);
                $bags[$bag_and_contents[0]][$bag_arr[1] . " " . $bag_arr[2]] = $bag_arr[0];
            }
        }

        return $bags;
    }

    private function findBag(array $all_bags, array $current_bag, string $bag_needle): int
    {
        $count = 0;
        foreach($current_bag as $bag_name => $bag_amount) {
            // We found the bag we are looking for!
            if($bag_name === $bag_needle){
                return 1;
            }
            // Keep going...
            $count += $this->findBag($all_bags, $all_bags[$bag_name], $bag_needle);
        }

        return $count;
    }

    private function countContentsOfBag(array $all_bags, array $current_bag): int
    {
        $count = 0;
        foreach($current_bag as $bag_name => $bag_amount) {
            $amount_of_bags_in_bag = $this->countContentsOfBag($all_bags, $all_bags[$bag_name]);
            if ($amount_of_bags_in_bag > 0) {
                $count += $bag_amount + $bag_amount * $amount_of_bags_in_bag;
            } else {
                $count += $bag_amount;
            }
        }

        return $count;
    }
}
