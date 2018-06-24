<?php

namespace Gendiff\Renderers;

function getRenderer(string $format)
{
    $renderers = [
        'pretty' => function ($data) {
            return renderPrettyDiff($data);
        },
        'plain' => function ($data) {
            return renderPlainDiff($data);
        }
    ];

    return $renderers[$format];
}

function renderPrettyDiff($ast)
{
    $renderAst = function ($ast, $indent = "  ") use (&$renderAst) {
        $iter = function ($node) use (&$iter, &$renderAst, &$prepareValueToRender, $indent) {
            $key = $node["key"];
            $type = $node["type"];
            $children = $node["children"];
            $beforeValue = $prepareValueToRender($node["beforeValue"], "{$indent}  ");
            $afterValue = $prepareValueToRender($node["afterValue"], "{$indent}  ");
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

        $prepareValueToRender = function ($value, $indent = "") use (&$arrayToString) {
            if (is_null($value) || is_bool($value)) {
                return strtolower(var_export($value, true));
            }

            if (is_array($value)) {
                return $arrayToString($value, $indent);
            }

            return $value;
        };

        $arrayToString = function (array $arr, $indent) use (&$arrayToString) {
            $result = array_map(function ($key, $value) use ($indent) {
                if (is_array($value)) {
                    $value = $arrayToString($value, "{$indent}    ");
                }

                return "{$indent}    {$key}: {$value}";
            }, array_keys($arr), $arr);

            return "{" . PHP_EOL . implode(PHP_EOL, $result) . PHP_EOL . "{$indent}}";
        };

        return implode(PHP_EOL, array_map($iter, $ast));
    };

    return "{" . PHP_EOL
        . $renderAst($ast) . PHP_EOL
        . "}" . PHP_EOL;
}

function renderPlainDiff($ast)
{
    $renderAst = function ($ast, $parent = "") use (&$renderAst) {
        $iter = function ($node) use (&$iter, &$renderAst, &$prepareValueToRender, $parent) {
            $key = $node["key"];
            $type = $node["type"];
            $children = $node["children"];
            $beforeValue = $prepareValueToRender($node["beforeValue"]);
            $afterValue = $prepareValueToRender($node["afterValue"]);
            switch ($type) {
                case "nested":
                    return $renderAst($children, "{$parent}{$key}.");
                case "added":
                    return "Property '{$parent}{$key}' was {$type} with value: '{$afterValue}'" . PHP_EOL;
                case "deleted":
                    return "Property '{$parent}{$key}' was {$type}" . PHP_EOL;
                case "changed":
                    return "Property '{$parent}{$key}' was {$type}. From '${beforeValue}' to '{$afterValue}'" . PHP_EOL;
            }
        };

        $prepareValueToRender = function ($value) {
            if (is_null($value) || is_bool($value)) {
                return strtolower(var_export($value, true));
            }
            if (is_array($value)) {
                return 'complex value';
            }

            return $value;
        };

        return implode("", array_map($iter, $ast));
    };

    return $renderAst($ast);
}
