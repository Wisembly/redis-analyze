<?php

namespace RedisAnalyze;

class Analyze
{
    private $sumSerializedLength = 0;
    private $countSerializedLength = 0;
    private $maxSerializedLength = 0;
    private $minSerializedLength = 0;

    public function addDebug(Debug $debug)
    {
        $serializedLength = $debug->getSerializedLength();

        $this->sumSerializedLength += $serializedLength;
        $this->countSerializedLength++;

        if ($serializedLength > $this->maxSerializedLength) {
            $this->maxSerializedLength = $serializedLength;
        }

        if (0 === $this->minSerializedLength || $serializedLength < $this->minSerializedLength) {
            $this->minSerializedLength = $serializedLength;
        }
    }

    public function getAverageSerializedLength()
    {
        return 0 !== $this->countSerializedLength ? round($this->sumSerializedLength / $this->countSerializedLength, 2) : 0;
    }

    public function getMaxSerializedLength()
    {
        return $this->maxSerializedLength;
    }

    public function getMinSerializedLength()
    {
        return $this->minSerializedLength;
    }
}
