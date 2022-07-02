<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class MetaTest extends TestCase
{
    public function testCreateWithStandardConstructor()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testCreateWithStaticOfMethod()
    {
        $collection = Collection::of('foo', 'bar', 'baz');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testOfMethodWithNoParametersReturnsNull()
    {
        $collection = Collection::of();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals([], $collection->toArray());
    }

    public function testItemsReturnsAnArrayIterator()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $iterator = $collection->items();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertEquals(['foo', 'bar', 'baz'], $iterator->getArrayCopy());
    }

    // Test from method
    public function testFromMethodWithArray()
    {
        $collection = Collection::from(['foo', 'bar', 'baz']);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testFromMethodWithTraversable()
    {
        $collection = Collection::from(new \ArrayObject(['foo', 'bar', 'baz']));

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testFromMethodWithEmtpyClass()
    {
        $object = new class {
        };
        $collection = Collection::from($object);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals([], $collection->toArray());
    }

    public function testFromMethodWithClass()
    {
        $object = new class {
            public $foo = 'foo';
            public $bar = 'bar';
            public static $baz = 'baz';
        };
        $collection = Collection::from($object);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', 'bar', 'baz'], $collection->toArray());
    }

    public function testFromMethodWithCallbackEmpty()
    {
        $collection = Collection::from(null, function ($item) {
            return strtoupper($item);
        });

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals([], $collection->toArray());
    }

    public function testFromMethodWithCallbackEmptyArray()
    {
        $collection = Collection::from([], function ($item) {
            return strtoupper($item);
        });

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals([], $collection->toArray());
    }

    public function testFromMethodWithCallback()
    {
        $collection = Collection::from(['foo', 'bar', 'baz'], function ($item) {
            return strtoupper($item);
        });

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['FOO', 'BAR', 'BAZ'], $collection->toArray());
    }
}
