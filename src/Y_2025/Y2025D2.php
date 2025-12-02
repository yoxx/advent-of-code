<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2025;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2025D2 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputLine(true, ",");
        $total = 0;
        foreach ($input as $id_range) {
            $total += $this->parseInvalidIdRangeExactlyTwice($id_range);
        }

        $output->writeln("P1: The solution is: " . $total);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputLine(true, ",");

        $total = 0;
        foreach ($input as $id_range) {
            $total += $this->parseInvalidIdRangeAtleastTwice($id_range);
        }

        $output->writeln("P2: The solution is: " . $total);
    }

    private function parseInvalidIdRangeExactlyTwice(string $input): int
    {
        $sum = 0;
        // Ids are invalid when they are made up of repeated numbers like 55 or 6464 or 123123
        // We need to add all invalid ids together and return
        [$start, $end] = explode("-", $input);
        for ($i = (int) $start; $i <= (int) $end; $i++) {
            // Should 55, 6464, 123123, etc.
            if (preg_match('/^(.+)\1$/', (string) $i)) {
                $sum += $i;
            }
        }
        return $sum;
    }
    private function parseInvalidIdRangeAtleastTwice(string $input): int
    {
        $sum = 0;
        // Ids are invalid when they are made up of repeated numbers like 55 or 6464 or 123123
        // We need to add all invalid ids together and return
        [$start, $end] = explode("-", $input);
        for ($i = (int) $start; $i <= (int) $end; $i++) {
            // Should 55, 6464, 123123, etc. Lol i used this first at part 1 but did not read the assignment correctly
            if (preg_match('/^(.+)\1+$/', (string) $i)) {
                $sum += $i;
            }
        }
        return $sum;
    }
}
