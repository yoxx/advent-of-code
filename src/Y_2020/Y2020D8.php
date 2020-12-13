<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2020D8 extends Day
{
    private array $instructionset = [];
    private int $accumulator = 0;

    public function runAssignment1(OutputInterface $output): void
    {
        $this->instructionset = $this->getInputArray(true, " ");
        $this->isolationRunnerUpUntillAnyInstructionIsRunTwice(0);

        $output->writeln("P1: Accumulator is: " . $this->accumulator);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $instructionset = $this->getInputArray(true, " ");
        $keep_running = true;
        $changed_key = 0;
        while($keep_running) {
            $altered_instructionset = $instructionset;
            $current_instruction = $altered_instructionset[$changed_key][0];
            switch($current_instruction) {
                case "nop":
                    $altered_instructionset[$changed_key][0] = "jmp";
                    break;
                case "jmp":
                    $altered_instructionset[$changed_key][0] = "nop";
                default:
            }
            $this->instructionset = $altered_instructionset;
            $this->accumulator = 0;

            $keep_running = $this->isolationRunner(0);
            $changed_key++;
        }

        $output->writeln("P2: Accumulator is: " . $this->accumulator);
    }

    public function isolationRunnerUpUntillAnyInstructionIsRunTwice(int $key): void
    {
        // We encounter an instruction that runs twice stop and return!
        if (count($this->instructionset[$key]) > 2) {
            return;
        }
        [$instruction, $value] = $this->instructionset[$key];
        switch ($instruction){
            case "acc":
                $this->accumulator += $value;
                $this->instructionset[$key][] = true;
                $this->isolationRunnerUpUntillAnyInstructionIsRunTwice($key+1);
                break;
            case "jmp":
                $this->instructionset[$key][] = true;
                $this->isolationRunnerUpUntillAnyInstructionIsRunTwice($key + $value);
                break;
            case "nop":
                $this->instructionset[$key][] = true;
                $this->isolationRunnerUpUntillAnyInstructionIsRunTwice($key+1);
                break;
            default:
                throw new \Error("Instruction unkown: " . $instruction ." " . $value);
        }
    }

    private function isolationRunner(int $key): bool
    {
        if (!isset($this->instructionset[$key])) {
            // We reached our termination point or ran out of bounds
            return false;
        }
        if (count($this->instructionset[$key]) > 2) {
            return true;
        }

        [$instruction, $value] = $this->instructionset[$key];
        switch ($instruction){
            case "acc":
                $this->accumulator += $value;
                $this->instructionset[$key][] = true;
                return $this->isolationRunner($key+1);
            case "jmp":
                $this->instructionset[$key][] = true;
                return $this->isolationRunner($key + $value);
            case "nop":
                $this->instructionset[$key][] = true;
                return $this->isolationRunner($key+1);
            default:
                throw new \Error("Instruction unkown: " . $instruction ." " . $value);
        }
    }
}
