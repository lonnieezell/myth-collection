# Collections

Collections are a powerful, array-like data structure that allow you to work with your data in a more expressive way than traditional arrays. They are especially useful when working with data that is returned from a database, or when working with JSON data. At their heart they are modelled after the Javascript array functionality, and have been expanded from there.

Collection instances are immutable. This means that any action that would modify the item array will return a new Collection instance containing the results of the previous action, leaving the original data untouched.

## A Quick Example

A new collection is created by creating a new instance of `Myth\Collection\Collection`. You can populate the items
in the collection at instantation by passing an array into the constructor.

```php
use Myth\Collection\Collection;

$items = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$collection = new Collection($items);

$firstEvenItem = $collection
    ->filter(function($value, $key) {
        return $value % 2 === 0;
    })->first();
```

You can integrate most of the following methods into your own work by using the `Myth\Collection\CollectionTrait` within your class.

All collections can be accessed via standard array access:

```php
$items = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
$collection = new Collection($items);

echo $collection['b'];
```

## Available Methods

|                         |                             |
|-------------------------|-----------------------------|
| [at](#at)               | [average](#average)         |
| [count](#count)         | [diff](#diff)               |
| [each](#each)           | [every](#every)             |
| [filter](#filter)       | [fill](#fill)               |
| [find](#find)           | [findIndex](#findIndex)     |
| [first](#first)         | [flatten](#flatten)         |
| [groupBy](#groupBy)     | [includes](#includes)       |
| [isEmpty](#isEmpty)     | [indexOf](#indexOf)         |
| [items](#items)         | [join](#join)               |
| [key](#key)             | [keys](#keys)               |
| [last](#last)           | [map](#map)                 |
| [merge](#merge)         | [next](#next)               |
| [pop](#pop)             | [prev](#prev)               |
| [push](#push)           | [reduce](#reduce)           |
| [reverse](#reverse)     | [serialize](#serialize)     |
| [shift](#shift)         | [slice](#slice)             |
| [sort](#sort)           | [sortDesc](#sortDesc)       |
| [splice](#splice)       | [sum](#sum)                 |
| [toArray](#toArray)     | [unique](#unique)           |
| [values](#values)       | [valid](#valid)             |
| [when](#when)           | [unless](#unless)           |
| [unserialize](#unserialize) |

### Creation

#### \_\_construct()

An array of items can be passed directly into the constructor at the time of creation.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
```

#### of()

Creates a new Collection instance with any number of items in it.

```php
$collection = Collection::of($foo, $bar, $baz);
```

#### from($items, $callback)

Creates a new Collection from an iterable class or object.

```php
$items = ['foo', 'bar', 'baz'];
$collection = Collection::from($items);
```

If `$items` is a class instance, it will make a collection from its public properties.

```php
class Foo
{
    public $foo = 'foo';
    public $bar = 'bar';
    protected $baz = 'baz';
}

$foo = new Foo();
$collection = Collection::from($foo);

// $collection = ['foo', 'bar']
```

You can process each element in the collection by passing a callback as the second parameter.

```php
$items = ['foo', 'bar', 'baz'];
$collection = Collection::from($items, function($item) {
    return strtoupper($item);
});

// $collection = ['FOO', 'BAR', 'BAZ']
```

### Item Retrieval

#### toArray()

Returns the items in the collection as an array.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->toArray();
// $collection = ['foo', 'bar', 'baz']
```

#### items()

Returns an ArrayIterator with all of the items in the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->items();
// returns new ArrayIterator(['foo', 'bar', 'baz'])
```

#### serialize()

Returns a serialized version of the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->serialize();
// returns 'a:3:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:3:"baz";}'
```

#### unserialize($items)

Creates a new collection out of a serialized array of items.

```php
$serialized = 'a:3:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:3:"baz";}';
$collection = (new Collection())->unserialize($serialized);
```

### Navigation

#### first()

Returns the first item in the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->next();
// returns 'foo'
```

#### last()

Returns the last item in the collection as it is currently sorted.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->last();
// returns 'baz'
```

#### next()

Returns the next item in the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->next();
// returns 'bar'
```

#### prev()

Returns the previous item in the collection. If the cursor is currently at the first element, it will return the first element in the array.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->prev();
// returns 'foo'
```

#### at()

Returns the item at the current offset. If the offset is negative it will return the item in the collection that is offset that many items from the end of the collection. The offset is zero-based.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->at(1);
// returns 'bar'
```

### Instance Methods

#### count()

Returns the total number of items in the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->count();
// returns 3
```

#### average($key)

Returns the average of all items in the collection. If `$key` is provided, it will return the average of $key within each item.

```php
$collection = new Collection([1, 2, 3, 4]);
return $collection->average();
// returns 2.5

$collection = new Collection([
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 30],
    ['foo' => 40],
]);
return $collection->average('foo');
// returns 25
```

#### diff()

Returns a new collection from the original collection with only different items from passed in array or collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->diff(['foo', 'baz']);
// returns [1 => 'bar']
```

You can also pass the column name as a `string` or multiple columns as `array` as a second parameter if you work with associative arrays or objects.

```php
$collection = new Collection([
    ['name' => 'John Doe 1', 'age' => 25],
    ['name' => 'Jane Doe 2', 'age' => 30],
    ['name' => 'John Doe 3', 'age' => 25],
]);
return $collection->diff([
    ['name' => 'John Doe 1', 'age' => 25],
    ['name' => 'John Doe 3', 'age' => 25],
], 'name');
// returns [1 => ['name' => 'Jane Doe 2', 'age' => 30]]
```

#### each()

Iterates over all items, passing each one into the callable.

```php
$collection->each(function($item, $key) {
    //
});
```

You can stop iterating through the items by returning false from the callable.

```php
$collection->each(function($item, $key) {
    if (/* condition */) {
        return false;
    }
});
```

#### every()

Used to determine if all items in the collection evaluate to true when passed to the given callable.

```php
$collection = new Collection([1, 2, 3, 4]);
$collection->every(function($item) {
    return $item > 2;
});
// returns false
```

If the $items array is empty, it will return `true`.

#### fill($start, $end, $value)

Fills all elements of the collection with a static value. The start value must be provided. If no end index is specified it will fill until the end of the collection.

```php
$collection = new Collection([1, 2, 3, 4, 5]);
return $collection->fill(1, 2, 9);
// returns [1, 9, 9, 4, 5]
```

#### filter()

Returns a new collection with only the items that satisfy the given callable by returning a boolean `true`.

```php
$collection = new Collection([1, 2, 3, 4, 5, 6]);
return $collection->filter(function($item) {
    return $item % 2 === 0;
});
// returns [2, 4, 6]
```

By default, this will only pass the value to the callback. You can pass `true` as the second parameter to pass both the value and the key to the callback.

```php
$collection = new Collection(['foo' => 1, 'bar' => 2, 'baz' => 3]);
return $collection->filter(function($value, $key) {
    if ($key === 'bar') {
        return $value;
    }
}, true);
// returns 2
```

#### find()

Returns the first item in the collection that satisfies the give callback.

```php
$collection = new Collection(['foo' => 1, 'bar' => 2, 'baz' => 2]);
return $collection->find(function($item, $key) {
    return $item >= 2;
});
// returns 2
```

#### findIndex()

Like `find()` except it returns the index of the first item that satisfies the callback.

```php
$collection = new Collection(['foo' => 1, 'bar' => 2, 'baz' => 3]);
return $collection->findIndex(function($item, $key) {
    return $item > 2;
});
// returns 3
```

#### flatten()

Returns a new collection that is a flattened version of the original collection. If `$depth` is provided, it will only flatten that many levels. By default, it will flatten only 1 level deep.

```php
$collection = new Collection([
    'fruits' => ['apple', 'banana', 'orange'],
    'vegetables' => ['carrot', 'tomato', 'cucumber'],
]);
return $collection->flatten();
// returns ['apple', 'banana', 'orange', 'carrot', 'tomato', 'cucumber']
```

Keys for the flattened arrays are NOT preserved.

```php
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
return $collection->flatten();
// returns [1, 2, 3, 4, 5, ['h' => 6, 'i' => 7]]
```

#### groupBy()

Returns a new collection with the items grouped by the given key.

```php
$collection = new Collection([
    ['name' => 'John', 'age' => 21],
    ['name' => 'Jane', 'age' => 21],
    ['name' => 'Bob', 'age' => 22],
    ['name' => 'Mary', 'age' => 22],
]);
return $collection->groupBy('age');
// returns [
//     21 => [
//         ['name' => 'John', 'age' => 21],
//         ['name' => 'Jane', 'age' => 21],
//     ],
//     22 => [
//         ['name' => 'Bob', 'age' => 22],
//         ['name' => 'Mary', 'age' => 22],
//     ],
// ]
```

#### includes()

Returns boolean `true` if the collection contains the given value, `false` otherwise.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->includes('bar');
// returns true
```

#### isEmpty()

Returns boolean `true` if the collection is empty, `false` otherwise.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->isEmpty();
// returns false

$collection = new Collection();
return $collection->isEmpty();
// returns true
```

#### indexOf()

Returns the index of the given value, or `-1` if it is not found.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->indexOf('bar');
// returns 1
```

#### join()

Return all values joined by a given string.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->join(', ');
// returns 'foo, bar, baz'
```

If a second parameter is provided, it will be used as the last separator.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->join(', ', ' and ');
// returns 'foo, bar and baz'
```

#### key()

Returns the key of the current item of the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->key();
// returns 0

$collection = new Collection(['foo' => 10, 'bar' => 15, 'baz' => 20]);
return $collection->key();
// returns 'foo'
```

#### keys()

Returns a new collection with only the keys from the original collection.

```php
$collection = new Collection(['foo' => 10, 'bar' => 15, 'baz' => 20]);
return $collection->keys();
// returns ['foo', 'bar', 'baz']
```

#### map()

Returns a new collection with the results of the callback applied to each item.

```php
$collection = new Collection([1, 2, 3, 4, 5]);
return $collection->map(function($item) {
    return $item * 2;
});
// returns [2, 4, 6, 8, 10]
```

#### merge()

Merges the given array or collection with the current collection. This uses the `array_merge()` function, so any duplicate keys will be overwritten by the new values.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->merge(['a', 'b', 'c']);
// returns ['foo', 'bar', 'baz', 'a', 'b', 'c']
```

#### pop()

Removes and returns the last item in the collection, shortening the items by 1. If no items are present `null` is returned.

NOTE: This method is NOT immutable and affects the original collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->pop();
// returns 'baz'
// $collection = ['foo', 'bar']
```

#### push()

Adds one or more items to the end of the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->push('a', 'b', 'c');
// returns ['foo', 'bar', 'baz', 'a', 'b', 'c']
```

#### reduce()

Returns the value created when the callback is applied to each item in the collection, passing the result into the next callback, and so on.

```php
$collection = new Collection([1, 2, 3, 4, 5]);
return $collection->reduce(function($carry, $item) {
    return $carry + $item;
});
// returns 15
```

#### reverse()

Returns a new collection with the items in reverse order.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->reverse();
// returns ['baz', 'bar', 'foo']
```

#### shift()

Removes and returns the first item in the collection, shortening the items by 1. If no items are present `null` is returned.

NOTE: This method is NOT immutable and affects the original collection.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->shift();
// returns 'foo'
// $collection = ['bar', 'baz']
```

#### slice()

Returns a new collection with only the items between and including the given start and end indexes. If no end index is provided, it will return all items from the start index to the end of the collection.

```php
$collection = new Collection(['foo', 'bar', 'baz', 'a', 'b', 'c']);
return $collection->slice(2, 4);
// returns ['baz', 'a', 'b']
```

#### sort()

Returns a new collection with the items sorted in ascending order by the given callback. If no callback is provided, it will sort the items alphabetically.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->sort();
// returns ['bar', 'baz', 'foo']

$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->sort(function($a, $b) {
    return $a <=> $b;
});
// returns ['bar', 'baz', 'foo']
```

#### sortDesc()

Returns a new collection with the items sorted in descending order by the given callback. If no callback is provided, it will attempt sort the items alphabetically.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->sortDesc();
// returns ['foo', 'baz', 'bar']

$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->sortDesc(function($a, $b) {
    return $a <=> $b;
});
// returns ['foo', 'baz', 'bar']
```

#### splice()

Returns a new collection with the portion of the items of the collection either removed or replaced with a new item(s).

> NOTE: This method is NOT idempotent and affects the original collection.

```php
$collection = new Collection(['red', 'green', 'yellow', 'blue']);
$new = $collection->splice(2);
// $new = ['yellow', 'blue']
// $collection = ['red', 'green']
```

You can also replace the removed items with new items. The second parameter is the number of items to remove. The third parameter is an array of items to insert into the original collection.

```php
$collection = new Collection(['red', 'green', 'yellow', 'blue']);
$new = $collection->splice(2, 1, 'orange');
// $new = ['yellow']
// $collection = ['red', 'green', 'orange', 'blue']
```

#### sum($key)

Returns the sum of all items in the collection. If `$key` is provided, it will return the sum of $key within each item.

```php
$collection = new Collection([1, 2, 3, 4, 5]);
return $collection->sum();
// returns 15
```

```php
$collection = new Collection([
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 30],
    ['foo' => 40],
]);
return $collection->sum('foo');
// returns 100
```

#### unique()

Returns a new collection with only unique items from the original collection.

```php
$collection = new Collection([1, 2, 2, 3, 1, 5, 1, 3]);
return $collection->unique();
// returns [0 => 1, 1 => 2, 3 => 3, 5 => 5]
```

You can also pass the column name as a `string` or multiple columns as `array` as parameter if you work with associative arrays or objects.

```php
$collection = new Collection([
    ['id' => 1, 'name' => 'John'],
    ['id' => 2, 'name' => 'Jane'],
    ['id' => 1, 'name' => 'Jim'],
    ['id' => 3, 'name' => 'Joe'],
]);
return $collection->unique('id');
// returns [
//    0 => ['id' => 1, 'name' => 'John'],
//    1 => ['id' => 2, 'name' => 'Jane'],
//    3 => ['id' => 3, 'name' => 'Joe'],
//]
```

#### values()

Returns a new collection with only the values from the original collection.

```php
$collection = new Collection([
    'foo' => 10,
    'bar' => 15,
    'baz' => 20
]);
return $collection->values();
// returns [10, 15, 20]
```

#### valid()

Returns boolean `true` if the current item is valid (has a key), `false` otherwise.

```php
$collection = new Collection(['foo', 'bar', 'baz']);
return $collection->valid();
// returns true
```

#### when($callback)

Returns the result of the callback if the collection is not empty. If the collection is empty, it will return the collection.

```php
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
// returns ['b' => 2, 'd' => 4]
```

#### unless($callback)

Returns a new collection that contains all items where the callback evaluates to `false`.

```php
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
// returns ['a' => 1, 'c' => 3, 'e' => 5]
```
