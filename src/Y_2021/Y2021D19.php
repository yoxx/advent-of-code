<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\Day;

class Y2021D19 extends Day
{
    public function runAssignment1(OutputInterface $output): void
    {
        $output->writeln("P1: The solution is: " . $this->determineUniqueBeacons($this->parseScannerData()));
    }

    public function runAssignment2(OutputInterface $output): void
    {
//        $output->writeln("P2: The solution is: " . $largest_magnitude);
    }

    public function determineScannerAbsolutePositionsAndAlign(array $scanners): array
    {
        /** @var Y2021D19_Scanner $reference_scanner_0 */
        $reference_scanner_0 = array_shift($scanners);
        $known_scanners = [$reference_scanner_0->id => $reference_scanner_0];

        while (!empty($scanners)) {
            foreach ($known_scanners as $known_scanner) {
                foreach ($scanners as $skey => $scanner) {
                    // Try our comparison
                    $scanner_with_loc = $known_scanner->compareScanner($scanner);
                    // We found an absolute location for this scanner
                    if ($scanner_with_loc) {
                        // Add him to the array
                        $known_scanners[$scanner_with_loc->id] = $scanner_with_loc;
                        // Unset from our workload
                        unset($scanners[$skey]);
                    }
                }
            }
        }
        return $known_scanners;
    }

    public function determineUniqueBeacons(array $scanners): int
    {
        // TODO determine the scanner absolute positions and align them with the reference (also called rotate)
        $aligned_scanners = $this->determineScannerAbsolutePositionsAndAlign($scanners);
        // TODO determine the unique number of beacons
        return 0; // since we currently know SHIT
    }

    /**
     * @return Y2021D19_Scanner[]
     */
    public function parseScannerData(): array
    {
        $scanners = [];
        $scanner = null;
        $input = $this->getInputArray();
        foreach ($input as $line) {
            // if my string contains something like "--- scanner" parse the ID and create a new scanner
            if (str_contains($line, "--- scanner ")) {
                // Save our old scanner if any
                if ($scanner !== null) {
                    $scanners[] = $scanner;
                }
                // Now fetch the new scanner ID and create a new scanner
                $id = (int)str_replace(["--- scanner ", " ---"], "", $line);
                if ($id === 0) {
                    $scanner = new Y2021D19_Scanner($id, 0, 0, 0);
                } else {
                    $scanner = new Y2021D19_Scanner($id);
                }
            } else {
                // Must be beacon cords or empty string
                if ($line !== "\n") {
                    $cords = explode(",", trim($line));
                    if (!empty($cords)) {
                        $scanner->addBeacon(new Y2021D19_Beacon((int)$cords[0], (int)$cords[1], (int)$cords[2]));
                    }
                }
            }
        }
        // We are done add the last scanner to the array
        $scanners[] = $scanner;
        // Return our scanners
        return $scanners;
    }
}
