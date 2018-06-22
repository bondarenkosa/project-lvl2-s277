<?php
namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\Lib\genDiff;

class GenDiffTest extends TestCase
{
    protected $diffPretty = <<<EOD
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOD;
    protected $fixturesPath = "tests" . DIRECTORY_SEPARATOR .  "fixtures". DIRECTORY_SEPARATOR;

    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expected, $extension)
    {
        $pathToFile1 = $this->fixturesPath . "before.{$extension}";
        $pathToFile2 = $this->fixturesPath . "after.{$extension}";

        $this->assertEquals($expected, genDiff($pathToFile1, $pathToFile2));
    }

    public function additionProvider()
    {
        return [
            [$this->diffPretty, "json"],
            [$this->diffPretty, "yml"]
        ];
    }
}
