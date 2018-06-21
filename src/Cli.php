<?php

namespace Gendiff\Cli;

use function Gendiff\Lib\genDiff;

const HELP = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]

DOC;

function run()
{
    $args = (new \Docopt\Handler)->handle(HELP);

    if (isset($args["<firstFile>"])) {
        $diff = genDiff($args["<firstFile>"], $args["<secondFile>"]);
        print_r($diff);
        echo PHP_EOL;
    }
}
