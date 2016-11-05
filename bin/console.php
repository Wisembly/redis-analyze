<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

use RedisAnalyze\Command\RedisCommand;
use RedisAnalyze\Command\MassGeneratorCommand;

$application = new Application();
$application->add(new RedisCommand);
$application->add(new MassGeneratorCommand);
$application->run();
