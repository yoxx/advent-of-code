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
                    // Perhaps its better to calculate the scanner position.
                    // If we know the position... We can count the times a position is matched
                    // HOW DO I CALCULTATE THE POSITION OF A SCANNER?
                    // Positions are relative to the scanner, but we do know position of the ref beacon
                    // What if we simply calculate for ALL beacons asuming they are on the same position?
                    // If they are above 12 im probably right?
                    [$ob_x, $ob_y, $ob_z] = $this->handleRotation($oth_beacon->x, $oth_beacon->y, $oth_beacon->z, $orientation);

                    // Im not sure why my other scanner position is incorrect. Need to debug.
                    $other_scanner_pos = [$ob_x + $ref_beacon->x, $ob_y + $ref_beacon->y, $ob_z + $ref_beacon->z];
                    if (!isset($compare_orientation[implode(",", $orientation)][implode(",", $other_scanner_pos)])) {
                        $compare_orientation[implode(",", $orientation)][implode(",", $other_scanner_pos)] = 1;
                    } else {
                        $compare_orientation[implode(",", $orientation)][implode(",", $other_scanner_pos)]++;
                    }
                }
            }
            // Check if we found 12 or more matches
            foreach($compare_orientation[implode(",",$orientation)] as $scanner_pos => $potential_match) {
                if ($potential_match >= 12) {
                    // In this orientation atleast 12 beacons match
                    // Set position? where do I get the other scanner position from?
//                $other_scanner->alignToReference();
                    return $other_scanner;
                }
            }
        }
        // Well if we end up here we simply found no absolute location
        return null;
    }

    public function handleRotation(int $x, int $y, int $z, array $orientation): array
    {
        if ($orientation["x"] !== 0) {
            [$x, $y, $z] = $this->rotateX($orientation["x"], $x, $y, $z);
        }
        if ($orientation["y"] !== 0) {
            [$x, $y, $z] = $this->rotateY($orientation["y"], $x, $y, $z);
        }
        if ($orientation["z"] !== 0) {
            [$x, $y, $z] = $this->rotateZ($orientation["z"], $x, $y, $z);
        }
        return [$x, $y, $z];
    }

    public function rotateX(int $direction, int $x, int $y, int $z): array
    {
        // returning x,y,z
        if ($direction === 1) {
            return [$x, $z*-1, abs($y)];
        }
        return [$x, abs($z), $y-1];
    }

    public function rotateY(int $direction, int $x, int $y, int $z): array
    {
        // returning x,y,z
        if ($direction === 1) {
            return [$z *-1, $y, abs($x)];
        }
        return [abs($z), $y, $x*-1];
    }

    public function rotateZ(int $direction, int $x, int $y, int $z): array
    {
        // returning x,y,z
        if ($direction === 1) {
            return [abs($y), $x*-1, $z];
        }
        return [$y*-1, abs($x), $z];
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
