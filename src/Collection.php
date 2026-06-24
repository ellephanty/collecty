<?php

class Collection implements IteratorAggregate, Countable
{
    protected $items = array();

    public function __construct(array $items = array())
    {
        $this->items = $items;
    }

    public static function make(array $items = array())
    {
        return new static($items);
    }

    public function all()
    {
        return $this->items;
    }

    public function set(array $items)
    {
        $this->items = $items;

        return $this;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    public function cloneCollection()
    {
        return clone $this;
    }

    public function first()
    {
        return empty($this->items)
            ? null
            : reset($this->items);
    }

    public function last()
    {
        return empty($this->items)
            ? null
            : end($this->items);
    }

    public function get($index)
    {
        return isset($this->items[$index])
            ? $this->items[$index]
            : null;
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function push($item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function pop()
    {
        return array_pop($this->items);
    }

    public function concat(array $items)
    {
        return new static(
            array_merge($this->items, $items)
        );
    }

    public function filter(callable $callback)
    {
        return new static(
            array_values(
                array_filter($this->items, $callback)
            )
        );
    }

    public function map(callable $callback)
    {
        return new static(
            array_map($callback, $this->items)
        );
    }

    public function contains(callable $callback)
    {
        foreach ($this->items as $item) {
            if ($callback($item)) {
                return true;
            }
        }

        return false;
    }

    public function firstWhere(callable $callback)
    {
        foreach ($this->items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    public function random()
    {
        if (empty($this->items)) {
            return null;
        }

        return $this->items[array_rand($this->items)];
    }

    public function unique()
    {
        return new static(
            array_map(
                'unserialize',
                array_unique(
                    array_map('serialize', $this->items)
                )
            )
        );
    }
}