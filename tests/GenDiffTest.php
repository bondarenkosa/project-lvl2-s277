<?php
namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\genDiff;

class GenDiffTest extends TestCase
{
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
        $diffPretty = file_get_contents($this->fixturesPath . "expected");

        return [
            [$diffPretty, "json"],
            [$diffPretty, "yml"]
        ];
    }
}
