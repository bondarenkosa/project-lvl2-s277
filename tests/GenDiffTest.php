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
    public function testGenDiff($fileWithExpectedData, $nameFile1, $nameFile2, $format = "pretty")
    {
        $expected = file_get_contents($this->fixturesPath . $fileWithExpectedData);
        $pathToFile1 = $this->fixturesPath . $nameFile1;
        $pathToFile2 = $this->fixturesPath . $nameFile2;

        $this->assertEquals($expected, genDiff($pathToFile1, $pathToFile2, $format));
    }

    public function additionProvider()
    {
        return [
            ["expected", "before.json", "after.json"],
            ["expected", "before.yml", "after.yml"],
            ["nestedExpected", "nestedBefore.json", "nestedAfter.json"],
            ["nestedExpected", "nestedBefore.yml", "nestedAfter.yml"],
            ["nestedExpectedPlainDiff", "nestedBefore.json", "nestedAfter.json", "plain"],
            ["nestedExpectedPlainDiff", "nestedBefore.yml", "nestedAfter.yml", "plain"]
        ];
    }
}
