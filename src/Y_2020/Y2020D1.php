<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2020D1 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
       $input = $this->getInputArray();
       $output->writeln("P1: The solution is: " . $this->findEntryMatchWithTwoNumbersAndMultiply($input));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        $output->writeln("P2: The solution is: " . $this->findEntryMatchWithThreeNumbersAndMultiply($input));
    }

    private function findEntryMatchWithTwoNumbersAndMultiply(array $input): ?int
    {
        foreach($input as $id_x => $x) {
            foreach($input as $id_y => $y) {
                if ($id_x !== $id_y && (int) $x + (int) $y === 2020) {
                    return $x * $y;
                }
            }
        }
        return null;
    }

    private function findEntryMatchWithThreeNumbersAndMultiply(array $input): ?int
    {
        foreach($input as $id_x => $x) {
            foreach($input as $id_y => $y) {
                foreach($input as $id_z => $z) {
                    if ($id_x !== $id_y && $id_y !== $id_z && $id_z !== $id_x && (int)$x + (int)$y + (int)$z === 2020) {
                        return $x * $y * $z;
                    }
                }
            }
        }
        return null;
    }
}
