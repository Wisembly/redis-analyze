<?php

namespace RedisAnalyze\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

use Predis\Client as Redis;
use Predis\Collection\Iterator\Keyspace;
use Predis\Collection\Iterator\HashKey;

class CleanUserStatisticsCommand extends Command
{
    protected function configure()
    {
        $this->setName('redis:clean-user-statistics');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $redis = new Redis;

        $scanIterator = new Keyspace($redis, "solid:user:*:statistics:week", 1000);

        foreach ($scanIterator as $key) {
            $hashIterator = new HashKey($redis, $key, "2015:*");
            $fields = [];

            foreach ($hashIterator as $field => $value) {
                $fields[] = $field;
            }

            if (empty($fields)) {
                continue;
            }

            array_unshift($fields, $key);
            call_user_func_array([$redis, 'hdel'], $fields);
        }
    }
}
