<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

use RedisAnalyze\RedisCommand;

$application = new Application();
$application->add(new RedisCommand);
$application->run();
