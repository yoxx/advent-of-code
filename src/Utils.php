<?php

namespace yoxx\Advent;

use Symfony\Component\Dotenv\Dotenv;

class Utils
{
    public static function memoryIntensive1G(): void
    {
        // This function is mempry intesive upping memory to 1G
        ini_set('memory_limit', '1G');
    }

    /**
     * Function to retrieve values from our .env file
     */
    public static function getEnvValue(string $key): string
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ .'/../.env');

        return $_ENV[$key];
    }
}
