<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

class IntCodeComputer
{
    protected bool $uses_output_machine = false;
    protected ?IntCodeComputer $output_machine = null;
    protected ?array $saved_state = null;
    protected ?int $start_input = null;
    protected ?int $last_output = null;
    protected bool $running = true;
    protected ?int $phase = null;

    public function setPhase(int $phase): void
    {
        $this->phase = $phase;
    }

    public function setStartInput(int $input): void
    {
        $this->start_input = $input;
    }

    public function setUsesOutputMachine(bool $uses_output_machine): void
    {
        $this->uses_output_machine = $uses_output_machine;
    }

    public function setOutputMachine(IntCodeComputer $output_machine): void
    {
        $this->output_machine = $output_machine;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    private function saveState($opcode, $instruction_set): void
    {
        $this->saved_state = ["opcode" => $opcode, "instruction_set" => $instruction_set];
    }

    public function runOpcode(array $opcode_cache, OutputInterface $output): array
    {
        // Sanity check
        if (!$this->running) {
            $output->writeln("Running is false why are we being called? Killing program!");
            die();
        }

        // If we were previousely running retrieve the saved state and run from there
        if ($this->saved_state !== null) {
            $opcode = $this->saved_state["opcode"];
            $opcode_cache = $this->saved_state["instruction_set"];
        } else {
            $opcode = 0;
        }
        $instructionset_length = \count($opcode_cache);
        $output_code = null;
        while ($opcode < $instructionset_length) {
            // First round means we want the input to be a phase so if set set that as as input
            if ($opcode === 0 && $this->phase !== null) {
                $input = $this->phase;
            } else {
                $input = $this->start_input;
            }
            $jumped = false;
            $default_jump = 4;
            $amount_of_values = 0;
            $has_output_val = false;
            // currently A mode is not used
            [$parsed_opcode, $c_mode, $b_mode, $a_mode] = $this->parseOpcode($opcode_cache[$opcode]);
            switch ($parsed_opcode) {
                case 1: // add
                    $opcode_cache[$opcode_cache[$opcode + 3]] = (($c_mode === 1)? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]])
                        + (($b_mode === 1)? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    break;
                case 2: // multiply
                    $opcode_cache[$opcode_cache[$opcode + 3]] = (($c_mode === 1)? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]])
                        * (($b_mode === 1)? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    break;
                case 3: // store input
                    if ($input === null) {
                        // We cannot store null this means we still need to recieve input, save state and wait till we are called again
                        $this->saveState($opcode, $opcode_cache);
                        break 2;
                    }
                    $amount_of_values -= 2;
                    $opcode_cache[$opcode_cache[$opcode + 1]] = $input;
                    // We expect input ONCE thus we clear the input. (We should get new input or wait for it)
                    $input = null;
                    break;
                case 4: // output input
                    $amount_of_values -= 2;
                    $output_code = (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]);
                    $this->last_output = $output_code;
                    $has_output_val = true;
                    break;
                case 5: // Jump-if-true
                    $param1 = (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]);
                    $param2 = (($b_mode === 1) ? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    if ($param1 !== 0) {
                        $opcode = $param2;
                        $jumped = true;
                    } else {
                        $amount_of_values -= 1;
                    }
                    break;
                case 6: // Jump-if-false
                    $param1 = (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]);
                    $param2 = (($b_mode === 1) ? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    if ($param1 === 0) {
                        $opcode = $param2;
                        $jumped = true;
                    } else {
                        $amount_of_values -= 1;
                    }
                    break;
                case 7: // less than
                    $param1 = (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]);
                    $param2 = (($b_mode === 1) ? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    if ($param1 < $param2) {
                        $opcode_cache[$opcode_cache[$opcode + 3]] = 1;

                    } else {
                        $opcode_cache[$opcode_cache[$opcode + 3]] = 0;
                    }
                    break;
                case 8: // equals
                    $param1 = (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]);
                    $param2 = (($b_mode === 1) ? $opcode_cache[$opcode + 2] : $opcode_cache[$opcode_cache[$opcode + 2]]);
                    if ($param1 === $param2) {
                        $opcode_cache[$opcode_cache[$opcode + 3]] = 1;

                    } else {
                        $opcode_cache[$opcode_cache[$opcode + 3]] = 0;
                    }
                    break;
                case 99:
//                    if ($this->start_input !== null) {
//                        $output->writeln("Halt program");
//                    }
                    if ($output_code === null) {
                        $output_code = $this->last_output;
                    }
                    $this->running = false;
                    break 2;
                default:
                    $output->writeln("Undefined opcode encountered halting program - OPCODE: " . $opcode . " statement: " . $parsed_opcode . " original:" . $opcode_cache[$opcode]);
                    break 2;
            }
            if (!$jumped) {
                $opcode += $default_jump + $amount_of_values;
            }

            // If we have an output machine we treat every output as a signal thus we break off our programm untill we recieve a new input
            if ($has_output_val && $this->uses_output_machine){
                $this->saveState($opcode, $opcode_cache);
                break;
            }
        }

        if ($output_code !== null && $this->uses_output_machine === false) {
            $output->writeln("Last output: " . $output_code);
        }

        return [$opcode_cache, $output_code];
    }

    private function parseOpcode(int $opcode_instruction): array
    {
        /**
         * Opcode - Can be a number from 1 number to 4 numbers
         * (currently the 5th number counting from the right is alway 0 due to the target always being 0 mode)
         *
         * ABCDE
         *  1002
         *
         * DE - two-digit opcode,      02 == opcode 2
         * C - mode of 1st parameter,  0 == position mode
         * B - mode of 2nd parameter,  1 == immediate mode
         * A - mode of 3rd parameter,  0 == position mode,
         *                                  omitted due to being a leading zero
         */
        // Sane defaults
        $parsed_opcode = null;
        $c_mode = 0;
        $b_mode = 0;
        $a_mode = 0;

        $opcode_list = str_split((string) $opcode_instruction);
        $opcode_length = \count($opcode_list);
        switch($opcode_length) {
            case 1:
                // Just the opcode
                $parsed_opcode = (int) $opcode_list[0];
                break;
            case 2:
                // Large opcode, currently only in use for 99
                $parsed_opcode = (int) ($opcode_list[0] . $opcode_list[1]);
                break;
            case 3:
                $parsed_opcode = (int) ($opcode_list[1] . $opcode_list[2]);
                $c_mode = (int) $opcode_list[0];
                break;
            case 4:
                $parsed_opcode = (int) ($opcode_list[2] . $opcode_list[3]);
                $c_mode = (int) $opcode_list[1];
                $b_mode = (int) $opcode_list[0];
                break;
            case 5:
            default:
                // we dont know what to do with this... best to throw an error
                throw new Error("Unknown opcode length:" . $opcode_length);
        }
        return [$parsed_opcode, $c_mode, $b_mode, $a_mode];
    }
}
