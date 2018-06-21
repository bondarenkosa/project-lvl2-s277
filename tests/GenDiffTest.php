<?php
namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use function Gendiff\Lib\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiffJson()
    {
        $before = <<<EOD
{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22"
}
EOD;
        $after = <<<EOD
{
  "timeout": 20,
  "verbose": true,
  "host": "hexlet.io"
}
EOD;
        $diff = <<<EOD
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOD;

        $root = vfsStream::setup();
        $firstFile = vfsStream::newFile('before.json')->at($root);
        $firstFile->setContent($before);
        $pathToFile1 = $firstFile->url();
        $secondFile = vfsStream::newFile('after.json')->at($root);
        $secondFile->setContent($after);
        $pathToFile2 = $secondFile->url();

        $this->assertEquals($diff, genDiff($pathToFile1, $pathToFile2));
    }
}
