<?php

namespace Gendiff\Renderers;

const RENDERERS = [
    'pretty' => __NAMESPACE__ . '\renderPrettyDiff'
];

function getRenderer(string $format)
{
    return RENDERERS[$format]();
}

function renderPrettyDiff()
{
    return function ($data) {
        return createPrettyView($data);
    };
}

function createPrettyView($ast)
{
    $renderAst = function ($ast, $indent = "  ") use (&$renderAst) {
        $iter = function ($node) use (&$iter, &$renderAst, $indent) {
            $key = $node["key"];
            $type = $node["type"];
            $children = $node["children"];
            $beforeValue = valueToString($node["beforeValue"], "{$indent}  ");
            $afterValue = valueToString($node["afterValue"], "{$indent}  ");
            switch ($type) {
                case "nested":
                    return "{$indent}  {$key}: {" . PHP_EOL
                        . $renderAst($children, "$indent    ")
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
    };

    return "{" . PHP_EOL
        . $renderAst($ast) . PHP_EOL
        . "}" . PHP_EOL;
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
