<?php

namespace Gendiff\KeyDiff;

function makeKeyDiff(string $key, array $firstCollection, array $secondCollection)
{
    $firstValue = isset($firstCollection[$key]) ? $firstCollection[$key] : null;
    $secondValue = isset($secondCollection[$key]) ? $secondCollection[$key] : null;
    if (!array_key_exists($key, $firstCollection)) {
        $status = "added";
    } elseif (!array_key_exists($key, $secondCollection)) {
        $status = "deleted";
    } elseif ($firstCollection[$key] === $secondCollection[$key]) {
        $status = "unchanged";
    } else {
        $status = "changed";
    }

    return [
        "name" => $key,
        "firstValue" => $firstValue,
        "secondValue" => $secondValue,
        "status" => $status
    ];
}

function getFirstValue($keyDiff)
{
    return $keyDiff['firstValue'];
}

function getSecondValue($keyDiff)
{
    return $keyDiff['secondValue'];
}

function getKeyDiffName($keyDiff)
{
    return $keyDiff['name'];
}

function getKeyDiffStatus($keyDiff)
{
    return $keyDiff['status'];
}

function keyDiffToString($keyDiff)
{
    $key = getKeyDiffName($keyDiff);
    $status = getKeyDiffStatus($keyDiff);
    $firstValue = getFirstValue($keyDiff);
    $secondValue = getSecondValue($keyDiff);
    $before = is_string($firstValue) ? $firstValue : json_encode($firstValue);
    $after = is_string($secondValue) ? $secondValue : json_encode($secondValue);
    switch ($status) {
        case "added":
            return "  + {$key}: {$after}";
        case "deleted":
            return "  - {$key}: {$before}";
        case "changed":
            return "  + {$key}: {$after}" . PHP_EOL
                . "  - {$key}: {$before}";
        case "unchanged":
            return "    {$key}: {$before}";
    }
}
