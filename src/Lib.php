<?php

namespace Gendiff\Lib;

use Gendiff\KeyDiff;

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
    $data = file_get_contents($filePath);
    return json_decode($data, true);
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
        $acc[] = new KeyDiff($key, $firstCollection, $secondCollection);
        return $acc;
    }, []);
}

function createView(array $keysDiff)
{
    return "{" . PHP_EOL
        . implode(PHP_EOL, $keysDiff) . PHP_EOL
        . "}";
}
