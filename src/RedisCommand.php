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
        $this->addOption('match', null, InputOption::VALUE_REQUIRED, 'Analyze the keys that match the given glob-style pattern.');
        $this->addOption('count', null, InputOption::VALUE_REQUIRED, 'Handle the number of elements that SCAN provides at every iteration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redis = new Redis;
        $scanCollection = new ScanCollection(
            $input->getOption('match'),
            $input->getOption('count')
        );

        do {
            var_dump($scanCollection->getScanParameters());
            $scan = call_user_func_array([$redis, 'scan'], $scanCollection->getScanParameters());
            $scanCollection->add($scan[1]);
            $scanCollection->updateCursor($scan[0]);
        } while(!$scanCollection->isTerminated());

        var_dump($scanCollection->getCollection());

        // $fileSystem = new Filesystem;
        // $fileSystem->dumpFile('test.txt', $scanCollection->dump());
    }
}
