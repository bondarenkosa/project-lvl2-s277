<?php

namespace Gendiff\KeyDiff;

function makeKeyDiff(string $key, array $dataBefore, array $dataAfter)
{
    if (!array_key_exists($key, $dataBefore)) {
        $type = "added";
        $valueBefore = null;
        $valueAfter = $dataAfter[$key];
    } elseif (!array_key_exists($key, $dataAfter)) {
        $type = "deleted";
        $valueBefore = $dataBefore[$key];
        $valueAfter = null;
    } elseif ($dataBefore[$key] === $dataAfter[$key]) {
        $type = "unchanged";
        $valueBefore = $dataBefore[$key];
        $valueAfter = $dataAfter[$key];
    } else {
        $type = "changed";
        $valueBefore = $dataBefore[$key];
        $valueAfter = $dataAfter[$key];
    }

    return [
        "name" => $key,
        "valueBefore" => $valueBefore,
        "valueAfter" => $valueAfter,
        "type" => $type
    ];
}

function keyDiffToString($keyDiff)
{
    $type = $keyDiff["type"];
    $key = $keyDiff["name"];
    $valueBefore = valueToString($keyDiff["valueBefore"]);
    $valueAfter = valueToString($keyDiff["valueAfter"]);
    switch ($type) {
        case "added":
            return "  + {$key}: {$valueAfter}";
        case "deleted":
            return "  - {$key}: {$valueBefore}";
        case "changed":
            return "  + {$key}: {$valueAfter}" . PHP_EOL
                . "  - {$key}: {$valueBefore}";
        case "unchanged":
            return "    {$key}: {$valueBefore}";
    }
}

function valueToString($value)
{
    if (is_null($value) || is_bool($value)) {
        return json_encode($value);
    }

    return $value;
}
