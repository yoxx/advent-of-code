<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

class IntCodeComputer
{
    protected ?IntCodeComputer $output_machine = null;
    protected bool $uses_output_machine = false;
    protected bool $running = true;
    protected bool $print_every_output = false;
    protected ?array $saved_state = null;
    protected int $relative_base = 0;
    protected ?int $start_input = null;
    protected ?int $last_output = null;
    protected ?int $phase = null;

    public function setPrintAllOutput(bool $bool): void
    {
        $this->print_every_output = $bool;
    }

    public function setRelativeBase(int $value): void
    {
        $this->relative_base = $value;
    }

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
        while (true) {
            // First round means we want the input to be a phase so if set set that as as input
            if ($opcode === 0 && $this->phase !== null) {
                $start_input = $this->phase;
            } else {
                $start_input = $this->start_input;
            }
            $jumped = false;
            $default_jump = 4;
            $amount_of_values = 0;
            $has_output_val = false;
            // currently A mode is not used
            [$parsed_opcode, $c_mode, $b_mode, $a_mode] = $this->parseOpcode($opcode_cache[$opcode]);
            switch ($parsed_opcode) {
                case 1: // add
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    $target = $this->handleKeyMode($opcode_cache, $opcode, 3, $a_mode);
                    $opcode_cache[$target] = $param1 + $param2;
                    break;
                case 2: // multiply
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    $target = $this->handleKeyMode($opcode_cache, $opcode, 3, $a_mode);
                    $opcode_cache[$target] = $param1 * $param2;
                    break;
                case 3: // store input
                    if ($start_input === null) {
                        // We cannot store null this means we still need to recieve input, save state and wait till we are called again
                        $this->saveState($opcode, $opcode_cache);
                        break 2;
                    }
                    // opcode and target constists of 2 parameters thus we alter our jump by 2
                    $amount_of_values -= 2;
                    $target = $this->handleKeyMode($opcode_cache, $opcode, 1, $c_mode);
                    $opcode_cache[$target] = $start_input;
                    // We expect input ONCE thus we clear the input. (We should get new input or wait for it)
                    $start_input = null;
                    break;
                case 4: // Output
                    // opcode and target constists of 2 parameters thus we alter our jump by 2
                    $amount_of_values -= 2;
                    $output_code = $this->handleParameterMode($opcode_cache,$opcode,1, $c_mode);
                    $this->last_output = $output_code;
                    if ($this->print_every_output) {
                        $output->writeln($output_code);
                    }
                    $has_output_val = true;
                    break;
                case 5: // Jump-if-true
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    if ($param1 !== 0) {
                        $opcode = $param2;
                        $jumped = true;
                    } else {
                        $amount_of_values -= 1;
                    }
                    break;
                case 6: // Jump-if-false
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    if ($param1 === 0) {
                        $opcode = $param2;
                        $jumped = true;
                    } else {
                        $amount_of_values -= 1;
                    }
                    break;
                case 7: // less than
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    $target = $this->handleKeyMode($opcode_cache, $opcode, 3, $a_mode);
                    if ($param1 < $param2) {
                        $opcode_cache[$target] = 1;

                    } else {
                        $opcode_cache[$target] = 0;
                    }
                    break;
                case 8: // equals
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $param2 = $this->handleParameterMode($opcode_cache, $opcode, 2, $b_mode);
                    $target = $this->handleKeyMode($opcode_cache, $opcode, 3, $a_mode);
                    if ($param1 === $param2) {
                        $opcode_cache[$target] = 1;

                    } else {
                        $opcode_cache[$target] = 0;
                    }
                    break;
                case 9: // Modify relative base
                    $amount_of_values -= 2;
                    $param1 = $this->handleParameterMode($opcode_cache, $opcode, 1, $c_mode);
                    $this->relative_base += $param1;
                    break;
                case 99:
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
            if ($has_output_val && $this->uses_output_machine) {
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
        switch ($opcode_length) {
            case 1: // OPCODE
                // Just the opcode
                $parsed_opcode = (int) $opcode_list[0];
                break;
            case 2: // OPCODE
                // Large opcode, currently only in use for 99
                $parsed_opcode = (int) ($opcode_list[0] . $opcode_list[1]);
                break;
            case 3: // C
                $parsed_opcode = (int) ($opcode_list[1] . $opcode_list[2]);
                $c_mode = (int) $opcode_list[0];
                break;
            case 4: // B
                $parsed_opcode = (int) ($opcode_list[2] . $opcode_list[3]);
                $c_mode = (int) $opcode_list[1];
                $b_mode = (int) $opcode_list[0];
                break;
            case 5: // A
                $parsed_opcode = (int) ($opcode_list[3] . $opcode_list[4]);
                $c_mode = (int) $opcode_list[2];
                $b_mode = (int) $opcode_list[1];
                $a_mode = (int) $opcode_list[0];
                break;
            default:
                // we dont know what to do with this... best to throw an error
                throw new Error("Unknown opcode length:" . $opcode_length);
        }

        return [$parsed_opcode, $c_mode, $b_mode, $a_mode];
    }

    private function handleParameterMode(array $opcode_cache, int $opcode, int $offset, int $mode): int
    {
        if ($mode === 1) {
            $key = $opcode + $offset;
            $input = $opcode_cache[$key];
        } elseif ($mode === 2) {
            $key = $opcode_cache[$opcode + $offset] + $this->relative_base;
            $input = $opcode_cache[$key];
        } else {
            $key = $opcode_cache[$opcode + $offset];
            $input = $opcode_cache[$key];
        }

        if ($input === null) {
            $input = 0;
        }

        return $input;
    }

    private function handleKeyMode(array $opcode_cache, int $opcode, int $offset, $mode): int
    {
        if ($mode === 1) {
            $key = $opcode + $offset;
        } elseif ($mode === 2) {
            $key = $this->relative_base + $opcode_cache[$opcode + $offset];
        } else { // 0
            $key = $opcode_cache[$opcode + $offset];
        }

        return $key;
    }
}
