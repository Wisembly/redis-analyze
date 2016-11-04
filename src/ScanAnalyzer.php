<?php

namespace RedisAnalyze;

use Predis\Client as Redis;

use Symfony\Component\Filesystem\Filesystem;

class ScanAnalyzer
{
    private $redis;
    private $scanCollection;

    public function __construct(Redis $redis, ScanCollection $scanCollection)
    {
        $this->redis = $redis;
        $this->scanCollection = $scanCollection;
    }

    public function dumpCSV($filePath = __DIR__)
    {
        $lengths = [];
        $maxLength = 0;
        $minLength = 0;
        $csv = '';

        foreach ($this->scanCollection->getCollection() as $key) {
            $debug = $this->redis->executeRaw(['debug', 'object', $key]);
            $debugExploded = explode(' ', $debug);
            $debugParsed = [];

            foreach ($debugExploded as $debugPart) {
                $parse = explode(':', $debugPart);

                if (isset($parse[0]) && isset($parse[1])) {
                    $debugParsed[$parse[0]] = $parse[1];
                }
            }

            // @see https://github.com/antirez/redis/blob/4082c38a60eedd524c78ef48c1b241105f4ddc50/src/rdb.c#L663-L671
            if (isset($debugParsed['serializedlength'])) {
                $lengths[] = $debugParsed['serializedlength'];

                list($formatedLength, $unit)  = $this->formatSizeUnits($debugParsed['serializedlength'], 1);

                if ($maxLength < $debugParsed['serializedlength']) {
                    $maxLength = $debugParsed['serializedlength'];
                }

                if (0 === $minLength || $minLength > $debugParsed['serializedlength']) {
                    $minLength = $debugParsed['serializedlength'];
                }

                $csv .= $formatedLength . ',' . $unit . ',' . $key . "\n";
            }
        }

        list($maxFormated, $maxUnit) = $this->formatSizeUnits($maxLength);
        list($minFormated, $minUnit) = $this->formatSizeUnits($minLength);
        list($avgFormated, $avgUnit) = 0 !== count($lengths) ? $this->formatSizeUnits(round(array_sum($lengths) / count($lengths), 2)) : [0, 'b'];

        $csv .= "Max: " . $maxFormated . " " . $maxUnit . "\n";
        $csv .= "Min: " . $minFormated . " " . $minUnit . "\n";
        $csv .= "Avg: " . $avgFormated . " " . $avgUnit;

        $fileSystem = new Filesystem;
        $fileSystem->dumpFile('test.csv', $csv);
    }

    // @see http://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
    public function formatSizeUnits($bytes)
    {
        $unit = 'b';

        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2);
            $unit = 'Gb';
        }
        elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2);
            $unit = 'Mb';
        }
        elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2);
            $unit = 'Kb';
        }
        else {
            $unit = 'b';
        }

        return [$bytes, $unit];
    }
}
