<?php

namespace RedisAnalyze\Command;

use InvalidArgumentException;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

use Clue\Redis\Protocol;

class MassGeneratorCommand extends Command
{
    protected function configure()
    {
        $this->setName('redis:mass_generator');
        $this->setDescription('Generate mass fake data for mass insertions.');
        $this->addOption('count', null, InputOption::VALUE_REQUIRED, 'The number of fake values must be generated.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('mass_generation');

        $factory = new Protocol\Factory();
        $parser = $factory->createResponseParser();
        $serializer = $factory->createSerializer();

        $content = "";

        $count = (int) $input->getOption('count');

        if (0 >= $count) {
            throw new InvalidArgumentException('The `count` option should be positive integer.');
        }

        for ($i = 1; $i <= $count; $i++) {
            $content .= $serializer->getRequestMessage('SET', ['key' . $i, $i]);
        }

        $fileSystem = new Filesystem;
        $fileSystem->dumpFile('mass.txt', $content);

        $event = $stopwatch->stop('mass_generation');

        $output->writeln(sprintf('<info>Mass insertion generated in %d ms.</info>', $event->getDuration()));
    }
}
