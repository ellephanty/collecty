<?php

namespace Ellephanty\Collecty;

use ArrayAccess;

class Entity implements ArrayAccess
{
    protected $object;

    public function __construct($object = null)
    {
        if (is_array($object)) {
            $object = (object) $object;
        }

        $this->object = $object;
    }

    // =====================
    // Object access ->age
    // =====================
    public function __get($property)
    {
        if (is_object($this->object)) {
            return $this->object->$property ? $this->object->$property : null;
        }

        if (is_array($this->object)) {
            return $this->object[$property] ? $this->object[$property] : null;
        }

        return null;
    }

    public function __isset($property)
    {
        if (is_object($this->object)) {
            return isset($this->object->$property);
        }

        if (is_array($this->object)) {
            return isset($this->object[$property]);
        }

        return false;
    }

    // =====================
    // Array access ['age']
    // =====================
    public function offsetExists($offset)
    {
        if (is_object($this->object)) {
            return isset($this->object->$offset);
        }

        if (is_array($this->object)) {
            return isset($this->object[$offset]);
        }

        return false;
    }

    public function offsetGet($offset)
    {
        if (is_object($this->object)) {
            return $this->object->$offset ? $this->object->$offset : null;
        }

        if (is_array($this->object)) {
            return $this->object[$offset] ? $this->object[$offset] : null;
        }

        return null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_object($this->object)) {
            $this->object->$offset = $value;
            return;
        }

        if (is_array($this->object)) {
            $this->object[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if (is_object($this->object)) {
            unset($this->object->$offset);
            return;
        }

        if (is_array($this->object)) {
            unset($this->object[$offset]);
        }
    }

    // =====================
    // helpers
    // =====================
    public function toJSON()
    {
        return $this->object;
    }

    public function toString()
    {
        return json_encode($this->object);
    }

    public function isEmpty()
    {
        if ($this->object === null) {
            return true;
        }

        if (is_array($this->object)) {
            return empty($this->object);
        }

        if (is_object($this->object)) {
            return empty((array) $this->object);
        }

        return false;
    }
}