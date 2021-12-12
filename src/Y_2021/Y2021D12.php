<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;
use yoxx\Advent\Utils;

class Y2021D12 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $node_map = $this->generateCaveMap();
        $output->writeln("P1: The solution is: " . $this->calculateAmountOfRoutes($node_map));
    }

    public function runAssignment2(OutputInterface $output): void
    {
        $node_map = $this->generateCaveMap();
        $output->writeln("P2: The solution is: " . $this->calculateAmountOfRoutes($node_map, true));
    }

    public function calculateAmountOfRoutes(array $node_map, bool $p2 = false): int
    {
        // We always start at start and end at end
        // We can visit lowercase caves only once per route
        // We can visit UPPERcase caves multiple times per route
        // start and end are considered lowercase
        $total_amount_of_routes = 0;
        $paths = [];
        $this->findPath("start", $node_map, [], $paths, $p2);
        return count($paths);
    }

    public function findPath(string $index, array $map, array $cur_path, array &$paths, bool $p2 = false): void
    {
        // We end and save our path if we reach the end
        if ($index === "end") {
            $cur_path[] = $index;
            $paths[] = $cur_path;
            return;
        }
        // Check if we have a lowercase char
        if (ctype_lower($index)) {
            if ($p2) {
                // check if we already have this index in our path if so return for an early out as we can only visit start once
                if ($index === "start" && in_array($index, $cur_path, true)) {
                    return;
                }
                // One lower case caves twice
                if (in_array($index, $cur_path, true)) {
                    // we allow the first lower cave to go to twice the we do not allow this anymore by going back to p1 fun
                    $p2 = false;
                }
            } else {
                // check if we already have this index in our path if so return for an early out as we can only visit lowecase nodes once
                if (in_array($index, $cur_path, true)) {
                    return;
                }
            }
        }
        foreach($map[$index] as $possible_route) {
            if (end($cur_path) !== $index) {
                $cur_path[] = $index;
            }
            $this->findPath($possible_route, $map, $cur_path, $paths, $p2);
        }
    }

    public function generateCaveMap(): array
    {
        $sub_routes = [];
        // We generate a list of all connected nodes per node
        $input = $this->getInputArray(true, "-");
        foreach($input as $node_connection) {
            // Left uniques
            if (!isset($sub_routes[trim($node_connection[0])])) {
                $sub_routes[trim($node_connection[0])] = [trim($node_connection[1])];
            } else {
                $sub_routes[trim($node_connection[0])][] = trim($node_connection[1]);
            }
            // Right uniques
            if (!isset($sub_routes[trim($node_connection[1])])) {
                $sub_routes[trim($node_connection[1])] = [trim($node_connection[0])];
            } else {
                $sub_routes[trim($node_connection[1])][] = trim($node_connection[0]);
            }
        }
        return $sub_routes;
    }
}
