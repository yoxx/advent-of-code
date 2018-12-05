<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2018D4 extends Day
{
    public function run(OutputInterface $output, int $part): void
    {
        // Part 1
        $original_input = $this->readAssignment($output);
        $guard_stats = [];

        usort($original_input, [$this, "sortByChronological"]);

        $guard_shifts = $this->parseInputToShiftsArray($original_input);

        $stats = $this->calcGuardStats($guard_stats, $guard_shifts);

        $output->writeln("<info>Solution to assignment 1: id x minute_slept_the_most = " .
            $stats[0]["id"] . " * " . $stats[0]["minute_asleep_at_most_of_the_time"] . " = " . (int) $stats[0]["id"] * (int) $stats[0]["minute_asleep_at_most_of_the_time"] . "</info>");

        $output->writeln("<info>Solution to assignment 2: id x minute_slept_the_most = " .
            $stats[1]["id"] . " * " . $stats[1]["minute_asleep_at_most_of_the_time"] . " = " . (int) $stats[1]["id"] * (int) $stats[1]["minute_asleep_at_most_of_the_time"] . "</info>");

    }

    /**
     * Read the puzzle input from the file
     */
    private function readAssignment(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $original_input[] = $this->parseForSort($line);
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    /**
     * Parse the array so we can sort this
     * EX: [1518-11-01 00:00] Guard #10 begins shift
     *
     * [
     *  "datetime" => "1518-11-01 00:00",
     *  "message" => "Guard #10 begins shift"
     * ]
     */
    private function parseForSort(string $line): array
    {
        $tmp = explode("]", $line);
        $output["datetime"] = str_replace("[", "", $tmp[0]);
        $output["message"] = trim($tmp[1]);

        return $output;
    }

    /**
     * We must sort the stats by chronological order
     */
    public function sortByChronological($a, $b): int
    {
        $tmp_datestring1 = (int) str_replace(" ", "", str_replace(":", "", str_replace("-", "", $a["datetime"])));
        $tmp_datestring2 = (int) str_replace(" ", "", str_replace(":", "", str_replace("-", "", $b["datetime"])));

        if ($tmp_datestring1 === $tmp_datestring2) {
            return 0;
        }

        return ($tmp_datestring1 < $tmp_datestring2) ? -1 : 1;
    }

    /** First we need to parse the input into viable numbers
     * Input like
     * [1518-11-01 00:00] Guard #10 begins shift
     * [1518-11-01 00:05] falls asleep
     * [1518-11-01 00:25] wakes up
     * [1518-11-01 00:30] falls asleep
     * [1518-11-01 00:55] wakes up
     *
     * Should perhaps be one parsed entry per shift.
     * [
     * "id" => "10",
     * "asleep" => [00:05,00:30],
     * "wakes" => [00:25,00:55]
     * ]
     */
    private function parseInputToShiftsArray(array $input): array
    {
        // Bundle the entries of a same shift with eachother
        $shift_array = [];
        $last_id = null;
        $tmp_shift = [];
        foreach ($input as $entry) {
            if (strpos($entry["message"], "Guard") !== false) {
                // This means new shift so save the old one
                if ($last_id !== null) {
                    $shift_array[] = $tmp_shift;
                }
                // Reset the temp shift
                $tmp_shift = [];
                // Get the id
                $last_id = explode(" ", explode("#", $entry["message"])[1])[0];
                $tmp_shift["id"] = $last_id;
            } elseif (strpos($entry["message"], "asleep") !== false) {
                // Check if we check this for the first time
                if (!isset($tmp_shift["asleep"])) {
                    $tmp_shift["asleep"] = [];
                }

                $tmp_shift["asleep"][] = explode(" ", $entry["datetime"])[1];
            } elseif (strpos($entry["message"], "wakes") !== false) {
                // Check if we check this for the first time
                if (!isset($tmp_shift["wakes"])) {
                    $tmp_shift["wakes"] = [];
                }

                $tmp_shift["wakes"][] = explode(" ", $entry["datetime"])[1];
            }
        }

        return $shift_array;
    }

    /**
     * A guard can be asleep, if the guard is sleeping on a minute we add this to the array
     * We up this count if he slept on this minute before
     *
     * [
     *  "<guard_id>" =>  [
     *      "<hours" => [
     *          "<minutes>" => int
     *      ]
     *  ]
     * ]
     */
    private function calcGuardStats(array &$guard_stats, array $guard_shifts): array
    {
        foreach ($guard_shifts as $shift) {
            if (!isset($guard_stats[$shift["id"]])) {
                $guard_stats[$shift["id"]] = [];
            }

            // We only check for guards that actually went asleep on the shift
            if (!empty($shift["asleep"])) {
                $amount_of_naps = \count($shift["asleep"]);

                // Loop through the naps
                for ($count = 0; $count < $amount_of_naps; $count++) {
                    // $shift["asleep"] <-> $shift["wakes"] is the time a guard sleeps
                    //$time[0]=hours $time[1]=min
                    $starttime = explode(":", $shift["asleep"][$count]);
                    $endtime = explode(":", $shift["wakes"][$count]);

                    // We only check which minute each guard is sleeping the most.
                    for ($subcount = (int) $starttime[1]; $subcount < (int) $endtime[1]; $subcount++) {
                        if (!isset($guard_stats[$shift["id"]][$subcount])) {
                            $guard_stats[$shift["id"]][$subcount] = 1;
                        } else {
                            $guard_stats[$shift["id"]][$subcount] += 1;
                        }
                    }
                }
            }
        }

        $results = [];
        $results2 = [];
        // return the id and the minute of the guard that has the most time sleeping aswell as the minute that was slept the most
        foreach ($guard_stats as $id => $stats) {
            // We only check guards that slept on the job
            if (!empty($stats)) {
                $total_min_slept = array_sum($stats);
                $max_min_freq = max($stats);
                $minute_asleep_at_most_of_the_time = array_search($max_min_freq, $stats, true);

                // Part 1 results
                if (empty($results)) {
                    $results["id"] = $id;
                    $results["minute_asleep_at_most_of_the_time"] = $minute_asleep_at_most_of_the_time;
                    $results["total_min_slept"] = $total_min_slept;
                } else {
                    if ($total_min_slept > $results["total_min_slept"]) {
                        $results["id"] = $id;
                        $results["minute_asleep_at_most_of_the_time"] = $minute_asleep_at_most_of_the_time;
                        $results["total_min_slept"] = $total_min_slept;
                    }
                }

                // Part 2 results
                if (empty($results2)) {
                    $results2["id"] = $id;
                    $results2["minute_asleep_at_most_of_the_time"] = $minute_asleep_at_most_of_the_time;
                    $results2["max_min_freq"] = $max_min_freq;
                } elseif ($max_min_freq > $results2["max_min_freq"]) {
                        $results2["id"] = $id;
                        $results2["minute_asleep_at_most_of_the_time"] = $minute_asleep_at_most_of_the_time;
                        $results2["max_min_freq"] = $max_min_freq;
                }
            }
        }

        return [$results, $results2];
    }
}
