# Myth:collection

Provides a fluent wrapper around an array of data.

## Creating a new collection

A new collection is created by creating a new instance of `Myth\Collection\Collection`. You can populate the items
in the collection at instantation by passing an array into the constructor.

```php
use Myth\Collection\Collection;

$collection = new Collection([1,2,3,4]);
```
