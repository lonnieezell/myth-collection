<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class CountableTest extends TestCase
{
    public function testCountable()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);

        $this->assertEquals(3, count($collection));
    }
}
