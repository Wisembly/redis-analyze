<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

use RedisAnalyze\Command\RedisCommand;
use RedisAnalyze\Command\MassGeneratorCommand;
use RedisAnalyze\Command\CleanUserStatisticsCommand;

$application = new Application();
$application->add(new RedisCommand);
$application->add(new MassGeneratorCommand);
$application->add(new CleanUserStatisticsCommand);
$application->run();
