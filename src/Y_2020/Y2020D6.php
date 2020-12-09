<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Y_2019\IntCodeComputer;

class Y2020D6 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $total = $this->countGroupInputAndSum($this->parseInputToGroups($input));
        $output->writeln("P1: sum of counts: " . $total);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $total = $this->countGroupInputAndSumWhereEveryoneAnsweredYes($this->parseInputToGroups($input));
        $output->writeln("P2: sum of counts: " . $total);
    }

    /**
     * Parse Groups
     */
    public function parseInputToGroups(array $input): array
    {
        $groups = [];
        $group_count = 0;
        $groups[$group_count] = [];
        foreach($input as $line) {
            if ($line === "\n") {
                $group_count++;
                $groups[$group_count] = [];
                continue;
            }

            $groups[$group_count][] = trim($line);
        }
        return $groups;
    }

    public function countGroupInputAndSum(array $groups): int
    {
        $total_sum = 0;
        foreach($groups as $group_arr) {
            $total_sum += count(array_unique(str_split(implode("", $group_arr))));
        }

        return $total_sum;
    }

    public function countGroupInputAndSumWhereEveryoneAnsweredYes(array $groups): int
    {
        $total_sum = 0;
        foreach($groups as $key => $group_arr) {
            foreach ($group_arr as $lkey => $line) {
                $group_arr[$lkey] = str_split($line);
            }
            $total_sum += count(array_intersect(...$group_arr));
        }

        return $total_sum;
    }
}
