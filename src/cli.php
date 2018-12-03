<?php
declare(strict_types=1);

namespace yoxx\Advent;

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use yoxx\Advent\ConsoleCommands\DownloadInputCommand;
use yoxx\Advent\ConsoleCommands\RunAssignmentCommand;

$application = new Application();

$application->add(new DownloadInputCommand());
$application->add(new RunAssignmentCommand());

$application->run();