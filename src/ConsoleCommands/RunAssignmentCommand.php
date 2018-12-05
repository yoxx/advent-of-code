<?php

namespace yoxx\Advent\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class RunAssignmentCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run:day';
    public const RUN_PART_ALL = 0;
    public const RUN_PART_1 = 1;
    public const RUN_PART_2 = 2;

    protected function configure()
    {
        $this->setDescription('Run assignment from adventofcode.com')
            ->addOption("day", "d", InputOption::VALUE_REQUIRED)
            ->addOption("year", "y", InputOption::VALUE_REQUIRED)
            ->addOption("part", "p", InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption("year")) {
            $year = (int) $input->getOption("year");
            // Check the input
            if (!preg_match("/^(20)\d{2}$/", $year)) {
                $output->writeln("<error>You can only enter an integer as year, this must be a year ranging 2000-2099 but not above your current year!</error>");

                return;
            }
            // Make sure we are not going over the current year
            if ($year > (int) date("Y")) {
                $output->writeln("<error>The year cannot be over the current year</error>");
            }
        } else {
            // Get the current year as int
            $year = date("Y");
        }

        // Check if the input is an int and is a number ranging from 1-25
        if ($input->getOption("day")) {
            $day = $input->getOption("day");
            // Check the input
            if (!preg_match("/^([1-9]|1[0-9]|2[0-5])$/", $day)) {
                $output->writeln("<error>You can only enter an integer as day, this must be a day ranging 1-25!</error>");

                return;
            }
            // Make sure the day is not over the current day
            if ($year === (int) date("Y") && $day > (int) date("j")) {
                $output->writeln("<error>You cannot get the input of tomorrow</error>");

                return;
            }
        } else {
            // Get the current day as int
            $day = date("j");
            // If the day is larger than 25 we set it to 25 (the puzzel never goes after day 25)
            if ($day > 25) {
                $day = 25;
            }
        }

        $part = 0;
        if ($input->getOption("part")) {
            $part = $input->getOption("part");
            if (!preg_match("/^([1-9])$/", $part)) {
                $output->writeln("<error>You can only enter an integer ranging 0-2, 0 for all or 1,2 for part 1 or 2</error>");

                return;
            }
        }

        $class = "yoxx\Advent\Y_" . $year . "\Y" . $year . "D" . $day;
        /** @var Day $assignment */
        $assignment = new $class();
        $assignment->setInput(__DIR__ . "/../../input_files/Y_" . $year . "/Day" . $day . ".txt");
        $assignment->run($output, $part);
    }
}