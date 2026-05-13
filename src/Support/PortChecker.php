<?php

namespace Light2000\Modeler\Support;

class PortChecker
{
    public static function isInUse(string $host, int $port, float $timeout = 1.0): bool
    {
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (is_resource($socket)) {
            fclose($socket);

            return true;
        }

        return false;
    }
}
