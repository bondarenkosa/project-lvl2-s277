<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

const PARSERS = [
    'json' => __NAMESPACE__ . "\jsonParse",
    'yaml' => __NAMESPACE__ . "\yamlParse",
    'yml' => __NAMESPACE__ . "\yamlParse"
];

function getParser(string $format)
{
    return PARSERS[$format]();
}

function jsonParse()
{
    return function ($data) {
        return json_decode($data, true);
    };
}

function yamlParse()
{
    return function ($data) {
        return (array) Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
    };
}
