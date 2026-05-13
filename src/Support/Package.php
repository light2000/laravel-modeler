<?php

namespace Light2000\Modeler\Support;

class Package
{
    public static function path(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function stubLatestJsonPath(): string
    {
        return self::path().'/stubs/latest.json';
    }
}
