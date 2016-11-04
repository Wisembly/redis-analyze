<?php

namespace RedisAnalyze;

class ScanCollection
{
    private $cursor = 0;
    private $count = null;
    private $match = null;
    private $collection = [];

    public function __construct($match = null, $count = null)
    {
        $this->match = $match;
        $this->count = $count;
    }

    public function getScanParameters()
    {
        $parameters = [$this->cursor];

        if (null !== $this->match) {
            $parameters = array_merge($parameters, ['MATCH', $this->match]);
        }

        if (null !== $this->count) {
            $parameters = array_merge($parameters, ['COUNT', (int) $this->count]);
        }

        return $parameters;
    }

    public function getCursor()
    {
        return $this->cursor;
    }

    public function updateCursor($update)
    {
        $this->cursor = (int) $update;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function add($elements)
    {
        if (!is_array($elements)) {
            $elements = (array) $elements;
        }

        $this->collection = array_merge($this->collection, $elements);
    }

    /**
     * Regarding the documentation, the iteration is concidered as finished when the cursor returns to 0.
     *
     * @see http://redis.io/commands/scan#scan-basic-usage
     * @return boolean
     */
    public function isTerminated()
    {
        return 0 === $this->cursor;
    }

    public function dumpCSV()
    {

    }
}
