<?php

namespace MyModule\Core;

use \Iterator;

/**
 * Class Collection
 * @package MyModule\Core
 */
class Collection implements Iterator
{
    /** @var array */
    protected $items;

    /**
     * Collection constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->rewind();
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function valid()
    {
        return key($this->items) !== null;
    }

    public function reduce($function, $initial = null)
    {
        return array_reduce($this->items, $function, $initial);
    }

    public function map($function)
    {
        return array_map($function, $this->items);
    }
}
