<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class NavigationTest extends TestCase
{
    public function testFirst()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals('foo', $collection->first());
    }

    public function testLast()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals('baz', $collection->last());
    }

    public function testNext()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals('bar', $collection->next());
    }

    public function testPrev()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $collection->next();

        $this->assertEquals('foo', $collection->prev());
    }

    public function testPrevOnFirstItem()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertFalse($collection->prev());
        $this->assertEquals('foo', $collection->first());
    }

    // Test at
    public function testAt()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals('foo', $collection->at(0));
        $this->assertEquals('bar', $collection->at(1));
        $this->assertEquals('baz', $collection->at(2));
        $this->assertEquals('baz', $collection->at(-1));
        $this->assertEquals('bar', $collection->at(-2));
    }
}
