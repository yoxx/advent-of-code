<?php

namespace yoxx\Advent\Y_2018;

use Symfony\Component\Console\Output\OutputInterface;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;
use yoxx\Advent\Day;

class Y2018D9 extends Day
{
    protected int $current_marble;
    /** @var Marble[] $marbles */
    protected array $marbles = [];
    protected array $player_scores = [];

    public function run(OutputInterface $logger, int $part, bool $test): void
    {
        $formatted_input = $this->getFormattedInput($logger);

        if ($part === RunAssignmentCommand::RUN_PART_1 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            /**
             * There are a couple of rules for this game to work:
             * 1. Each player takes a turn after the other player.
             * 2. The marble places must be placed between 1 and 2 clockwise of the current marble
             * 3. If the marble is a multiply of 23 this marble is taken for the score of the person and so is the 7th
             * marble before this. The marble directly clockwise of the removed marble is the new current marble.
             * 4.
             */
            $output = $this->playGame($formatted_input["players"],$formatted_input["last_marble"]);
            $logger->writeln("Part 1: Player: " . $output["winning_player"] . " wins the game with a highscore of " . $output["highscore"]);
        }

        if ($part === RunAssignmentCommand::RUN_PART_2 || $part === RunAssignmentCommand::RUN_PART_ALL) {
            // Up the memory limit for part 2
            ini_set('memory_limit','1024M');

            $output = $this->playGame($formatted_input["players"],$formatted_input["last_marble"]*100);
            $logger->writeln("Part 2: Player: " . $output["winning_player"] . " wins the game with a highscore of " . $output["highscore"]);
        }
    }

    public function runAssignment1(OutputInterface $output):void {}
    public function runAssignment2(OutputInterface $output):void {}

    private function getFormattedInput(OutputInterface $logger): array
    {
        $original_input = [];
        $handle = fopen($this->input_file, "rb");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                preg_match("/^(\d+) players; last marble is worth (\d+) points$/", trim($line), $match);
                $original_input["players"] = (int) $match[1];
                $original_input["last_marble"] = (int) $match[2];
            }
            fclose($handle);
        } else {
            $logger->writeln("Error reading line input from file");
        }

        return $original_input;
    }

    private function setupGame(int $players): void
    {
        // Clear general variables
        $this->player_scores = [];
        $this->marbles = [];

        // Create the scoreboard
        for ($player_count = 1; $player_count <= $players; $player_count++) {
            $this->player_scores[$player_count] = 0;
        }

        // Add the first marble to the game
        $marble = new Marble(0);
        $marble->setPrevious(0);
        $marble->setNext(0);
        $this->marbles[] = $marble;
        $this->current_marble = 0;
    }

    /**
     * Play a game of elvish marbles
     * Return an array with the name and highscore of the winning elf
     *
     * @return int
     */
    private function playGame(int $players, int $last_marble): array
    {
        $this->setupGame($players);
        $current_player = 1;
        for ($round = 1; $round <= $last_marble; $round++) {
            // Check the current player
            if ($current_player > $players) {
                $current_player = 1;
            }

            /**
             * If the $round can be multiplied by 23
             * We should add the roundscore to the current player
             * We should remove the 7th marble from the current marble and add that score aswell
             */
            if ($round % 23 === 0) {
                $this->player_scores[$current_player] += $round + $this->remove7thMarble();
            } else { // Else insert the marble
                $this->insertMarble(new Marble($round));
            }
            $current_player++;
        }

        // We went to the rounds time to determine the winning player and highscore!
        return ["winning_player" => array_keys($this->player_scores, max($this->player_scores))[0], "highscore" => max($this->player_scores)];
    }

    private function remove7thMarble(): int
    {
        $marble = $this->marbles[$this->current_marble];
        // we need to go 7 marbles back
        for ($count = 0; $count < 7; $count++) {
            $marble = $this->marbles[$marble->getPrevious()];
        }
        // By now we have the 7th marble and we need to remove this from the links
        // Before we remove this we have to make sure the links are okay
        $prev_marble = $this->marbles[$marble->getPrevious()];
        $next_marble = $this->marbles[$marble->getNext()];

        // Link up the prev and next marble
        $prev_marble->setNext($next_marble->getID());
        $next_marble->setPrevious($prev_marble->getID());

        // Unset the marble from our playingfield for sanity
        unset($this->marbles[$marble->getID()]);

        // Set the new current marble
        $this->current_marble = $next_marble->getID();

        // Return this marbles value
        return $marble->getID();
    }

    private function insertMarble(Marble $inserted_marble): void
    {
        // Add the marble to our list of fancy marbles
        $this->marbles[$inserted_marble->getID()] = $inserted_marble;
        // The marble places must be placed between 1 and 2 clockwise of the current marble
        $marble1 = $this->marbles[$this->marbles[$this->current_marble]->getNext()];
        $marble2 = $this->marbles[$marble1->getNext()];

        // Setup marble 1 to the inserted marble
        $marble1->setNext($inserted_marble->getID());
        $inserted_marble->setPrevious($marble1->getID());
        // Setup the inserted marble to marble 2
        $inserted_marble->setNext($marble2->getID());
        $marble2->setPrevious($inserted_marble->getID());

        // Set the new current marble
        $this->current_marble = $inserted_marble->getID();
    }
}

/**
 * Sub-class Marble
 */
class Marble {
    protected $marble_id;
    protected $previous;
    protected $next;

    public function __construct(int $marble_id)
    {
        $this->marble_id = $marble_id;
    }

    public function getID(): int
    {
        return $this->marble_id;
    }

    public function setPrevious(int $previous): void
    {
        $this->previous = $previous;
    }

    public function getPrevious(): int
    {
        return $this->previous;
    }

    public function setNext(int $next): void
    {
        $this->next = $next;
    }

    public function getNext(): int
    {
        return $this->next;
    }
}
