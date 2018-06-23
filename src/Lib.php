<?php

namespace Gendiff;

use function Gendiff\Parsers\getParser;
use function Gendiff\KeyDiff\makeKeyDiff;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty")
{
    $dataBefore = getParsedFileData($pathToFile1);
    $dataAfter = getParsedFileData($pathToFile2);
    $keys = getUniqueKeys($dataBefore, $dataAfter);
    $keysDiff = getKeysDiff($keys, $dataBefore, $dataAfter);

    return createView($keysDiff);
}

function getParsedFileData(string $filePath)
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = file_get_contents($filePath);
    $parse = getParser($extension);

    return $parse($data);
}

function getUniqueKeys(array $dataBefore, array $dataAfter)
{
    $keys = array_merge(array_keys($dataBefore), array_keys($dataAfter));

    return array_unique($keys);
}

function getKeysDiff(array $keys, array $dataBefore, array $dataAfter)
{
    return array_reduce($keys, function ($acc, $key) use ($dataBefore, $dataAfter) {
        $acc[] = makeKeyDiff($key, $dataBefore, $dataAfter);
        return $acc;
    }, []);
}

function createView(array $keysDiff)
{
    $arrayOfStrings = array_map("\Gendiff\KeyDiff\keyDiffToString", $keysDiff);
    return "{" . PHP_EOL
        . implode(PHP_EOL, $arrayOfStrings) . PHP_EOL
        . "}" . PHP_EOL;
}
