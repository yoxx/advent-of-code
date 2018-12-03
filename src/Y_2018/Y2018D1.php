<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2018D1 extends Day
{
    public function run(OutputInterface $output): void
    {
        $this->runAssignment1($output);
        $this->runAssignment2($output);
    }

    public function runAssignment1(OutputInterface $output): void
    {
        $freq = 0;
        $count = 0;
        $handle = fopen($this->input_file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $freq += (int) $line;
                $count++;

                $output->writeln("Line: " . $count . " adds " . (int) $line . " results in: " . $freq);
            }

            $output->writeln("Final Answer: " . $freq);

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $freq = 0;
        $count = 0;
        $freq_reached = [];
        $freq_twice = false;

        while (!$freq_twice) {
            $handle = fopen($this->input_file, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $freq += (int) $line;

                    if (isset($freq_reached[$freq])) {
                        $freq_twice = true;

                        $output->writeln("Ran trough the file " . $count . " times");
                        $output->writeln("Found freq that is reached twice " . $freq);
                        break;
                    }

                    $freq_reached[$freq] = $freq;
                }

                $count++;


                fclose($handle);
            } else {
                $output->writeln("Error reading line input from file");
            }
        }
    }
}