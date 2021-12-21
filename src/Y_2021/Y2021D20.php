<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D20 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        [$algorithm, $input_image] = $this->parseInput();
        $output_image = $this->enhance($algorithm, $input_image);

        $output->writeln("P1: The solution is: " . $this->determineUniqueBeacons($this->parseScannerData()));
    }

    public function runAssignment2(OutputInterface $output): void
    {
//        $output->writeln("P2: The solution is: " . $largest_magnitude);
    }

    public function enhance(string $algorithm, $input_image): array
    {
        // first we enlarge the infinite matrix once to account for new stuff
        foreach($input_image as $line_key => $matrix_line) {
            if (!isset($input_image[$line_key - 1])) {
                $input_image[$line_key - 1] = implode("", array_fill(0, strlen($matrix_line) + 2, "."));
            }
            if (!isset($input_image[$line_key + 1])) {
                $input_image[$line_key + 1] = implode("", array_fill(0, strlen($matrix_line) + 2, "."));
            }
            $input_image[$line_key] = "." . $input_image[$line_key] . ".";
        }
        $input_image = array_values($input_image);
        // NOW we enhance
        $output_image = [];
        $image_line_count = $input_image;
        for ($ii = 0; $ii < $image_line_count; $ii++) {
            $input_image = array_values($input_image);
            $line_length = strlen($input_image[$ii]);
            for ($i = 1; $i <= $line_length; $i++) {
//                if ($i === 1) {
//                    $input_image[$ii] = "." . $input_image[$ii] . ".";
//                }
                if (!isset($input_image[$ii - 1])) {
                    $input_image[$ii - 1] = implode("", array_fill(0, $line_length, "."));
                }
                if (!isset($input_image[$ii+1])) {
                    $input_image[$ii + 1] = implode("", array_fill(0, $line_length, "."));
                }
//                else if ($i === 1) {
//                    $input_image[$ii + 1] = "." . $input_image[$ii + 1] . ".";
//                }
                if (!isset($output_image[$ii])) {
                    $output_image[$ii] = $input_image[$ii];
                }
                $line_check = strlen($output_image[$ii]);
                $upper = substr($input_image[$ii - 1], $i - 1, 3);
                $middle = substr($input_image[$ii], $i - 1, 3);
                $lower = substr($input_image[$ii + 1], $i - 1, 3);

                $bin_num = bindec(str_replace([".", "#"], ["0", "1"], $upper . $middle . $lower));
                $enhanced = $algorithm[$bin_num];
                $output_image[$ii][$i] = $enhanced;
            }
        }
        return $output_image;
    }

    public function parseInput(): array
    {
        $input = $this->getInputArray();

        $algorithm = $input[0];
        unset($input[0], $input[1]);
        $input_image = [];
        foreach ($input as $line) {
            $input_image[] = trim($line);
        }
        return [$algorithm, $input_image];
    }
}
