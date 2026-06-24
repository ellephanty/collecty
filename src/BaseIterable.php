<?php

namespace Ellephanty\Collecty;

use IteratorAggregate;
use ArrayIterator;
use Traversable;

class BaseIterable implements IteratorAggregate
{
    /**
     * @var mixed
     */
    protected $iterable;

    /**
     * BaseIterable constructor.
     * @param mixed $iterable
     */
    public function __construct($iterable)
    {
        $this->iterable = $iterable;
    }

    public function toArray()
    {
        if (is_array($this->iterable)) {
            return $this->iterable;
        }

        if (is_object($this->iterable) && isset($this->iterable->items)) {
            return $this->iterable->items;
        }

        return array();
    }

    /**
     * @return Traversable
     */
    public function getIterator()
    {
        $data = $this->toArray();
        $mapped = array();

        foreach ($data as $item) {
            $mapped[] = $this->item($item);
        }

        return new ArrayIterator($mapped);
    }

    /**
     * @param mixed $item
     */
    public function item($item)
    {
        return $item;
    }
}