<?php
declare(strict_types=1);

namespace yoxx\Advent;

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use yoxx\Advent\ConsoleCommands\DownloadInputCommand;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

// Echo our latest PHP release
echo phpversion() . PHP_EOL;
// Run the CLI application
$application = new Application();
$application->add(new DownloadInputCommand());
$application->add(new RunAssignmentCommand());
$application->run();
