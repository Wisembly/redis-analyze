<?php

namespace RedisAnalyze\Dumper;

use Symfony\Component\Filesystem\Filesystem;

use RedisAnalyze\ScanCollection;
use RedisAnalyze\Analyze;

class CSV
{
    public function dump($content)
    {
        switch (true) {
            case $content instanceof ScanCollection:
                $this->handleScanCollection($content);

                break;

            case $content instanceof Analyze:
                $this->handleAnalyze($content);

                break;

            default:
                $this->write($content, 'dump');
        }
    }

    private function handleScanCollection(ScanCollection $scanCollection)
    {
        $csv = '';

        foreach ($scanCollection->getCollection() as $key) {
            $csv .= $key . "\n";
        }

        $this->write($csv, 'keys');
    }

    private function handleAnalyze(Analyze $analyze)
    {
        $csv = "avgSerializedLength,maxSerializedLentgh,minSerializedLength\n";
        $csv .= $analyze->getAverageSerializedLength() . ',' . $analyze->getMaxSerializedLength() . ',' . $analyze->getMinSerializedLength() . "\n";
        $this->write($csv, 'analyze');
    }

    private function write($content, $fileName)
    {
        $fileSystem = new Filesystem;
        $fileSystem->dumpFile($fileName . '.csv', $content);
    }
}
