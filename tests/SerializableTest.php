<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class SerializableTest extends TestCase
{
    public function testSerializable()
    {
        $collection = new Collection([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $serialized = serialize($collection);

        $this->assertEquals(unserialize($serialized), $collection);
    }
}
