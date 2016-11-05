<?php

namespace RedisAnalyze;

class DebugParser
{
    public function parse($string)
    {
        $debug = new Debug;

        $debugExploded = explode(' ', $string);

        foreach ($debugExploded as $debugPart) {
            $parse = explode(':', $debugPart);

            if (isset($parse[0]) && isset($parse[1])) {
                $debug->add($parse[0], $parse[1]);
            }
        }

        return $debug;
    }
}
