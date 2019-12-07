<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D7 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $instrutionset = array_map('intval', $this->getInputLine(true, ","));

        if ($this->test) {
            $phases = [[1,0,4,3,2]];
        } else {
            $phases = $this->generatePhases0To4();
        }
        $highest_outcome = 0;
        foreach ($phases as $phase) {
            // We have 5 thrusters
            $last_output_code = 0;
            for ($amp_count = 0; $amp_count < 5; $amp_count++) {
                // First input sets the phase
                $int_code_computer = new IntCodeComputer();
                $int_code_computer->setStartInput($last_output_code);
                $int_code_computer->setPhase($phase[$amp_count]);
                [$opcode_cache, $output_code] = $int_code_computer->runOpcode($instrutionset, $output);
                $last_output_code = $output_code;
            }

            if ($last_output_code > $highest_outcome) {
                $highest_outcome = $last_output_code;
            }
        }

        $output->writeln("P1: The highest signal we can get is: " . $highest_outcome);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $instrutionset = array_map('intval', $this->getInputLine(true, ","));

        if ($this->test) {
            $phases = [[9,8,7,6,5]];
        } else {
            $phases = $this->generatePhases5To9();
        }
        $highest_outcome = 0;
        foreach ($phases as $phase) {
            // We have 5 thrusters
            $last_output_code = 0;
            $int_code_computer_status = [];
            while(true) {
                // if the last one is false everything is done break the loop
                if ($int_code_computer_status[4] === false) {
                    break;
                }

                // Loop through our amps
                for ($amp_count = 0; $amp_count < 5; $amp_count++) {
                    /** @var IntCodeComputer[] $int_code_computer_status */
                    if (!isset($int_code_computer_status[$amp_count])) {
                        $int_code_computer = new IntCodeComputer();
                        $int_code_computer->setUsesOutputMachine(true);
                        $int_code_computer->setPhase($phase[$amp_count]);
                        $int_code_computer_status[$amp_count] = $int_code_computer;
                    }

                    if ($int_code_computer_status[$amp_count] !== false && !$int_code_computer_status[$amp_count]->isRunning()) {
                        $int_code_computer_status[$amp_count] = false;
                        continue;
                    }

                    $int_code_computer_status[$amp_count]->setStartInput($last_output_code);
                    [$opcode_cache, $output_code] = $int_code_computer_status[$amp_count]->runOpcode($instrutionset, $output);
                    $last_output_code = $output_code;
                }
            }

            if ($last_output_code > $highest_outcome) {
                $highest_outcome = $last_output_code;
            }
        }

        $output->writeln("P2: The highest signal we can get is: " . $highest_outcome);
    }

    public function generatePhases0To4(): array
    {
        $phases = [];
        // Loop through all of the phase spots (we have 5...
        for ($count_0 = 0; $count_0 < 5; $count_0++) {
            for ($count_1 = 0; $count_1 < 5; $count_1++) {
                for ($count_2 = 0; $count_2 < 5; $count_2++) {
                    for ($count_3 = 0; $count_3 < 5; $count_3++) {
                        for ($count_4 = 0; $count_4 < 5; $count_4++) {
                            // We have 5 numbers for a phase setup
                            $phase_setup = [$count_0, $count_1, $count_2, $count_3, $count_4];
                            // To be correct only each phase setting should be used once (NO DUPLICATES!)
                            if (count(array_unique($phase_setup)) === 5) {
                                $phases[] = $phase_setup;
                            }
                        }
                    }
                }
            }
        }

        return $phases;
    }

    public function generatePhases5To9(): array
    {
        $phases = [];
        // Loop through all of the phase spots (we have 5...
        for ($count_0 = 5; $count_0 < 10; $count_0++) {
            for ($count_1 = 5; $count_1 < 10; $count_1++) {
                for ($count_2 = 5; $count_2 < 10; $count_2++) {
                    for ($count_3 = 5; $count_3 < 10; $count_3++) {
                        for ($count_4 = 5; $count_4 < 10; $count_4++) {
                            // We have 5 numbers for a phase setup
                            $phase_setup = [$count_0, $count_1, $count_2, $count_3, $count_4];
                            // To be correct only each phase setting should be used once (NO DUPLICATES!)
                            if (count(array_unique($phase_setup)) === 5) {
                                $phases[] = $phase_setup;
                            }
                        }
                    }
                }
            }
        }

        return $phases;
    }
}
