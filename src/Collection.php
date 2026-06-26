<?php

namespace Ellephanty\Collecty;

class Collection extends BaseIterable
{
    /**
     * 🔑 Acceso unificado: array | object | Entity
     */
    function getValue($item, $key)
    {
        foreach (explode('.', $key) as $segment) {
            if (is_array($item)) {
                $item = $item[$segment];
            } elseif (is_object($item)) {
                $item = $item->$segment;
            } else {
                return null;
            }
        }
        return $item;
    }

    public function set($array)
    {
        if (is_array($this->iterable)) {
            $this->iterable = $array;
        } else {
            $this->iterable->items = $array;
        }

        return $this;
    }

    public function cloneCollection()
    {
        return unserialize(serialize($this));
    }

    public function concat($array)
    {
        if (is_object($array) && method_exists($array, 'toArray')) {
            $array = $array->toArray();
        }

        $this->set(array_merge($this->toArray(), $array));
        return $this;
    }

    public function contains($callback)
    {
        foreach ($this as $item) {
            if ($callback($item)) {
                return true;
            }
        }

        return false;
    }

    public function take($limit){
        return $this->slice(0, $limit);
    }

    public function slice($start, $end){
        $array = $this->toArray();
        return new self(array_slice($array, $start, $end));
    }

    public function count()
    {
        return count($this->toArray());
    }

    public function diff($array)
    {
        return $this->filter(function ($item) use ($array) {
            return !in_array($item, $array);
        });
    }

    public function filter($callback)
    {
        $newItems = array();

        foreach ($this->toArray() as $item) {
            $currentItem = $this->item($item);

            if ($callback($currentItem)) {
                $newItems[] = $item;
            }
        }

        $clone = $this->cloneCollection();
        $clone->set($newItems);

        return $clone;
    }

    public function first()
    {
        $array = $this->toArray();

        if (!count($array)) {
            return null;
        }

        return $this->item($array[0]);
    }

    public function firstWhere($callback)
    {
        foreach ($this as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    public static function fromArray($array)
    {
        return new self($array);
    }

    public static function fromJson($json)
    {
        return new self(json_decode($json, true));
    }

    public function get($index)
    {
        $array = $this->toArray();

        if (!isset($array[$index])) {
            return null;
        }

        return $this->item($array[$index]);
    }

    public function isEmpty()
    {
        return !count($this->toArray());
    }

    public function last()
    {
        $array = $this->toArray();

        if (!count($array)) {
            return null;
        }

        return $this->item($array[count($array) - 1]);
    }

    public function map($callback)
    {
        $result = array();

        foreach ($this->toArray() as $item) {
            $result[] = $callback($this->item($item));
        }

        return new self($result);
    }

    public function pluck()
    {
        $args = func_get_args();
        $array = array();

        if (count($args) === 1) {
            foreach ($this->toArray() as $item) {
                $array[] = $this->getValue($item, $args[0]);
            }
        } else {
            foreach ($this->toArray() as $item) {
                $result = array();

                foreach ($args as $prop) {
                    $result[$prop] = $this->getValue($item, $prop);
                }

                $array[] = $result;
            }
        }

        return new self($array);
    }

    public function pop()
    {
        $array = $this->toArray();
        $element = array_pop($array);

        $this->set($array);

        return $this->item($element);
    }

    public function push($element)
    {
        $array = $this->toArray();
        $array[] = $element;

        $this->set($array);
    }

    public function random()
    {
        $array = $this->toArray();

        if (!count($array)) {
            return null;
        }

        return $this->item($array[array_rand($array)]);
    }

    public function sortBy($property, $order = 'asc')
    {
        $array = $this->toArray();

        usort($array, function ($a, $b) use ($property, $order) {

            $valueA = $this->getValue($a, $property);
            $valueB = $this->getValue($b, $property);

            $multiplier = $order === 'desc' ? -1 : 1;

            if (is_string($valueA) && is_string($valueB)) {
                return strcmp($valueA, $valueB) * $multiplier;
            }

            if ($valueA > $valueB) return 1 * $multiplier;
            if ($valueA < $valueB) return -1 * $multiplier;

            return 0;
        });

        $clone = $this->cloneCollection();
        $clone->set($array);

        return $clone;
    }

    public function sum($property)
    {
        $path = explode('.', $property);

        $getDeepValue = function ($item, $path) {
            foreach ($path as $key) {
                $item = $this->getValue($item, $key);
            }
            return $item;
        };

        $total = 0;

        foreach ($this->toArray() as $item) {
            $value = $getDeepValue($item, $path);
            $total += (float)$value;
        }

        return $total;
    }

    public function unique()
    {
        $seen = array();
        $result = array();

        foreach ($this->toArray() as $item) {
            $key = md5(json_encode($item));

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $item;
            }
        }

        $clone = $this->cloneCollection();
        $clone->set($result);

        return $clone;
    }

    public function where()
    {
        $args = func_get_args();

        // where(callback)
        if (count($args) === 1 && is_callable($args[0])) {
            return $this->filter($args[0]);
        }

        // where(field, value)
        if (count($args) === 2) {
            list($field, $value) = $args;

            return $this->filter(function ($item) use ($field, $value) {
                return $this->getValue($item, $field) == $value;
            });
        }

        // where(field, operator, value)
        if (count($args) === 3) {
            list($field, $operator, $value) = $args;

            return $this->filter(function ($item) use ($field, $operator, $value) {

                $itemValue = $this->getValue($item, $field);

                switch ($operator) {
                    case '=':
                    case '==':
                    case '===':
                        return $itemValue === $value;

                    case '!=':
                    case '!==':
                        return $itemValue !== $value;

                    case '>':
                        return $itemValue > $value;

                    case '>=':
                        return $itemValue >= $value;

                    case '<':
                        return $itemValue < $value;

                    case '<=':
                        return $itemValue <= $value;
                }

                throw new \Exception("Operator not supported");
            });
        }

        throw new \Exception("Invalid arguments");
    }

    public function whereIn($property, $values)
    {
        $values = $values instanceof self ? $values->toArray() : (array)$values;

        return $this->filter(function ($item) use ($property, $values) {

            $itemValue = $this->getValue($item, $property);

            return in_array($itemValue, $values);
        });
    }

    public function whereNotIn($property, $values)
    {
        $values = $values instanceof self ? $values->toArray() : (array)$values;

        return $this->filter(function ($item) use ($property, $values) {

            $itemValue = $this->getValue($item, $property);

            return !in_array($itemValue, $values);
        });
    }

    public function whereEmpty($property)
    {
        return $this->filter(function ($item) use ($property) {
            return empty($this->getValue($item, $property));
        });
    }

    public function whereNotEmpty($property)
    {
        return $this->filter(function ($item) use ($property) {
            return !empty($this->getValue($item, $property));
        });
    }

    public function whereNot($property, $value)
    {
        return $this->filter(function ($item) use ($property, $value) {
            return $this->getValue($item, $property) !== $value;
        });
    }

    public function orderBy($property, $order = 'asc')
    {
        $array = $this->toArray();

        usort($array, function ($a, $b) use ($property, $order) {

            $valueA = $this->getValue($a, $property);
            $valueB = $this->getValue($b, $property);

            if ($order === 'asc') {
                return $valueA > $valueB ? 1 : -1;
            }

            return $valueA < $valueB ? 1 : -1;
        });

        $this->set($array);

        return $this;
    }

    public function min($property)
    {
        $array = $this->toArray();

        if (!count($array)) {
            return null;
        }

        $min = $array[0];

        foreach ($array as $item) {
            if ($this->getValue($item, $property) < $this->getValue($min, $property)) {
                $min = $item;
            }
        }

        return $this->item($min);
    }

    public function flatten($depth = INF)
    {
        $result = array();

        foreach ($this->toArray() as $item) {
            $this->flattenItem($item, $result, $depth);
        }

        return new self($result);
    }

    private function flattenItem($item, &$result, $depth)
    {
        if ($depth === 0) {
            $result[] = $item;
            return;
        }

        if (is_array($item)) {

            // 👇 si es array asociativo, NO lo rompas
            if ($this->isAssoc($item)) {
                $result[] = $item;
                return;
            }

            // 👇 solo aplana listas
            foreach ($item as $subItem) {
                $this->flattenItem($subItem, $result, $depth - 1);
            }
        } else {
            $result[] = $item;
        }
    }

    public function transform($callback)
    {
        $items = [];

        foreach ($this->toArray() as $item) {
            $items[] = $callback($this->item($item));
        }

        $this->set($items);

        return $this;
    }

    private function isAssoc(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
