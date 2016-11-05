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

use RedisAnalyze\ScanCollection;
use RedisAnalyze\ScanAnalyzer;
use RedisAnalyze\Dumper\CSV;

/**
 * Scan a Redi database using the SCAN command.
 *
 * @see http://redis.io/commands/scan
 *
 * For a faster scanning, use an appropriate COUNT option.
 * For example, on a 100k keys database, without COUNT option the scanning takes 2 minutes.
 * Using a COUNT setted to 1000, it takes 1,5 seconds.
 */
class RedisCommand extends Command
{
    protected function configure()
    {
        $this->setName('redis:analyze');
        $this->setDescription('Analyze Redis depending on inputs.');
        $this->addOption('match', null, InputOption::VALUE_REQUIRED, 'Analyze the keys that match the given glob-style pattern.');
        $this->addOption('count', null, InputOption::VALUE_REQUIRED, 'Handle the number of elements that SCAN provides at every iteration.');
        $this->addOption('dump-csv', null, InputOption::VALUE_NONE, 'You can dump as a CSV the scanning keys.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $redis = new Redis;
        $csvDumper = new CSV;

        $matchOption = $input->getOption('match');
        $countOption = $input->getOption('count');

        $scanCollection = new ScanCollection($matchOption, $countOption);

        $stopwatch = new Stopwatch;
        $stopwatch->start('scan');

        $io->section('Scanning the Redis database');

        if (null !== $matchOption) {
            $io->note(sprintf('keys matching %s glob-style pattern.', $matchOption));
        }

        if (null !== $countOption) {
            $io->note(sprintf('SCAN\'s COUNT option setted to %d.', $countOption));
        }

        do {
            $scan = call_user_func_array([$redis, 'scan'], $scanCollection->getScanParameters());
            $scanCollection->add($scan[1]);
            $scanCollection->updateCursor($scan[0]);
        } while(!$scanCollection->isTerminated());

        $event = $stopwatch->stop('scan');

        $io->text(sprintf('<info>%d keys scanned in %d ms.</info>', $scanCollection->count(), $event->getDuration()));

        if (true === $input->getOption('dump-csv')) {
            $csvDumper->dump($scanCollection);
        }

        $scanAnalyzer = new ScanAnalyzer($redis, $scanCollection);
        $analyze = $scanAnalyzer->analyze();
        $csvDumper->dump($analyze);
    }
}
