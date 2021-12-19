<?php declare(strict_types=1);

namespace yoxx\Advent\Y_2021;

class Y2021D19_Scanner
{
    /** @var Y2021D19_Beacon[] $beacons  */
    public array $beacons = [];

    public function __construct(
        public int $id,
        public ?int $x = null,
        public ?int $y = null,
        public ?int $z = null
    ){}

    public function addBeacon(Y2021D19_Beacon $beacon):void
    {
        $this->beacons[] = $beacon;
    }

    public function compareScanner(Y2021D19_Scanner $other_scanner): ?Y2021D19_Scanner
    {
        // We use this function to compare THIS scanner with a different scanner
        $compare_orientation = [];
        foreach ($this->getAllPossibleOrientations() as $orientation) {
            $compare_orientation[implode(",",$orientation)] = [];
            foreach ($this->beacons as $ref_beacon) {
                foreach ($other_scanner->beacons as $oth_beacon) {
                    // Compare the ref beacon with the origin beacon x,y,z
                    if (EH?!) {
                        // we have a match thus add it to the array above
                        $compare_orientation[implode(",",$orientation)][] = $oth_beacon;
                    }
                }
            }
            // Check if we found 12 or more matches
            if (count($compare_orientation[implode(",",$orientation)]) >= 12) {
                // In this orientation atleast 12 beacons match
                // Set position? where do I get the other scanner position from?
//                $other_scanner->alignToReference();
                return $other_scanner;
            }
        }
        // Well if we end up here we simply found no match
        return null;
    }

    public function alignToReference(Y2021D19_Scanner $reference_scanner): void
    {
        // TODO write a function to permanently "align" our scanner to our reference scanner
    }

    public function getAllPossibleOrientations(): array
    {
        /**
         * 000 - no change == no county
         * 001 | 00-1
         * 010 | 0-10
         * 100 | -100
         * 011 | 0-11 | 01-1 | 0-1-1
         * 101 | -101 | 10-1 | -10-1
         * 110 | -110 | 1-10 | -1-10
         * 111 | -111 | -1-11 | -11-1 | 11-1 | -1-1-1
         */
        return [
            // 001 | 00-1
            ["x" => 0, "y" => 0, "z" => -1],
            ["x" => 0, "y" => 0, "z" => 1],
            // 010 | 0-10
            ["x" => 0, "y" => -1, "z" => 0],
            ["x" => 0, "y" => 1, "z" => 0],
            // 100 | -100
            ["x" => -1, "y" => 0, "z" => 0],
            ["x" => 1, "y" => 0, "z" => 0],
            // 011 | 0-11 | 01-1 | 0-1-1
            ["x" => 0, "y" => 1, "z" => 1],
            ["x" => 0, "y" => -1, "z" => 1],
            ["x" => 0, "y" => 1, "z" => -1],
            ["x" => 0, "y" => -1, "z" => -1],
            // 101 | -101 | 10-1 | -10-1
            ["x" => 1, "y" => 0, "z" => 1],
            ["x" => -1, "y" => 0, "z" => 1],
            ["x" => 1, "y" => 0, "z" => -1],
            ["x" => -1, "y" => 0, "z" => -1],
            // 110 | -110 | 1-10 | -1-10
            ["x" => 1, "y" => 1, "z" => 0],
            ["x" => -1, "y" => 1, "z" => 0],
            ["x" => 1, "y" => -1, "z" => 0],
            ["x" => -1, "y" => -1, "z" => 0],
            // 111 | -111 | -1-11 | -11-1 | 11-1 | -1-1-1
            ["x" => 1, "y" => 1, "z" => 1],
            ["x" => -1, "y" => 1, "z" => 1],
            ["x" => -1, "y" => -1, "z" => 1],
            ["x" => -1, "y" => 1, "z" => -1],
            ["x" => 1, "y" => 1, "z" => -1],
            ["x" => -1, "y" => -1, "z" => -1],
        ];
    }
}
