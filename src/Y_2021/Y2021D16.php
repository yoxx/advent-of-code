<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D16 extends Day
{
    private const TYPE_SUM = 0;
    private const TYPE_PRODUCT = 1;
    private const TYPE_MINIMUM = 2;
    private const TYPE_MAXIMUM = 3;
    private const TYPE_LITERAL = 4;
    private const TYPE_GREATER_THAN = 5;
    private const TYPE_LESS_THAN = 6;
    private const TYPE_EQUAL_TO = 7;

    public function runAssignment1(OutputInterface $output): void
    {
        [$value, $version_sum] = $this->parseBinaryString($this->parseInput());

        $output->writeln("P1: The solution is: " . $version_sum);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        [$value, $version_sum] = $this->parseBinaryString($this->parseInput());
        $output->writeln("P2: The solution is: " . $value);
    }

    public function parseBinaryString(string $binary_string, ?int $max_packets_to_handle = null, ?int $operator = null)
    {
        $value = null;
        $version_sum = 0;
        $handled_packet_count = 0;
        while ($binary_string !== "") {
            if (strlen($binary_string) < 11) {
                // we have leftover bits! Ignore them
                $binary_string = "";
                break;
            }
            [$version, $type, $binary_string] = $this->parseHeader($binary_string);
            $version_sum += $version;
            if ($type === self::TYPE_LITERAL) {
                // Everything behind this header is a literal string
                [$literal_value, $binary_string] = $this->decodeLiteral($binary_string);
                if ($operator !== null) {
                    $value = $this->determineValue($value, $literal_value, $operator);
                } else {
                    $value = $literal_value;
                }
            } else {
                // Anything OTHER than self::TYPE_LITERAL is an operator
                if ($operator === null) {
                    $operator = $type;
                }
                // Every operator has a length type ID
                $value_subpackets = 0;
                $version_sum_subpackets = 0;
                $length_type_id = $binary_string[0];
                $binary_string = substr($binary_string, 1);
                if ($length_type_id === "0") {
                    // The next 15 bits are a number that represents the TOTAL length in bits of the subpackets contained by this packet
                    $length_in_bits = bindec(substr($binary_string, 0, 15));
                    $subpackets = substr($binary_string, 15, $length_in_bits);
                    [$value_subpackets, $version_sum_subpackets] = $this->parseBinaryString($subpackets, null, $type);
                    $binary_string = substr($binary_string, 15 + $length_in_bits);
                } elseif ($length_type_id === "1") {
                    // The next 11 bits are a number that represents the NUMBER of subpackets contained by this packet
                    $number_of_subpackets = bindec(substr($binary_string, 0, 11));
                    $binary_string = substr($binary_string, 11);
                    [$value_subpackets, $version_sum_subpackets, $binary_string] = $this->parseBinaryString($binary_string, $number_of_subpackets, $type);
                }

                $value = $this->determineValue($value, $value_subpackets, $operator);
                $version_sum += $version_sum_subpackets;
            }

            if ($max_packets_to_handle !== null) {
                $handled_packet_count++;
                if ($max_packets_to_handle === $handled_packet_count) {
                    break;
                }
            }
        }
        return [$value, $version_sum, $binary_string];
    }

    public function determineValue(?int $value, int $new_value, int $operator) {
        switch ($operator) {
            case self::TYPE_SUM:
                if ($value === null) {
                    $value = $new_value;
                } else {
                    $value += $new_value;
                }
                break;
            case self::TYPE_PRODUCT:
                if ($value === null) {
                    $value = $new_value;
                } else {
                    $value *= $new_value;
                }
                break;
            case self::TYPE_MINIMUM:
                if ($value === null) {
                    $value = $new_value;
                } else if ($new_value < $value) {
                    $value = $new_value;
                }
                break;
            case self::TYPE_MAXIMUM:
                if ($value === null) {
                    $value = $new_value;
                } else if ($new_value > $value) {
                    $value = $new_value;
                }
                break;
            case self::TYPE_GREATER_THAN:
                if ($value === null) {
                    $value = $new_value;
                } else if ($new_value < $value) {
                    $value = 1;
                } else {
                    $value = 0;
                }
                break;
            case self::TYPE_LESS_THAN:
                if ($value === null) {
                    $value = $new_value;
                } else if ($new_value > $value) {
                    $value = 1;
                } else {
                    $value = 0;
                }
                break;
            case self::TYPE_EQUAL_TO:
                if ($value === null) {
                    $value = $new_value;
                } else if ($new_value === $value) {
                    $value = 1;
                } else {
                    $value = 0;
                }
                break;
        }
        return $value;
    }

    public function decodeLiteral(string $decoded_string): array
    {
        $decoding = true;
        $output = "";
        while ($decoding) {
            $output .= substr($decoded_string, 1, 4);
            if ($decoded_string[0] === "0") {
                if (strlen($decoded_string) < 11) {
                    // we have leftover bits! Ignore them
                    $decoded_string = "";
                }
                $decoding = false;
            }
            $decoded_string = substr($decoded_string, 5);
        }

        return [bindec($output), $decoded_string];
    }


    public function parseHeader(string $decoded_string): array
    {
        $version = substr($decoded_string, 0, 3);
        $type = substr($decoded_string, 3, 3);
        $decoded_string = substr($decoded_string, 6);

        return [bindec($version), bindec($type), $decoded_string];
    }

    public function parseInput(): string
    {
        $input = str_split(trim($this->getInputLine()[0]));
        $map = [
            "0" => "0000",
            "1" => "0001",
            "2" => "0010",
            "3" => "0011",
            "4" => "0100",
            "5" => "0101",
            "6" => "0110",
            "7" => "0111",
            "8" => "1000",
            "9" => "1001",
            "A" => "1010",
            "B" => "1011",
            "C" => "1100",
            "D" => "1101",
            "E" => "1110",
            "F" => "1111",
        ];
        $output = "";
        foreach ($input as $hex) {
            $output .= $map[$hex];
        }

        return $output;
    }
}
