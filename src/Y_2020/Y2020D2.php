<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2020;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2020D2 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
       $input = $this->getInputArray();

       $valid_passwords = 0;
       foreach($input as $item) {
           [$password, $policy, $min, $max] = $this->parsePaswordPolicy($item);
           if ($this->validatePassword($password, $policy, $min, $max)) {
               $valid_passwords++;
           }
       }
       $output->writeln("P1: The amount of valid passwords is: " . $valid_passwords);
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $input = $this->getInputArray();

        $valid_passwords = 0;
        foreach($input as $item) {
            [$password, $policy, $pos1, $pos2] = $this->parsePaswordPolicy($item);
            if ($this->validatePassword2($password, $policy, $pos1, $pos2)) {
                $valid_passwords++;
            }
        }
        $output->writeln("P2: The amount of valid passwords is: " . $valid_passwords);
    }

    private function parsePaswordPolicy(string $input): array
    {
        $input_arr = explode(" ", $input);
        $min_max_arr = explode("-", $input_arr[0]);
        $policy = str_replace(":", "", $input_arr[1]);
        $password = trim($input_arr[2]);

        return [$password, $policy, (int) $min_max_arr[0], (int) $min_max_arr[1]];
    }

    private function validatePassword(string $password, string $policy, int $min, int $max): bool
    {
        $count = substr_count($password, $policy);
        // Password is invalid when the policy is not in there
        // or when policy is in there more than the max
        // or less than the min
        return $count >= $min && $count <= $max;
    }

    private function validatePassword2(string $password, string $policy, int $pos1, int $pos2): bool
    {
        $password_arr = str_split($password);
        // Password is invalid when the policy is not in there
        // or when policy is in there on more than 1 of the positions
        if (($password_arr[$pos1-1] === $policy && $password_arr[$pos2-1] !== $policy) ||
            ($password_arr[$pos1-1] !== $policy && $password_arr[$pos2-1] === $policy)) {
            return true;
        }
        return false;
    }
}
