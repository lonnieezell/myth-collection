<?php

namespace Myth\Collection;

trait CollectionTrait
{
    protected array $items = [];

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
     * Run the callable on each item in the collection.
     * You can stop processing the collection by returning false.
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
    }

    /**
     * Returns boolean indicating whether all items in the collection
     * satisfy the given callable.
     */
    public function every(callable $callback): bool
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
     * Returns a new collection with the items that satisfy the given callable.
     * If $useBoth is true, the callback will be passed both the value and the key.
     */
    public function filter(callable $callback, bool $useBoth=false): Collection
    {
        return new static(array_filter($this->toArray(), $callback, $useBoth ? ARRAY_FILTER_USE_BOTH : 0));
    }

    /**
     * method returns the first element in the provided array that satisfies the provided
     * testing function. If no element is found, FALSE is returned.
     */
    public function find(callable $callback)
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
    public function findIndex(callable $callback)
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
    public function join(string $glue = '', string $lastValue = null): string
    {
        $items = $this->items;

        if ($lastValue && count($items) > 1) {
            $items[count($items) -1] = $lastValue . $items[count($items) -1];
        }

        return (implode($glue, $items));
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
    public function map(callable $callback)
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
     * TODO make this immutable
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
    public function reduce(callable $callback, $initial = null): mixed
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
     *
     * @return mixed|null
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
    public function sort(callable $callback=null): Collection
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
    public function sortDesc(callable $callback=null): Collection
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
     * Returns a new collection with only unique items
     * from the original collection.
     */
    public function unique($columns = null): Collection
    {
        if ($columns === null) {
            return new static(array_unique($this->items));
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $keys        = [];
        $uniqueItems = [];

        foreach ($this->items as $index => $item) {
            $key = implode('|', array_map(function($column) use ($item) {
                return $item[$column];
            }, $columns));

            if (! isset($keys[$key])) {
                $keys[$key]          = true;
                $uniqueItems[$index] = $item;
            }
        }

        return new static($uniqueItems);
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
    public function when(callable $callback)
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
    public function unless(callable $callback)
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            if (! $callback($value, $key)) {
                $result[$key] = $value;
            }
        }

        return new static($result);
    }
}
