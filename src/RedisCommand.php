<?php

namespace RedisAnalyze;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Predis\Client as Redis;

class RedisCommand extends Command
{
    protected function configure()
    {
        $this->setName('redis:analyze');
        $this->setDescription('Analyze Redis depending on inputs.');
        $this->addOption('key', null, InputOption::VALUE_REQUIRED, 'Analyze this specific key.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redis = new Redis;
        $scanCollection = new ScanCollection;

        do {
            $scan = $redis->scan($scanCollection->getCursor(), 'COUNT', 1);
            $scanCollection->add($scan[1]);
            $scanCollection->updateCursor($scan[0]);
        } while(!$scanCollection->isTerminated());

        $fileSystem = new Filesystem;
        $fileSystem->dumpFile('test.txt', $scanCollection->dump());
    }
}
