<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

class Y2019Utils
{
    public static function runOpcode(array $opcode_cache, OutputInterface $output, ?int $input = null): array
    {
        $instructionset_length = \count($opcode_cache);
        $opcode = 0;
        while ($opcode < $instructionset_length) {
            $jumped = false;
            $default_jump = 4;
            $amount_of_values = 0;
            // currently A mode is not used
            [$parsed_opcode, $c_mode, $b_mode, $a_mode] = self::parseOpcode($opcode_cache[$opcode]);
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
                    $amount_of_values -= 2;
                    $opcode_cache[$opcode_cache[$opcode + 1]] = $input;
                    break;
                case 4: // output input
                    $amount_of_values -= 2;
                    $output->writeln("Output: " . (($c_mode === 1) ? $opcode_cache[$opcode + 1] : $opcode_cache[$opcode_cache[$opcode + 1]]));
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
                    if ($input !== null) {
                        $output->writeln("Halt program");
                    }
                    break 2;
                default:
                    $output->writeln("Undefined opcode encountered halting program - OPCODE: " . $opcode);
                    break 2;
            }
            if (!$jumped) {
                $opcode += $default_jump + $amount_of_values;
            }
        }

        return $opcode_cache;
    }

    private static function parseOpcode(int $opcode_instruction): array
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
