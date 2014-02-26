<?php

namespace GeoIO\WKB\Generator\Exception;

class InvalidOptionException extends \InvalidArgumentException implements Exception
{
    public static function create($name, $value, $expected)
    {
        return new static(
            sprintf(
                'Invalid value for option %s passed: %s (Expected %s)',
                $name,
                json_encode($value),
                implode(', ', array_map('json_encode', (array) $expected))
            )
        );
    }
}
