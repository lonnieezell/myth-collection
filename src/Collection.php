<?php

namespace Myth\Collection;

use ArrayAccess;
use Closure;
use Countable;
use ReflectionClass;
use ReflectionProperty;
use Traversable;

class Collection implements ArrayAccess, Countable, \Serializable
{
    protected array $items = [];

    /*----------------------------------------------------------
     * Constructors
     *--------------------------------------------------------*/

    public function __construct(?array $items)
    {
        if (is_array($items)) {
            $this->items = $items;
        }
    }

    /**
     * Create a new instance of the Collection class
     * from any number of items.
     */
    public static function of(...$params)
    {
        return new static($params);
    }

    /**
     * Generates an instance of the Collection class from
     * iteratable class or object.
     */
    public static function from($items, Closure $callback = null)
    {
        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        if (is_object($items) && $items instanceof Traversable) {
            $items = iterator_to_array($items, true);
        }

        // Get public properties of the class (including static)
        if (is_object($items)) {
            $reflection = new ReflectionClass($items);
            $items = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
            $items = array_column($items, 'name');
        }

        if (is_array($items) && is_callable($callback)) {
            $items = array_map(function ($item) use ($callback) {
                return $callback($item);
            }, $items);
        }

        return new static($items);
    }

    /**
     * Get the collection of items as a plain array.
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Returns an ArrayIterator object with the items
     * in the collection.
     */
    public function items()
    {
        return new \ArrayIterator($this->items);
    }

    /*----------------------------------------------------------
     * Navigation
     *--------------------------------------------------------*/

    /**
     * Get the first item from the collection.
    */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Get the last item from the collection.
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Get the next item from the collection.
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * Get the previous item from the collection.
     */
    public function prev()
    {
        return prev($this->items);
    }

    /**
     * Returns the item at the specified offset.
     * If the offset is negative it will return
     * the item at the offset from the end of the collection.
     */
    public function at(int $index)
    {
        $index = $index < 0
            ? $index = count($this->items) + $index
            : $index;

        return $this->items[$index];
    }

    /*----------------------------------------------------------
     * Instance Methods
     *--------------------------------------------------------*/

    /**
     * Returns the average of all items in the collection.
     * If $key is present, will return the average of that key.
     */
    public function average(string $key = null)
    {
        $items = $this->items;

        if ($key) {
            $items = array_column($items, $key);
        }

        return array_sum($items) / count($items);
    }

    /**
     * Run the closure on each item in the collection.
     * You can stop processing the collection by returning false.
     */
    public function each(Closure $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
    }

    /**
     * Returns boolean indicating whether all items in the collection
     * satisfy the given closure.
     */
    public function every(Closure $callback): bool
    {
        return array_reduce($this->items, function ($carry, $item) use ($callback) {
            return $carry && $callback($item);
        }, true);
    }

    /**
     * Fills all the elements of collection from a
     * start index to an end index with a static value.
     * If end index is not specified, it will fill to the end of the collection.
     */
    public function fill(int $start, int $end=null, $value)
    {
        $end = $end ?? count($this->items) -1;
        if ($end <= 0) {
            return $this;
        }

        $startSlice = [];
        $endSlice = [];

        if ($start > 0) {
            $startSlice = array_slice($this->items, 0, $start);
            $endSlice = array_slice($this->items, $end + 1);
        }

        return new static(array_merge(
            $startSlice,
            array_fill($start, ($end - $start + 1) ?? 1, $value),
            $endSlice
        ));
    }

    /**
     * Returns a new collection with the items that satisfy the given closure.
     * $mode can be one of:
     *  - ARRAY_FILTER_USE_KEY  - pass key as the only argument to callback instead of the value
     *  - ARRAY_FILTER_USE_BOTH - pass both value and key as arguments to callback instead of the value
     */
    public function filter(Closure $callback, int $mode=null): Collection
    {
        return new static(array_filter($this->toArray(), $callback, $mode));
    }

    /**
     * method returns the first element in the provided array that satisfies the provided
     * testing function. If no element is found, FALSE is returned.
     */
    public function find(Closure $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns the index of the first element in the array that satisfies the provided
     * testing function.
     * Otherwise, it returns -1, indicating that no element passed the test.
     */
    public function findIndex(Closure $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $key;
            }
        }

        return -1;
    }

    /**
     * Creates a new array with all sub-array elements concatenated
     * into it recursively up to the specified depth.
     */
    public function flatten($depth = 1): Collection
    {
        $items = $this->items;
        $result = [];

        while ($items) {
            $item = array_shift($items);

            if (is_array($item)) {
                if ($depth > 1) {
                    $items = array_merge($items, $item);
                } else {
                    $result = array_merge($result, array_values($item));
                }
            } else {
                $result[] = $item;
            }
        }

        return new static($result);
    }

    /**
     * Returns a new collection with the items grouped by the given key.
     */
    public function groupBy(string $key): Collection
    {
        if (empty($this->items) || !array_key_exists($key, $this->items[0])) {
            return new static($this->items);
        }

        $result = [];
        foreach ($this->items as $item) {
            $result[$item[$key]][] = $item;
        }

        return new static($result);
    }

    /**
     * Returns a boolean indicating whether the collection
     * contians the given value.
     */
    public function includes($value): bool
    {
        return in_array($value, $this->items);
    }

    /**
     * Returns a boolean indicating whether the collection
     * is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Returns the index of the item if found.
     *
     * @return int|string
     */
    public function indexOf($value, bool $strict = false)
    {
        $result = array_search($value, $this->items, $strict);

        return $result === false ? -1 : $result;
    }

    /**
     * Return all values joined by a given string, where an
     * optional value can be inserted prior to the last value
     */
    public function join(string $glue=null, string $lastValue = null): string
    {
        $items = $this->items;

        if ($lastValue && count($items) > 1) {
            $items[count($items) -1] = $lastValue . $items[count($items) -1];
        }

        return(implode($glue, $items));
    }

    /**
     * Return the key of the current item.
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Returns a new collection with the keys of the collection.
     */
    public function keys(): Collection
    {
        return new static(array_keys($this->items));
    }

    /**
     * Returns a new collection with the results of applying
     * the callback to each item in the collection.
     */
    public function map(Closure $callback)
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * Merges the items in the passed in arrays or collections
     * to the end of the current collection.
     *
     * @param array|Collection $items
     */
    public function merge(...$arrays)
    {
        for ($i = 0; $i < count($arrays); $i++) {
            if ($arrays[$i] instanceof Collection) {
                $arrays[$i] = $arrays[$i]->toArray();
            }
        }

        $this->items = array_merge($this->items, ...$arrays);

        return $this;
    }

    /**
     * Pops the last item off the collection and returns it
     * shortening the items by 1. If no items are present,
     * null is returned.
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Pushes the given item(s) onto the end of the collection.
     */
    public function push(...$values)
    {
        array_push($this->items, ...$values);

        return $this;
    }

    /**
     * Returns the value created when the callback is applied
     * to each item in the collection, passing the result into the
     * next callback, and so on.
     */
    public function reduce(Closure $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Returns a new collection where the items have been reversed.
     */
    public function reverse(): Collection
    {
        $items = array_reverse($this->items, false);

        return new static($items);
    }

    /**
     * Shifts the first item off the collection and returns it.
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Returns a collection with a slice of the items
     * starting at the given index
     */
    public function slice(int $start, int $end = null): Collection
    {
        return new static(array_slice($this->items, $start, $end));
    }

    /**
     * Returns a new collection with the items sorted in ascending
     * order by the given callback.
     */
    public function sort(Closure $callback=null): Collection
    {
        $items = $this->items;

        if ($callback === null) {
            sort($items);
            return new static($items);
        }


        usort($items, function ($a, $b) use ($callback) {
            $a = $callback($a);
            $b = $callback($b);

            return $a <=> $b;
        });

        return new static($items);
    }

    /**
     * Returns a new collection with the items sorted in descending
     * order by the given callback.
     */
    public function sortDesc(Closure $callback=null): Collection
    {
        $items = $this->items;

        if ($callback === null) {
            rsort($items);
            return new static($items);
        }

        usort($items, function ($a, $b) use ($callback) {
            $a = $callback($a);
            $b = $callback($b);

            return $b <=> $a;
        });

        return new static($items);
    }


    /**
     * Returns a new collection with the portion of the items of the collection
     * either removed or replaced with a new item(s).
     */
    public function splice(int $offset, int $length=null, ...$replacements): Collection
    {
        if ($length === null) {
            $length = count($this->items) - $offset;
        }

        return new static(array_splice($this->items, $offset, $length, $replacements));
    }

    /**
     * Returns the sum of the values of the collection.
     * If $key is passed, will sum the values of the given key.
     * If $callback is passed, will sum the values of the given callback.
     */
    public function sum($key = null)
    {
        if ($key === null) {
            return array_sum($this->items);
        }

        if (is_string($key)) {
            return array_sum(array_column($this->items, $key));
        }

        $result = [];
        foreach ($this->items as $item) {
            $result[] = $key($item);
        }

        return array_sum($result);
    }

    /**
     * Returns a new collection with the values of the collection
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * Returns true if the current item is valid,
     * meaing it has a key.
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Evaluates the given callback for each item in the collection
     * and returns a new collection with the items where the
     * callback returns true.
     */
    public function when(Closure $callback)
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                $result[$key] = $value;
            }
        }

        return new static($result);
    }

    /**
     * Returns a new collection with the items where the
     * callback returns false.
     */
    public function unless(Closure $callback)
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            if (! $callback($value, $key)) {
                $result[$key] = $value;
            }
        }

        return new static($result);
    }

    /*----------------------------------------------------------
     * ArrayAccess
     *--------------------------------------------------------*/

    /**
     * Assigns a value to the object at the specified offset
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Returns the value at the specified offset, or null if not set
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset])
            ? $this->items[$offset]
            : null;
    }

    /**
     * Checks if an item exists at the given offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Unsets an item at the given offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /*----------------------------------------------------------
     * Countable
     *--------------------------------------------------------*/

    /**
     * Returns the total number of items in the collection
     */
    public function count(): int
    {
        return count($this->items);
    }

    /*----------------------------------------------------------
     * Serializable
     *--------------------------------------------------------*/

    /**
     * Serializes the collection
     */
    public function serialize(): ?string
    {
        return serialize($this->items);
    }

    /**
     * Unserializes the collection
     */
    public function unserialize($data)
    {
        $this->items = unserialize($data);
    }

    /*----------------------------------------------------------
     * Magic Methods
     *--------------------------------------------------------*/

    /**
     * Returns the array of items in the collection to
     */
    public function __serialize(): array
    {
        return $this->items;
    }

    /**
     * Unserializes the collection
     */
    public function __unserialize(array $items): void
    {
        $this->items = $items;
    }
}
