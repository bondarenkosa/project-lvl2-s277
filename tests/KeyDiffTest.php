<?php
namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\KeyDiff;

class KeyDiffTest extends TestCase
{
    public function testToString()
    {
        $key = "verbose";
        $first[$key] = null;
        $diff = "  - {$key}: null";
        $keyDiff = new KeyDiff($key, $first, []);
        
        $this->assertEquals($diff, (string) $keyDiff);
    }
}
