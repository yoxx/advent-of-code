<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2019;

use Error;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2019D6 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $orbits = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // input ala X)Y means X is orbitted by Y
                $orbit = explode(")", $line);
                $orbiting_object = str_replace("\n", "", $orbit[1]);
                if(!isset($orbits[$orbit[0]])) {
                    $orbits[$orbit[0]] = [$orbiting_object => $orbiting_object];
                } else {
                    $orbits[$orbit[0]][$orbiting_object] = $orbiting_object;
                }
            }

            $output->writeln("P1: The total amount of direct and indirect orbits is: " . $this->calculateAmountOfOrbits($orbits));

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $orbits = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // input ala X)Y means X is orbitted by Y
                $orbit = explode(")", $line);
                $orbiting_object = str_replace("\n", "", $orbit[1]);
                if(!isset($orbits[$orbit[0]])) {
                    $orbits[$orbit[0]] = [$orbiting_object => $orbiting_object];
                } else {
                    $orbits[$orbit[0]][$orbiting_object] = $orbiting_object;
                }
            }
            $santa_is_orbiting = null;
            $im_orbiting = null;

            foreach ($orbits as $object => $orbiting_array) {
                    if ($im_orbiting !== null && $santa_is_orbiting !== null) {
                        break; // early out when we know enough
                    }

                    if (isset($orbiting_array["SAN"])) {
                        $santa_is_orbiting = $this->countUpTheChain($orbits, $object);
                    } elseif (isset($orbiting_array["YOU"])) {
                        $im_orbiting = $this->countUpTheChain($orbits, $object);
                    }
            }

            // We now have both paths to base, find the objects both have access to with the lowest steps
            $im_diff_path = array_intersect_key($im_orbiting["path"], $santa_is_orbiting["path"]);
            $santa_diff_path = array_intersect_key($santa_is_orbiting["path"], $im_orbiting["path"]);

            // Dont count ourselfs as objects
            $output->writeln("P2: The total amount of steps to Santa is: " . (min($im_diff_path)-1 + min($santa_diff_path)-1));

            fclose($handle);
        } else {
            $output->writeln("Error reading line input from file");
        }
    }

    private function calculateAmountOfOrbits(array $orbits): int
    {
        $amount_of_orbits = 0;
        // Go trough the tree and find the last object (the object that is not being orbited by anyone)
        while(true) {
            // We don't have any orbits left to check
            if (\count($orbits) < 1) {
                break;
            }
            // Loop through the objects
            foreach ($orbits as $object => $orbiting_array) {
                foreach ($orbiting_array as $orbiting_object) {
                    // If we have an orbiting object that is not is the list as object that is being orbited unset and up the count.
                    if (!isset($orbits[$orbiting_object])) {
                        $chain_steps = $this->countUpTheChain($orbits, $object);
                        // Up the total orbit count
                        $amount_of_orbits += $chain_steps["steps"];
                        // unset the last one (we dont need it anymore)
                        unset($orbits[$object][$orbiting_object]);
                    }
                }
                // If all the orbiting objects have been counted and there are none left unset
                if (\count($orbiting_array) < 1) {
                    unset($orbits[$object]);
                }
            }
        }
        return $amount_of_orbits;
    }

    private function countUpTheChain($orbits, $current_object_that_is_being_orbited): array
    {
        // We start at the end of the chain which means we atleast have 1 step to an object that is being orbited
        $chain_steps = 1;
        // We are the end of an orbit chain count the steps back to COM
        $curr_point = $current_object_that_is_being_orbited;
        $path = [$curr_point => $chain_steps];
        while ($curr_point !== "COM") {
            foreach ($orbits as $key => $orbiting_array) {
                if (isset($orbiting_array[$curr_point])) {
                    // Go up the chain
                    $chain_steps++;
                    $curr_point = $key;
                    $path[$curr_point] = $chain_steps;
                    break;
                }
            }
        }

        return ["steps" => $chain_steps,"path" => $path];
    }
}
