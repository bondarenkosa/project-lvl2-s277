<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParser(string $format)
{
    $parsers = [
        'json' => function ($data) {
            return json_decode($data, true);
        },
        'yml' => function ($data) {
            return Yaml::parse($data);
        }
    ];

    return $parsers[$format];
}
