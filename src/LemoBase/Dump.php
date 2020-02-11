<?php

namespace LemoBase;

class Dump
{
    public static function dump($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

    public static function dumpExit($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        exit;
    }
}