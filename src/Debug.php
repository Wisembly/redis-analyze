<?php

namespace RedisAnalyze;

class Debug
{
    // @see https://github.com/antirez/redis/blob/4082c38a60eedd524c78ef48c1b241105f4ddc50/src/rdb.c#L663-L671
    private $serializedLength;

    public function add($key, $value)
    {
        switch ($key) {
            case 'serializedlength':
                $this->serializedLength = $value;
                break;
        }
    }

    public function getSerializedLength()
    {
        return $this->serializedLength;
    }
}
