<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Myth\Collection\Collection;

class InstanceTest extends TestCase
{
    public function testAverage()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(3, $collection->average());
    }

    public function testAverageWithStringKey()
    {
        $collection = new Collection([
            ['pages' => 10, 'copies' => 1],
            ['pages' => 20, 'copies' => 2],
            ['pages' => 30, 'copies' => 3],
            ['pages' => 40, 'copies' => 4],
            ['pages' => 50, 'copies' => 5],
        ]);
        $this->assertEquals(30, $collection->average('pages'));
        $this->assertEquals(3, $collection->average('copies'));
    }

    public function testKey()
    {
        $collection = new Collection(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertEquals('foo', $collection->key());

        $collection->next();
        $this->assertEquals('bar', $collection->key());
    }

    public function testColumn()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Carter'],
            ['id' => 3, 'name' => 'Steve'],
        ]);
        $new = $collection->column('name');
        $this->assertSame(['John', 'Carter', 'Steve'], $new->toArray());
    }

    public function testColumnWithColumnAndIndex()
    {
        $collection = new Collection([
            (object) ['id' => 1, 'name' => 'John'],
            (object) ['id' => 2, 'name' => 'Carter'],
            (object) ['id' => 3, 'name' => 'Steve'],
        ]);
        $new = $collection->column('name', 'id');
        $this->assertSame([1 => 'John', 2 => 'Carter', 3 => 'Steve'], $new->toArray());
    }

    public function testColumnWithIndex()
    {
        $collection = new Collection([
            (object) ['id' => 1, 'name' => 'John'],
            (object) ['id' => 2, 'name' => 'Carter'],
            (object) ['id' => 3, 'name' => 'Steve'],
        ]);
        $new = $collection->column(null, 'id');
        $this->assertEquals([
            1 => (object) ['id' => 1, 'name' => 'John'],
            2 => (object) ['id' => 2, 'name' => 'Carter'],
            3 => (object) ['id' => 3, 'name' => 'Steve'],
        ], $new->toArray());
    }

    public function testDiff()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);
        $new = $collection->diff(['foo', 'baz']);
        $this->assertSame([1 => 'bar'], $new->toArray());
    }

    public function testDiffWithCollection()
    {
        $collection = new Collection(['foo', 'bar', 'baz']);
        $new = $collection->diff(new Collection(['foo', 'baz']));
        $this->assertSame([1 => 'bar'], $new->toArray());
    }

    public function testDiffWithColumn()
    {
        $collection = new Collection([
            ['name' => 'John Doe 1', 'age' => 25],
            ['name' => 'Jane Doe 2', 'age' => 30],
            ['name' => 'John Doe 3', 'age' => 25],
        ]);
        $new = $collection->diff([
            ['name' => 'John Doe 1', 'age' => 25],
            ['name' => 'John Doe 3', 'age' => 25],
        ], 'name');
        $this->assertSame([1 => ['name' => 'Jane Doe 2', 'age' => 30]], $new->toArray());
    }

    public function testDiffWithColumns()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'John Doe', 'age' => 15],
            ['id' => 2, 'name' => 'Jane Doe', 'age' => 30],
            ['id' => 3, 'name' => 'John Doe', 'age' => 25],
            ['id' => 4, 'name' => 'John Doe', 'age' => 25],
        ]);
        $new = $collection->diff([
            ['id' => 1, 'name' => 'John Doe', 'age' => 25],
            ['id' => 2, 'name' => 'Jane Doe', 'age' => 30],
            ['id' => 4, 'name' => 'John Doe', 'age' => 25],
        ], ['name', 'age']);
        $this->assertSame([['id' => 1, 'name' => 'John Doe', 'age' => 15]], $new->toArray());
    }

    public function testDiffWithColumnAndCollection()
    {
        $collection = new Collection([
            (object) ['name' => 'John Doe 1', 'age' => 25],
            (object) ['name' => 'Jane Doe 2', 'age' => 30],
            (object) ['name' => 'John Doe 3', 'age' => 25],
        ]);
        $new = $collection->diff(new Collection([
            (object) ['name' => 'John Doe 1', 'age' => 25],
            (object) ['name' => 'John Doe 3', 'age' => 25],
        ]), 'name');
        $this->assertEquals([1 => (object) ['name' => 'Jane Doe 2', 'age' => 30]], $new->toArray());
    }

    public function testEach()
    {
        $collection = new Collection(['foo' => 'bar', 'bar' => 'baz']);
        $squashed = '';
        $collection->each(function ($item, $key) use (&$squashed) {
            $squashed .= $key . ':' . $item . ' ';
        });
        $this->assertEquals('foo:bar bar:baz ', $squashed);
    }

    public function testEvery()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertTrue($collection->every(function ($item) {
            return $item < 6;
        }));

        $this->assertFalse($collection->every(function ($item) {
            return $item < 4;
        }));
    }

    public function testEveryEmpty()
    {
        $collection = new Collection([]);
        $this->assertTrue($collection->every(function ($item) {
            return $item < 6;
        }));
    }

    public function testFill()
    {
        $collection = new Collection([]);
        $new = $collection->fill(0, 5, 'foo');
        $this->assertEquals(['foo', 'foo', 'foo', 'foo', 'foo', 'foo'], $new->toArray());
        $this->assertNotSame($new, $collection);
    }

    public function testFillStartOnly()
    {
        $collection = new Collection([]);
        $new = $collection->fill(5, null, 'foo');
        $this->assertEquals([], $new->toArray());
    }

    public function testFillPartial()
    {
        $collection = new Collection(['foo', 'bar', 'baz', 'qux']);
        $new = $collection->fill(1, 3, 'bar');
        $this->assertEquals(['foo', 'bar', 'bar', 'bar'], $new->toArray());
    }

    public function testFillNullEnd()
    {
        $collection = new Collection(['foo', 'bar', 'baz', 'qux']);
        $new = $collection->fill(1, null, 'bar');
        $this->assertEquals(['foo', 'bar', 'bar', 'bar'], $new->toArray());
    }

    public function testFilter()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);

        // Use array_values to ensure the indexes are correct
        $this->assertEquals([2, 4], array_values($collection->filter(function ($item) {
            return $item % 2 === 0;
        })->toArray()));
    }

    public function testFind()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(2, $collection->find(function ($item) {
            return $item % 2 === 0;
        }));
    }

    public function testFindIndex()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(1, $collection->findIndex(function ($item) {
            return $item % 2 === 0;
        }));
    }

    public function testFindIndexNotFound()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(-1, $collection->findIndex(function ($item) {
            return $item > 10;
        }));
    }

    public function testFlatten()
    {
        $collection = new Collection([1, 2, 3, [4, 5]]);
        $this->assertEquals([1, 2, 3, 4, 5], $collection->flatten()->toArray());
    }

    public function testFlattenWithStringKeys()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => [
                'e' => 4,
                'f' => 5,
            ],
        ]);
        $this->assertEquals([1, 2, 3, 4, 5], $collection->flatten()->toArray());
    }

    public function testFlattenWithStringKeysAndValues()
    {
        $collection = new Collection([
            'fruits' => ['apple', 'banana', 'orange'],
            'vegetables' => ['carrot', 'tomato', 'cucumber'],
        ]);
        $this->assertEquals(['apple', 'banana', 'orange', 'carrot', 'tomato', 'cucumber'], $collection->flatten()->toArray());
    }

    public function testFlattenWithDepth()
    {
        $collection = new Collection([1, 2, 3, [4, 5, [6, 7]]]);
        $this->assertEquals([1, 2, 3, 4, 5, [6, 7]], $collection->flatten()->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, [6, 7]], $collection->flatten(1)->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $collection->flatten(2)->toArray());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $collection->flatten(INF)->toArray());
    }

    public function testFlattenWithDepthAndKeys()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => [
                'e' => 4,
                'f' => 5,
                'g' => [
                    'h' => 6,
                    'i' => 7,
                ],
            ],
        ]);
        $this->assertEquals([1, 2, 3, 4, 5, ['h' => 6, 'i' => 7]], $collection->flatten()->toArray());
    }

    // test groupBy
    public function testGroupBy()
    {
        $collection = new Collection([
            ['name' => 'John Doe', 'age' => 25],
            ['name' => 'Jane Doe', 'age' => 30],
            ['name' => 'John Doe', 'age' => 25],
        ]);
        $grouped = $collection->groupBy('name');
        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertCount(2, $grouped);
        $this->assertEquals(['John Doe' => [
            ['name' => 'John Doe', 'age' => 25],
            ['name' => 'John Doe', 'age' => 25],
        ], 'Jane Doe' => [
            ['name' => 'Jane Doe', 'age' => 30],
        ]], $grouped->toArray());
    }

    public function testGroupByInvalidKey()
    {
        $collection = new Collection([
            ['name' => 'John Doe', 'age' => 25],
            ['name' => 'Jane Doe', 'age' => 30],
            ['name' => 'John Doe', 'age' => 25],
        ]);
        $grouped = $collection->groupBy('invalid');
        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertCount(3, $grouped);
        $this->assertEquals([
            ['name' => 'John Doe', 'age' => 25],
            ['name' => 'Jane Doe', 'age' => 30],
            ['name' => 'John Doe', 'age' => 25],
        ], $grouped->toArray());
    }

    public function testGroupByEmpty()
    {
        $collection = new Collection([]);
        $grouped = $collection->groupBy('name');
        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertCount(0, $grouped);
        $this->assertEquals([], $grouped->toArray());
    }

    public function testIncludes()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertTrue($collection->includes(3));
        $this->assertFalse($collection->includes(6));
    }

    public function testIncludesStrings()
    {
        $collection = new Collection(['dog', 'cat', 'bird', 'fish']);
        $this->assertTrue($collection->includes('cat'));
        $this->assertFalse($collection->includes('cow'));
    }

    public function testIncludesEmpty()
    {
        $collection = new Collection([]);
        $this->assertFalse($collection->includes(1));
    }

    public function testIsEmpty()
    {
        $collection = new Collection([]);
        $this->assertTrue($collection->isEmpty());
    }

    public function testIsEmptyWithItems()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertFalse($collection->isEmpty());
    }

    public function testIndexOf()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(3, $collection->indexOf(4));
    }

    public function testIndexOfWithStringKeys()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4
        ]);
        $this->assertEquals('d', $collection->indexOf(4));
    }

    public function testIndexOfNotFound()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(-1, $collection->indexOf(6));
    }

    public function testJoin()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals('12345', $collection->join());
        $this->assertEquals('1,2,3,4,5', $collection->join(','));
    }

    public function testJoinWithLastValue()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals('1,2,3,4, and 5', $collection->join(',', ' and '));
    }

    public function testJoinWithLastValueOnlyOneItem()
    {
        $collection = new Collection([1]);
        $this->assertEquals('1', $collection->join(',', ' and '));
    }

    public function testKeys()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4
        ]);
        $new = $collection->keys();
        $this->assertEquals(['a', 'b', 'c', 'd'], $new->toArray());
    }

    public function testKeysEmpty()
    {
        $collection = new Collection([]);
        $new = $collection->keys();
        $this->assertEquals([], $new->toArray());
    }

    public function testMap()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEquals([2, 4, 6, 8, 10], $result->toArray());
    }

    // test map with empty array
    public function testMapEmpty()
    {
        $collection = new Collection([]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        $this->assertEquals([], $result->toArray());
    }

    public function testMergeWithArrays()
    {
        $collection = new Collection([1, 2, 3]);
        $collection->merge([4, 5, 6], [7, 8, 9]);

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $collection->toArray());
    }

    // test merge with Collection
    public function testMergeWithCollection()
    {
        $collection = new Collection([1, 2, 3]);
        $collection->merge(new Collection([4, 5, 6]));

        $this->assertEquals([1, 2, 3, 4, 5, 6], $collection->toArray());
    }

    // test merge with array and collection
    public function testMergeWithArrayAndCollection()
    {
        $collection = new Collection([1, 2, 3]);
        $collection->merge([4, 5, 6], new Collection([7, 8, 9]));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $collection->toArray());
    }

    public function testPop()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(5, $collection->pop());
        $this->assertEquals([1, 2, 3, 4], $collection->toArray());
    }

    public function testPopEmpty()
    {
        $collection = new Collection([]);
        $this->assertNull($collection->pop());
    }

    public function testPush()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $collection->push(6);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $collection->toArray());
    }

    public function testPushMultiple()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $collection->push(6, 7, 8);
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $collection->toArray());
    }

    public function testReduce()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals(15, $result);
    }

    public function testReduceWithInitialValue()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 10);
        $this->assertEquals(25, $result);
    }

    public function testReverse()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $new = $collection->reverse();
        $this->assertEquals([5, 4, 3, 2, 1], $new->toArray());
    }

    public function testReverseWithStringKeys()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4
        ]);
        $new = $collection->reverse();
        $this->assertEquals(['d' => 4, 'c' => 3, 'b' => 2, 'a' => 1], $new->toArray());
    }

    public function testShift()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(1, $collection->shift());
        $this->assertEquals([2, 3, 4, 5], $collection->toArray());
    }

    public function testSlice()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $new = $collection->slice(2);
        $this->assertEquals([3, 4, 5], $new->toArray());
    }

    public function testSliceNegative()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $new = $collection->slice(-2);
        $this->assertEquals([4, 5], $new->toArray());
    }

    public function testSort()
    {
        $collection = new Collection([3, 1, 5, 2, 4]);
        $new = $collection->sort();
        $this->assertEquals([1, 2, 3, 4, 5], $new->toArray());
    }

    public function testSortWithCallback()
    {
        $collection = new Collection([
            ['pages' => 10, 'copies' => 2],
            ['pages' => 100, 'copies' => 1],
            ['pages' => 1, 'copies' => 2],
        ]);
        $new = $collection->sort(function ($item) {
            return $item['pages'];
        });
        $expected = [
            ['pages' => 1, 'copies' => 2],
            ['pages' => 10, 'copies' => 2],
            ['pages' => 100, 'copies' => 1],
        ];
        $this->assertEquals($expected, $new->toArray());
    }

    public function testSortDesc()
    {
        $collection = new Collection([3, 1, 5, 2, 4]);
        $new = $collection->sortDesc();
        $this->assertEquals([5, 4, 3, 2, 1], $new->toArray());
    }

    public function testSortDescWithCallback()
    {
        $collection = new Collection([
            ['pages' => 10, 'copies' => 2],
            ['pages' => 100, 'copies' => 1],
            ['pages' => 1, 'copies' => 2],
        ]);
        $new = $collection->sortDesc(function ($item) {
            return $item['pages'];
        });
        $expected = [
            ['pages' => 100, 'copies' => 1],
            ['pages' => 10, 'copies' => 2],
            ['pages' => 1, 'copies' => 2],
        ];
        $this->assertEquals($expected, $new->toArray());
    }

    public function testSplice()
    {
        $collection = new Collection(['red', 'green', 'yellow', 'blue']);
        $new = $collection->splice(2);
        $this->assertEquals(['yellow', 'blue'], $new->toArray());
        $this->assertEquals(['red', 'green'], $collection->toArray());
    }

    public function testSpliceWithReplacement()
    {
        $collection = new Collection(['red', 'green', 'yellow', 'blue']);
        $new = $collection->splice(2, 2, 'orange', 'purple');
        $this->assertEquals(['yellow', 'blue'], $new->toArray());
        $this->assertEquals(['red', 'green', 'orange', 'purple'], $collection->toArray());
    }

    public function testSum()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertEquals(15, $collection->sum());
    }

    public function testSumWithKey()
    {
        $collection = new Collection([
            ['pages' => 10, 'copies' => 2],
            ['pages' => 20, 'copies' => 3],
            ['pages' => 30, 'copies' => 4]
        ]);
        $this->assertEquals(60, $collection->sum('pages'));
        $this->assertEquals(9, $collection->sum('copies'));
    }

    public function testSumWithClosure()
    {
        $collection = new Collection([
            ['pages' => 10, 'copies' => 2],
            ['pages' => 20, 'copies' => 3],
            ['pages' => 30, 'copies' => 4]
        ]);
        $this->assertEquals(60, $collection->sum(function ($item) {
            return $item['pages'];
        }));
        $this->assertEquals(9, $collection->sum(function ($item) {
            return $item['copies'];
        }));
    }

    public function testUnique()
    {
        $collection = new Collection([1, 2, 2, 3, 1, 5, 1, 3]);
        $new = $collection->unique();
        $this->assertEquals([
            0 => 1,
            1 => 2,
            3 => 3,
            5 => 5,
        ], $new->toArray());
    }

    public function testUniqueWithColumn()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
            ['id' => 1, 'name' => 'Jim'],
            ['id' => 3, 'name' => 'Joe'],
        ]);

        $new = $collection->unique('id');
        $this->assertEquals([
            0 => ['id' => 1, 'name' => 'John'],
            1 => ['id' => 2, 'name' => 'Jane'],
            3 => ['id' => 3, 'name' => 'Joe'],
        ], $new->toArray());
    }

    public function testUniqueWithColumnAndObjects()
    {
        $collection = new Collection([
            (object) ['id' => 1, 'name' => 'John'],
            (object) ['id' => 2, 'name' => 'Jane'],
            (object) ['id' => 1, 'name' => 'Jim'],
            (object) ['id' => 3, 'name' => 'Joe'],
        ]);

        $new = $collection->unique('id');
        $this->assertEquals([
            0 => (object) ['id' => 1, 'name' => 'John'],
            1 => (object) ['id' => 2, 'name' => 'Jane'],
            3 => (object) ['id' => 3, 'name' => 'Joe'],
        ], $new->toArray());
    }

    public function testUniqueWithColumnAndUnorderedArray()
    {
        $collection = new Collection([
            2 => ['id' => 1, 'name' => 'John'],
            3 => ['id' => 2, 'name' => 'Jane'],
            0 => ['id' => 1, 'name' => 'Jim'],
            1 => ['id' => 3, 'name' => 'Joe'],
        ]);

        $new = $collection->unique('id');
        $this->assertEquals([
            2 => ['id' => 1, 'name' => 'John'],
            1 => ['id' => 3, 'name' => 'Joe'],
            3 => ['id' => 2, 'name' => 'Jane'],
        ], $new->toArray());
    }

    public function testUniqueWithManyColumns()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'John', 'age' => 30],
            ['id' => 2, 'name' => 'Jane', 'age' => 35],
            ['id' => 1, 'name' => 'Jim', 'age' => 30],
            ['id' => 2, 'name' => 'Janet', 'age' => 35],
            ['id' => 3, 'name' => 'Joe', 'age' => 30],
        ]);

        $new = $collection->unique(['id', 'age']);
        $this->assertEquals([
            0 => ['id' => 1, 'name' => 'John', 'age' => 30],
            1 => ['id' => 2, 'name' => 'Jane', 'age' => 35],
            4 => ['id' => 3, 'name' => 'Joe', 'age' => 30],
        ], $new->toArray());
    }

    // test values method
    public function testValues()
    {
        $collection = new Collection([
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
        ]);
        $new = $collection->values();
        $this->assertEquals([0 => 'ten', 1 => 'eleven', 2 => 'twelve'], $new->toArray());
        $this->assertNotSame($new, $collection);
    }

    public function testWhen()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ]);
        $new = $collection->when(function ($item) {
            return $item % 2 === 0;
        });
        $this->assertEquals(['b' => 2, 'd' => 4], $new->toArray());
    }

    public function testUnless()
    {
        $collection = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ]);
        $new = $collection->unless(function ($item) {
            return $item % 2 === 0;
        });
        $this->assertEquals(['a' => 1, 'c' => 3, 'e' => 5], $new->toArray());
    }
}
