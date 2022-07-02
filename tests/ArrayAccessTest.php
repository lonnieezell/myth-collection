<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class ArrayAccessTest extends TestCase
{
    public function testOffsetGetWithIndexes()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals('foo', $collection[0]);
        $this->assertEquals('bar', $collection[1]);
        $this->assertEquals('baz', $collection[2]);
    }

    public function testOffsetGetWithKeys()
    {
        $collection = new Collection([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $this->assertEquals('bar', $collection['foo']);
        $this->assertEquals('baz', $collection['bar']);
    }

    public function testOffsetSetWithIndexes()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $collection[0] = 'qux';
        $collection[1] = 'quux';

        $this->assertEquals('qux', $collection[0]);
        $this->assertEquals('quux', $collection[1]);
    }

    public function testOffsetSetWithKeys()
    {
        $collection = new Collection([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $collection['foo'] = 'qux';
        $collection['bar'] = 'quux';

        $this->assertEquals('qux', $collection['foo']);
        $this->assertEquals('quux', $collection['bar']);
    }

    public function testOffsetExistsWithIndexes()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertTrue(isset($collection[2]));
        $this->assertFalse(isset($collection[3]));
    }

    public function testOffsetExistsWithKeys()
    {
        $collection = new Collection([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $this->assertTrue(isset($collection['foo']));
        $this->assertTrue(isset($collection['bar']));
        $this->assertFalse(isset($collection['baz']));
    }

    public function testOffsetUnsetWithIndexes()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        unset($collection[0]);

        $this->assertFalse(isset($collection[0]));
        $this->assertEquals(2, $collection->count());
    }
}
