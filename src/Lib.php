<?php

namespace Gendiff;

use function Gendiff\Parsers\getParser;
use function Gendiff\Renderers\getRenderer;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty")
{
    $dataBefore = getParsedFileData($pathToFile1);
    $dataAfter = getParsedFileData($pathToFile2);
    $ast = buildAst($dataBefore, $dataAfter);

    return createView($ast, $format);
}

function createView($ast, $format)
{
    $render = getRenderer($format);

    return $render($ast);
}

function getParsedFileData(string $filePath)
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $data = file_get_contents($filePath);
    $parse = getParser($extension);

    return $parse($data);
}

function buildAst($dataBefore = [], $dataAfter = [])
{
    $keys = getUniqueKeys($dataBefore, $dataAfter);
    $result = array_map(function ($key) use ($dataBefore, $dataAfter) {
        if (array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)) {
            $beforeValue = $dataBefore[$key];
            $afterValue = $dataAfter[$key];
            if (hasChildren($beforeValue) && hasChildren($afterValue)) {
                return getNode([
                    "key" => $key,
                    "type" => "nested",
                    "children" => buildAst($beforeValue, $afterValue)
                ]);
            }
            if ($beforeValue === $afterValue) {
                return getNode([
                    "key" => $key,
                    "type" => "unchanged",
                    "beforeValue" => $beforeValue,
                    "afterValue" => $afterValue
                ]);
            }
            return getNode([
                "key" => $key,
                "type" => "changed",
                "beforeValue" => $beforeValue,
                "afterValue" => $afterValue
            ]);
        }
        if (!array_key_exists($key, $dataAfter)) {
            return getNode([
                "key" => $key,
                "type" => "deleted",
                "beforeValue" => $dataBefore[$key],
            ]);
        }

        return getNode([
            "key" => $key,
            "type" => "added",
            "afterValue" => $dataAfter[$key],
        ]);
    }, $keys);

    return $result;
}

function getNode(array $properties)
{
    $default = [
        "key" => "",
        "type" => "",
        "beforeValue" => null,
        "afterValue" => null,
        "children" => []
    ];

    return array_merge($default, $properties);
}

function hasChildren($node)
{
    return is_array($node);
}

function getUniqueKeys($dataBefore = [], $dataAfter = [])
{
    $keys = array_merge(array_keys($dataBefore), array_keys($dataAfter));

    return array_unique($keys);
}
