<?php

namespace yoxx\Advent\UtilClasses;

use Symfony\Component\Console\Output\OutputInterface;
use Thread;
use yoxx\Advent\Day;

class AsyncOperation extends Thread {

    protected $logger;
    protected $object;
    protected $function;
    protected $parameters;

    public function __construct(OutputInterface $logger, Day $object, string $function, array $parameters) {
        $this->logger = $logger;
        $this->object = $object;
        $this->function = $function;
        $this->parameters = $parameters;
    }

    public function run() {
        $this->logger->writeln("Running function " . $this->function . " in a seperate Thread");
        $this->object->{$this->function}($this->parameters);
    }
}