<?php

use Ellephanty\Collecty\BaseIterable;

class BaseIterableItemTransformer extends BaseIterable
{
    public function item($item)
    {
        return $item * 2;
    }
}

class BaseIterableTest extends PHPUnit_Framework_TestCase
{
    public function testToArrayWithArray()
    {
        $it = new BaseIterable(array(1, 2, 3));

        $this->assertEquals(array(1, 2, 3), $it->toArray());
    }

    public function testToArrayWithObjectItems()
    {
        $obj = new stdClass();
        $obj->items = array(4, 5, 6);

        $it = new BaseIterable($obj);

        $this->assertEquals(array(4, 5, 6), $it->toArray());
    }

    public function testGetIteratorForeach()
    {
        $it = new BaseIterable(array(1, 2, 3));

        $result = array();

        foreach ($it as $item) {
            $result[] = $item;
        }

        $this->assertEquals(array(1, 2, 3), $result);
    }

    public function testItemTransformation()
    {
        $it = new BaseIterableItemTransformer(array(1, 2, 3));

        $result = array();

        foreach ($it as $item) {
            $result[] = $item;
        }

        $this->assertEquals(array(2, 4, 6), $result);
    }
}