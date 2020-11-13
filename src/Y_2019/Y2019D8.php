<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D8 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $line_array = $this->getInputLine();
        // We have a single line thus simply arraymap
        $image_set = array_map('intval', str_split(str_replace("\n", "", $line_array[0])));

        if ($this->test) {
            $width = 3;
            $height = 2;
        } else {
            $width = 25;
            $height = 6;
        }

        $layers = $this->getLayers($image_set, $width, $height);

        $lowest_zero_count = null;
        $count_one = null;
        $count_two = null;
        foreach ($layers as $layer) {
            $count_of_zero_digits = 0;
            $count_of_one_digits = 0;
            $count_of_two_digits = 0;
            foreach ($layer as $row) {
                $values_count = array_count_values($row);
                if (isset($values_count[0])) {
                    $count_of_zero_digits += $values_count[0];
                }
                if (isset($values_count[1])) {
                    $count_of_one_digits += $values_count[1];
                }
                if (isset($values_count[2])) {
                    $count_of_two_digits += $values_count[2];
                }
            }

            if ($lowest_zero_count === null || $count_of_zero_digits < $lowest_zero_count) {
                $lowest_zero_count = $count_of_zero_digits;
                $count_one = $count_of_one_digits;
                $count_two = $count_of_two_digits;
            }
        }
        $output->writeln("P1: The number of 1 digits multiplied by the number of 2 digits is: " . ($count_one * $count_two));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $line_array = $this->getInputLine();
        // We have a single line thus simply arraymap
        $image_set = array_map('intval', str_split(str_replace("\n", "", $line_array[0])));

        if ($this->test) {
            $width = 3;
            $height = 2;
        } else {
            $width = 25;
            $height = 6;
        }

        $layers = $this->getLayers($image_set, $width, $height);
        $image = [];
        foreach ($layers as $layer) {
            foreach ($layer as $height_key => $image_height) {
                if (!isset($image[$height_key])) {
                    $image[$height_key] = [];
                }
                foreach ($image_height as $width_key => $pixel_val) {
                    if (!isset($image[$height_key][$width_key])) {
                        $image[$height_key][$width_key] = $pixel_val;
                    } else {
                        if ($image[$height_key][$width_key] === 2 && $pixel_val !== 2) {
                            $image[$height_key][$width_key] = $pixel_val;
                        }
                    }
                }
            }
        }

        $output->writeln("P2:");
        foreach ($image as $height_key => $image_height) {
            foreach ($image_height as $width_key => $pixel_val) {
                if ($pixel_val === 1) {
                    $val = "#";
                } else {
                    $val = ".";
                }
                $output->write($val);
            }
            $output->writeln("");
        }
    }

    private function getLayers($image_set, $width, $height): array
    {
        $image_length = count($image_set);
        $index = 0;
        $layers = [];
        while ($index < $image_length) {
            $layer = [];
            for ($cur_height = 0; $cur_height < $height; $cur_height++) {
                $layer[$cur_height] = [];
                for ($cur_width = 0; $cur_width < $width; $cur_width++) {

                    $layer[$cur_height][] = $image_set[$index];
                    $index++;
                }
            }

            $layers[] = $layer;
        }

        return $layers;
    }
}
