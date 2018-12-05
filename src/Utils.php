<?php

namespace yoxx\Advent;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;
use yoxx\Advent\UtilClasses\AsyncOperation;

class Utils
{
    /**
     * Function to retrieve values from our .env file
     */
    public static function getEnvValue(string $key): string
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../.env');

        return getenv($key);
    }

    /**
     * Function to run a single function as async operation
     */
    public static function runAsyncOperation(OutputInterface $logger, Day $object, string $function, array $parameters): void
    {
        $logger->writeln("Currently not workable with threads in php 7.1 will need to update to 7.2 first");
        die();
//        $thread = new AsyncOperation($logger, $object, $function, $parameters);
//        $thread->start();
    }

    /**
     * Function to run a multiple function as async operation
     */
    public static function runMultiAsyncOperation(OutputInterface $logger, Day $object, array $functions, array $parameters_bag): void
    {
        $logger->writeln("Currently not workable with threads in php 7.1 will need to update to 7.2 first");
        die();
//        $amount_of_functions = \count($functions);
//        $threads = [];
//        // Create the Thread objects
//        for ($count = 0; $count < $amount_of_functions; $count++) {
//            $threads[] = new AsyncOperation($logger, $object, $functions[$count], $parameters_bag[$count]);
//        }
//
//        // Start the threads
//        foreach ($threads as $thread) {
//            $thread->start();
//        }
    }
}