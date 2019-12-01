<?php

namespace yoxx\Advent;

use Symfony\Component\Dotenv\Dotenv;

class Utils
{
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
