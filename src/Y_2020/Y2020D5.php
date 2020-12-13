<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
class Y2020D5 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $highest = 0;
        foreach ($input as $line) {
            $seat_num = $this->getSeatNumber(trim($line));
            if ($seat_num > $highest) {
                $highest = $seat_num;
            }
        }
        $output->writeln("P1: Highest seat ID: " . $highest);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $seats = [];
        foreach ($input as $line) {
            $seats[] = $this->getSeatNumber(trim($line));
        }

        $seat = $this->determineSeat($seats);
        $output->writeln("P1: my seat ID: " . $seat);
    }

    private function getSeatNumber(string $line): int
    {
        $input = str_split($line);
        $row = $this->partition($input, 0, 127, "F", "B", 0, 6);
        $column = $this->partition($input, 0, 7, "L", "R", 7, 9);

        return $row * 8 + $column;
    }

    private function partition(array $input, int $min_range, int $max_range, string $lower_half_char, string $upper_half_char, int $char_min_index, int $char_max_index): int
    {
        $min = $min_range;
        $max = $max_range;
        $char = null;

        // The row is only determined by the first 7 characters
        for ($count = $char_min_index; $count <= $char_max_index; $count++) {
            $char = $input[$count];
            switch ($char) {
                case $lower_half_char:
                    $max = (int) floor($max - (($max - $min) / 2));
                    break;
                case $upper_half_char:
                    $min = (int) ceil($min + (($max - $min) / 2));
                    break;
                default:
                    throw new \Error("Did not expect char: " . $char);
            }
        }

        // Return either the lower or the upper depending on the last char
        switch ($char) {
            case $upper_half_char:
                return $min;
            case $lower_half_char:
                return $max;
            default:
                throw new \Error("Did not expect char: " . $char);
        }
    }

    private function determineSeat(array $input): int
    {
        sort($input);

        $first_seat = reset($input);
        $own_seat = 0;
        $list_size = count($input);
        for ($i = 0; $i < $list_size; $i++) {
            // According to the text I just have to find the seat that has a number above and below mine
            if (isset($input[$i+1]) && $first_seat+1 !== $input[$i+1] && $first_seat+2 === $input[$i+1])
            {
                $own_seat = $first_seat+1;
                break;
            }

            $first_seat++;
        }

        return $own_seat;
    }
}
