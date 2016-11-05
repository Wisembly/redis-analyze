<?php

namespace RedisAnalyze\Dumper;

use Symfony\Component\Filesystem\Filesystem;

use RedisAnalyze\ScanCollection;

class CSV
{
    public function dump($content)
    {
        if ($content instanceof ScanCollection) {
            $this->handleScanCollection($content);
        }
    }

    private function handleScanCollection(ScanCollection $scanCollection)
    {
        $csv = '';

        foreach ($scanCollection->getCollection() as $key) {
            $csv .= $key . "\n";
        }

        $fileSystem = new Filesystem;
        $fileSystem->dumpFile('keys.csv', $csv);
    }
}
