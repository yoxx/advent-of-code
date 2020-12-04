# Advent-Of-Code
Advent of Code is an Advent calendar of small programming puzzles for a variety of 
skill sets and skill levels that can be solved in any programming language you like.

For more information about the advent of code see https://adventofcode.com

## General Info
* This project is written in php
  * Current php version 7.4 

## Startup
* This project uses Docker
* First build the docker-compose images: ```docker-compose build```
* To install composer packages run ```docker-compose run --rm advent-composer install```
* To run a command run ```docker-compose run --rm advent-php <your-command>``` to start the development enviroment
* Simply run ```docker-compose run -rm advent-php src/cli.php``` and start hacking
  * ```run:day```
    * ```-y``` enter the year you want to execute a day from (default to current year)
    * ```-d``` enter the day you want to execute (default today)
    * ```-p``` enter the day-part you want to execute
    * ```-t``` run the test if a testfile exists in input files

##Years
### 2018
* First started with the Advent of Code, made and finished day 12

### 2019
* Setup docker enviroment to run the advent challenges
* PHP 7.2

### 2020
* PHP8.0[]

