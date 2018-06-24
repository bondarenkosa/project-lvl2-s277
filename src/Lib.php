<?php

namespace Gendiff;

use function Gendiff\Parsers\getParser;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty")
{
    $dataBefore = getParsedFileData($pathToFile1);
    $dataAfter = getParsedFileData($pathToFile2);
    $ast = buildAst($dataBefore, $dataAfter);

    return createView($ast);
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

function createView($ast)
{
    return "{" . PHP_EOL
        . renderAst($ast) . PHP_EOL
        . "}" . PHP_EOL;
}

function renderAst(array $ast, $indent = "  ")
{
    $iter = function ($node) use (&$iter, $indent) {
        $key = $node["key"];
        $type = $node["type"];
        $children = $node["children"];
        $beforeValue = valueToString($node["beforeValue"], "{$indent}  ");
        $afterValue = valueToString($node["afterValue"], "{$indent}  ");
        switch ($type) {
            case "nested":
                return "{$indent}  {$key}: {" . PHP_EOL
                    . renderAst($children, $indent . "    ")
                    . PHP_EOL . "    }";
            case "added":
                return "{$indent}+ {$key}: {$afterValue}";
            case "deleted":
                return "{$indent}- {$key}: {$beforeValue}";
            case "changed":
                return "{$indent}+ {$key}: {$afterValue}" . PHP_EOL
                    . "{$indent}- {$key}: {$beforeValue}";
            case "unchanged":
                return "{$indent}  {$key}: {$beforeValue}";
        }
    };

    return implode(PHP_EOL, array_map($iter, $ast));
}

function valueToString($value, $indent)
{
    if (is_null($value) || is_bool($value)) {
        return json_encode($value);
    }

    if (is_array($value)) {
        return arrayToString($value, $indent);
    }

    return $value;
}

function arrayToString(array $arr, $indent)
{
    $result = array_map(function ($key, $value) use ($indent) {
        if (is_array($value)) {
            $value = arrayToString($value, "{$indent}    ");
        }

        return "{$indent}    {$key}: {$value}";
    }, array_keys($arr), $arr);

    return "{" . PHP_EOL . implode(PHP_EOL, $result) . PHP_EOL . "{$indent}}";
}
