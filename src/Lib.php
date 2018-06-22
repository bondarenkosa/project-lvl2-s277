<?php

namespace Gendiff\Lib;

use function Gendiff\Parsers\getParser;
use function Gendiff\KeyDiff\makeKeyDiff;

function genDiff(string $firstFile, string $secondFile, string $format = "pretty")
{
    $firstCollection = getCollection($firstFile);
    $secondCollection = getCollection($secondFile);
    $keys = getKeys($firstCollection, $secondCollection);
    $keysDiff = getKeysDiff($keys, $firstCollection, $secondCollection);

    return createView($keysDiff);
}

function getCollection(string $filePath)
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = file_get_contents($filePath);
    $parse = getParser($extension);

    return $parse($data);
}

function getKeys(array $firstCollection, array $secondCollection)
{
    return array_keys(
        array_merge($firstCollection, $secondCollection)
    );
}

function getKeysDiff(array $keys, array $firstCollection, array $secondCollection)
{
    return array_reduce($keys, function ($acc, $key) use ($firstCollection, $secondCollection) {
        $acc[] = makeKeyDiff($key, $firstCollection, $secondCollection);
        return $acc;
    }, []);
}

function createView(array $keysDiff)
{
    $arrayOfStrings = array_map("\Gendiff\KeyDiff\keyDiffToString", $keysDiff);
    return "{" . PHP_EOL
        . implode(PHP_EOL, $arrayOfStrings) . PHP_EOL
        . "}";
}
