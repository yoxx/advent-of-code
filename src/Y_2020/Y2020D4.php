<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Y_2019\IntCodeComputer;

class Y2020D4 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $valid_fields = ["byr", "iyr", "eyr", "hgt", "hcl", "ecl", "pid"]; // cid: we skip
        $input = $this->getInputLine();

        $passports = $this->parseToPassports($input);
        $valid_passports = $this->countValidPassports($valid_fields, $passports);
        $output->writeln("P1: amount of valid passports: " . $valid_passports);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputLine();
        $passports = $this->parseToPassports($input);
        $valid_passports = $this->validateFieldsAndCountValidPassports($passports);
        $output->writeln("P2: amount of valid passports: " . $valid_passports);
    }

    private function countValidPassports(array $valid_fields, array $passports): int
    {
        $valid_passport_count = 0;
        foreach ($passports as $passport) {
            $invalid_fields = [];
            foreach ($valid_fields as $field) {
                if (str_contains($passport, $field . ":")) {
                    continue;
                }
                $invalid_fields[] = $field;
            }

            // If we do not have a invalid fields we have a valid passport
            if (count($invalid_fields) === 0) {
                $valid_passport_count++;
            }
        }

        return $valid_passport_count;
    }

    /**
     * Parse the incoming lines to seperate passports
     */
    private function parseToPassports(array $input): array
    {
        // Collection of our passports
        $all_passports = [];
        // The current passport we are handling
        $cur_passport_index = 0;
        foreach ($input as $line) {
            // If we encounter a new line save the current passport and start to track the next
            if ($line === "\n") {
                $cur_passport_index++;
            }
            if (isset($all_passports[$cur_passport_index])) {
                $all_passports[$cur_passport_index] .= " " . trim($line);
            } else {
                $all_passports[$cur_passport_index] = trim($line);
            }
        }

        return $all_passports;
    }

    private function validateFieldsAndCountValidPassports(array $passports): int
    {
        $valid_passport_count = 0;
        foreach ($passports as $passport) {
            $passport_field = explode(" ", $passport);
            $passport_vars = [];
            foreach ($passport_field as $field) {
                $field_arr = explode(":", $field);
                if (isset($field_arr[0], $field_arr[1])) {
                    $passport_vars[$field_arr[0]] = $field_arr[1];
                }
            }

            if (
                isset($passport_vars["byr"], $passport_vars["iyr"], $passport_vars["eyr"], $passport_vars["hgt"], $passport_vars["hcl"], $passport_vars["ecl"], $passport_vars["pid"]) &&
                $this->validateByr($passport_vars["byr"]) && $this->validateIyr($passport_vars["iyr"]) && $this->validateEyr($passport_vars["eyr"]) &&
                $this->validateHgt($passport_vars["hgt"]) && $this->validateHcl($passport_vars["hcl"]) && $this->validateEcl($passport_vars["ecl"]) &&
                $this->validatePid($passport_vars["pid"])
            ) {
                $valid_passport_count++;
            }
        }

        return $valid_passport_count;
    }

    private function validateByr(string $value): bool
    {
        return (int) $value >= 1920 && (int) $value <= 2002;
    }

    private function validateIyr(string $value): bool
    {
        return (int) $value >= 2010 && (int) $value <= 2020;
    }

    private function validateEyr(string $value): bool
    {
        return (int) $value >= 2020 && (int) $value <= 2030;
    }

    private function validateHgt(string $value): bool
    {
        if (str_ends_with($value, "cm")) {
            return (int) str_replace("cm", "", $value) >= 150 && (int) str_replace("cm", "", $value) <= 193;
        }

        if (str_ends_with($value, "in")) {
            return (int) str_replace("in", "", $value) >= 59 && (int) str_replace("in", "", $value) <= 76;
        }
        return false;
    }

    private function validateHcl(string $value): bool
    {
        return preg_match("/^#[a-f0-9]{6}$/", $value) === 1;
    }

    private function validateEcl(string $value): bool
    {
        return in_array($value, ["amb", "blu", "brn", "gry", "grn", "hzl", "oth"]);
    }

    private function validatePid(string $value): bool
    {
        return preg_match("/^\d{9}$/", $value) === 1;
    }
}
