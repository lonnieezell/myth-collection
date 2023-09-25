<?php

namespace Myth\Collection;

use ArrayAccess;
use Countable;
use ReflectionClass;
use ReflectionProperty;
use Traversable;
use Myth\Collection\CollectionTrait;

class Collection implements ArrayAccess, Countable, \Serializable
{
    use CollectionTrait;

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
    public static function from($items, callable $callback = null)
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
    public function offsetGet($offset): mixed
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
