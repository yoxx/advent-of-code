# Advent-Of-Code
Advent of Code is an Advent calendar of small programming puzzles for a variety of 
skill sets and skill levels that can be solved in any programming language you like.

For more information about the advent of code see https://adventofcode.com

## General Info
* This project is written in php
  * Current php version 8.1
* I like to do the advent puzzles for as long as I have time, usually it gets busier during the december month. 
  This will result in me picking my battles and focussing on other things. I do it for fun anyways.

## Startup
* This project uses Docker
* First build the docker-compose images: ```docker-compose build```
* To install composer packages run ```docker-compose run --rm advent-composer install```
* To run a command run ```docker-compose run --rm advent-php <your-command>``` to start the development environment
* Simply run ```docker-compose run --rm advent-php src/cli.php``` and start puzzling
  * ```run:day```
    * ```-y``` enter the year you want to execute a day from (default to current year)
    * ```-d``` enter the day you want to execute (default today)
    * ```-p``` enter the day-part you want to execute
    * ```-t``` run the test if a test file exists in input files
* If you want to download the input files for yourself
  * Copy the .env.example to .env ```cp .env.example .env```
  * Login at adventofcode.com and retrieve the session value from your cookie
  * Run the following command:
    * ```input:download```
      * ```-y``` enter the year you want to execute a day from (default to current year) it is a number ranging from 2000-2099 and not above our current year
      * ```-d``` enter the day you want to execute (default today) it is a number ranging from 1-25

##Years
### 2018
* First started with the Advent of Code, made and finished day 12

### 2019
* Setup docker environment to run the advent challenges
* PHP 7.4
* Made it to day 10

### 2020
* PHP 8.0
* Made it to day 8

### 2021
* PHP 8.1
* Updated composer packages and install composer/xdebug using [install-php-extensions](https://github.com/mlocati/docker-php-extension-installer)
* Did a little cleanup

### 2022
* This year I will be trying AOC in Golang. The Go repo can be found here [AOC-GOLANg](https://github.com/yoxx/aoc-go)

