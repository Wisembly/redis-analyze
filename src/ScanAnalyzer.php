<?php

namespace RedisAnalyze;

use Predis\Client as Redis;

use Symfony\Component\Filesystem\Filesystem;

class ScanAnalyzer
{
    private $redis;
    private $scanCollection;
    private $debugParser;

    public function __construct(Redis $redis, ScanCollection $scanCollection)
    {
        $this->redis = $redis;
        $this->scanCollection = $scanCollection;
        $this->debugParser = new DebugParser;
    }

    public function analyze()
    {
        $analyze = new Analyze;

        foreach ($this->scanCollection->getCollection() as $key) {
            $debug = $this->debugParser->parse($this->redis->executeRaw(['debug', 'object', $key]));
            $analyze->addDebug($debug);
        }

        return $analyze;
    }

    // // @see http://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
    // public function formatSizeUnits($bytes)
    // {
    //     $unit = 'b';

    //     if ($bytes >= 1073741824) {
    //         $bytes = number_format($bytes / 1073741824, 2);
    //         $unit = 'Gb';
    //     }
    //     elseif ($bytes >= 1048576) {
    //         $bytes = number_format($bytes / 1048576, 2);
    //         $unit = 'Mb';
    //     }
    //     elseif ($bytes >= 1024) {
    //         $bytes = number_format($bytes / 1024, 2);
    //         $unit = 'Kb';
    //     }
    //     else {
    //         $unit = 'b';
    //     }

    //     return [$bytes, $unit];
    // }
}
