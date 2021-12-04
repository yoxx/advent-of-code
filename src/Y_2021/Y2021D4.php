<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D4 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->bingo());
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $output->writeln("P2: The solution is: " . $this->bingoLast());
    }

    public function bingo(): int
    {
        [$list_of_drawn_numbers, $boards] = $this->parseInput();
        // A board has bingo is a row or column is full
        foreach($list_of_drawn_numbers as $num) {
            foreach ($boards as &$board) {
                foreach ($board as $key_row => $row) {
                    foreach ($row as $key_item => $item) {
                        if ($item[0] === $num) {
                            $board[$key_row][$key_item][1] = true;
                        }
                    }
                }
                if ($this->checkBingo($board)) {
                    return $this->sumOfBingoBoard($board) * $num;
                }
            }
        }
    }

    public function bingoLast(): int
    {
        [$list_of_drawn_numbers, $boards] = $this->parseInput();
        // A board has bingo is a row or column is full
        foreach($list_of_drawn_numbers as $num) {
            foreach ($boards as $board_key => &$board) {
                foreach ($board as $key_row => $row) {
                    foreach ($row as $key_item => $item) {
                        if ($item[0] === $num) {
                            $board[$key_row][$key_item][1] = true;
                        }
                    }
                }
                if ($this->checkBingo($board)) {
                    if (count($boards) > 1) {
                        unset($boards[$board_key]);
                    } else {
                        // The board with the last bingo!
                        return $this->sumOfBingoBoard($board) * $num;
                    }
                }
            }
        }
    }

    public function sumOfBingoBoard($board): int
    {
        $sum = 0;
        foreach($board as $row) {
            $values = array_filter($row, static function($item) {
                return $item[1] === false;
            });
            foreach ($values as $value) {
                $sum += $value[0];
            }
        }
        return $sum;
    }

    public function checkBingo(array $board): bool
    {
        for($key = 0; $key < 5; $key++) {
            // If a row is completely true
            $row_values_where_true = array_filter($board[$key], static function($item) {
                return $item[1];
            });
            // If a column is completely true
            $column_values_where_true = array_filter(array_map(static function ($i) use ($key) {
                return $i[$key];
            }, $board), static function($item) {
                return $item[1];
            });

            if (count($row_values_where_true) === 5 || count($column_values_where_true) === 5) {
                return true;
            }
        }
        return false;
    }

    public function parseInput(): array
    {
        $input = $this->getInputArray();
        // Get the first
        $list_of_drawn_numbers = array_map(static function($item) {return (int) $item;}, explode(",", trim($input[0])));

        $boards = [];
        $cur_board = [];
        for ($key = 1, $input_count = count($input); $key < $input_count; $key++) {
            if ($input[$key] === "\n") {
                if (!empty($cur_board)) {
                    $boards[] = $cur_board;
                }
                $cur_board = [];
                continue;
            }
            $cur_board[] = array_map(static function($item) {
                    return [(int) $item, false];
                }, array_values(array_filter(explode(" ", $input[$key]), static function ($item) {return $item !== "";})));
        }
        $boards[] = $cur_board;

        return [$list_of_drawn_numbers, $boards];
    }
}
