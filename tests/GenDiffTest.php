<?php
namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use function Gendiff\Lib\genDiff;

class GenDiffTest extends TestCase
{
    protected $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup();
        $this->diffPretty = <<<EOD
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOD;
    }

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
        $firstFile = $this->makeFile('before.json', $before);
        $secondFile = $this->makeFile('after.json', $after);

        $this->assertEquals($this->diffPretty, genDiff($firstFile->url(), $secondFile->url()));
    }

    public function testGenDiffYml()
    {
        $before = <<<EOD
host: hexlet.io
timeout: 50
proxy: 123.234.53.22
EOD;
        $after = <<<EOD
timeout: 20
verbose: true
host: hexlet.io
EOD;
        $firstFile = $this->makeFile('before.yml', $before);
        $secondFile = $this->makeFile('after.yml', $after);

        $this->assertEquals($this->diffPretty, genDiff($firstFile->url(), $secondFile->url()));
    }

    protected function makeFile(string $name, string $data)
    {
        $file = vfsStream::newFile($name)->at($this->root);
        $file->setContent($data);

        return $file;
    }
}
