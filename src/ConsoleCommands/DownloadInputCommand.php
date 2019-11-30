<?php

namespace yoxx\Advent\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;
use yoxx\Advent\Utils;

class DownloadInputCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'input:download';
    private static $download_url = "https://adventofcode.com/";

    protected function configure()
    {
        $this->setDescription('Downloads puzzle input from adventofcode.com')
            ->addOption("day", "d", InputOption::VALUE_REQUIRED)
            ->addOption("year", "y", InputOption::VALUE_REQUIRED)
            ->addOption("force", "f", InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Downloading AdventOfCode-Input");
        // Check if the input is an int and is a number ranging from 2000-2099 and not above our current year.
        if ($input->getOption("year")) {
            $year = (int) $input->getOption("year");
            // Check the input
            if (!preg_match("/^(20)\d{2}$/", $year)){
                $output->writeln("<error>You can only enter an integer as year, this must be a year ranging 2000-2099 but not above your current year!</error>");
                return 0;
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
                return 0;
            }
            // Make sure the day is not over the current day
            if ($year === (int) date("Y") && $day > (int) date("j")) {
                $output->writeln("<error>You cannot get the input of tomorrow</error>");
                return 0;
            }
        } else {
            // Get the current day as int
            $day = date("j");
            // If the day is larger than 25 we set it to 25 (the puzzel never goes after day 25)
            if ($day > 25) {
                $day = 25;
            }
        }

        $filename = __DIR__ . "/../../input_files/Y_" . $year . "/Day" . $day . ".txt";

        // Store our file if the file does not already exist
        if (!file_exists($filename) || $input->getOption("force")) {
            // Check if the year dir exists
            $foldername = __DIR__ . "/../../input_files/Y_" . $year;
            if (!is_dir($foldername)) {
                mkdir($foldername, 0775);
            }

            // Set options for our get call
            $options = array(
                "http"=>array(
                    "method"=>"GET",
                    "header"=>"Accept-language: en\r\n" .
                        "Cookie: session=" . Utils::getEnvValue("SESSION_KEY") . "\r\n"
                )
            );

            $context = stream_context_create($options);

            if (file_put_contents($filename, file_get_contents("https://adventofcode.com/" . $year . "/day/" . $day . "/input", false, $context))) {
                $output->writeln("<info>File: " . $filename . " downloaded successfully</info>");
            } else {
                $output->writeln("<info>File could not be saved please save the file manually</info>");
            }
        } else {
            $output->writeln("<error>File: " . $filename . " already exists! If you want to override this file use --force</error>");
            return 0;
        }
    }
}
