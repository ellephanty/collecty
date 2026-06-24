<?php

namespace Ellephanty\Collecty;

class Entity
{
    protected $object;

    public function __construct($object = null)
    {
        // 🔥 NORMALIZACIÓN TOTAL (clave del fix)
        if (is_array($object)) {
            $object = (object) $object;
        }

        $this->object = $object;
    }

    public function __get($property)
    {
        // caso object
        if (is_object($this->object)) {

            if (isset($this->object->$property)) {
                return $this->object->$property;
            }

            return null;
        }

        // caso array fallback (por seguridad)
        if (is_array($this->object) && isset($this->object[$property])) {
            return $this->object[$property];
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