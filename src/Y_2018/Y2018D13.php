<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2018D13 extends Day
{
    public function run(OutputInterface $logger, int $part): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {

            $logger->writeln("P1 ");
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            $logger->writeln("P2 ");
        }
    }

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $original_input["rules"] = [];
        $handle = fopen($this->input_file, "rb");
        $count = 0;
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if($count === 0) {
                    $original_input["starting_input"] = str_split(explode(" ", trim($line))[2]);
                    $count++;
                } else {
                    $rule = explode(" ", trim($line));
                    if (\count($rule) > 1 && $rule[2] === "#") {
                        $original_input["rules"][] = $rule[0];
                    }
                }
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }
}