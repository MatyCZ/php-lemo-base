<?php

namespace Lemo\Base\Utility;

class Dump
{
    public static function dump(mixed $value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

    public static function dumpExit(mixed $value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        exit;
    }
}