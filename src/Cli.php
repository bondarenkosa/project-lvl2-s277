<?php

namespace Gendiff\Cli;

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
}
