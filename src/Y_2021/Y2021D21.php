<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D21 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $players = $this->parseInput();
        $dice_counter = 1;
        $deterministic_dice = 1;
        while ($players[1]["score"] <= 1000 && $players[2]["score"] <= 1000) {
            foreach ($players as $pkey => $player) {
                $moves = 0;
                for ($i = 0; $i < 3; $i++) {
                    $moves += $deterministic_dice;
                    $deterministic_dice++;
                    if ($deterministic_dice > 100) {
                        $deterministic_dice = 1;
                    }
                    $dice_counter++;
                }
                $players[$pkey]["pos"] += $moves;
                if ($players[$pkey]["pos"] > 10) {
                    $players[$pkey]["pos"] %= 10;
                    if ($players[$pkey]["pos"] === 0) {
                        $players[$pkey]["pos"] = 1;
                    }
                }
                $players[$pkey]["score"] += $players[$pkey]["pos"];
                if ($players[$pkey]["score"] > 1000) {
                    break 2;
                }
             }
        }

        $lowest_score = min([$players[1]["score"], $players[2]["score"]]);

        $output->writeln("P1: The solution is: " . $lowest_score * $dice_counter);
    }

    public function runAssignment2(OutputInterface $output): void
    {
//        $output->writeln("P2: The solution is: " . $largest_magnitude);
    }

    public function parseInput(): array
    {
        $input = $this->getInputArray();
        $output = [];
        foreach ($input as $line) {
            preg_match_all("/\d/", $line, $matches, PREG_SET_ORDER, 0);
            $output[(int) $matches[0][0]] = ["pos" => (int) $matches[1][0], "score" => 0];
        }
        return $output;
    }
}
