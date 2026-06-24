<?php

use Ellephanty\Collecty\Collection;
use Ellephanty\Collecty\Entity;

class Author extends Entity {}

class Authors extends Collection
{
    public function item($author)
    {
        return new Author($author);
    }
}

class CollectionTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->authors = new Authors([
            [
                'name' => 'Erick',
                'age' => 50,
                'publications' => [
                    [
                        'title' => 'Publicacion 1',
                        'rating' => 5
                    ],
                    [
                        'title' => 'Publicacion 2',
                        'rating' => 4
                    ],
                    [
                        'title' => 'Publicacion 3',
                        'rating' => 3
                    ]
                ]
            ],
            [
                'name' => 'Jorge pedroza',
                'age' => 30,
                'publications' => [
                    [
                        'title' => 'Publicacion 4',
                        'rating' => 9
                    ],
                    [
                        'title' => 'Publicacion 5',
                        'rating' => 8
                    ]
                ]
            ],
            [
                'name' => 'Manuel Tavarez',
                'age' => 40,
                'publications' => [
                    [
                        'title' => 'Publicacion 6',
                        'rating' => 7
                    ],
                ]
            ],
            [
                'name' => 'Mónica García',
                'age' => 20,
                'publications' => [
                    [
                        'title' => 'Publicacion 7',
                        'rating' => 0
                    ]
                ]
            ],
        ]);
    }

    public function testFromArray()
    {
        $this->assertEquals(4, $this->authors->count());
    }

    public function testFirstAndLast()
    {
        $this->assertEquals('Erick', $this->authors->first()->name);
        $this->assertEquals('Mónica García', $this->authors->last()->name);
    }

    public function testPush()
    {
        $this->authors->push(['name' => 'Manuel Tavarez']);
        $this->assertEquals(5, $this->authors->count());
    }

    public function testPop()
    {

        $last = $this->authors->pop();

        $this->assertEquals('Mónica García', $last->name);
        $this->assertEquals(3, $this->authors->count());
    }

    public function testMap()
    {
        $mapped = $this->authors->map(function ($author) {
            return $author->name;
        });

        $this->assertEquals(array('Erick', 'Jorge pedroza', 'Manuel Tavarez', 'Mónica García'), $mapped->toArray());
    }

    public function testFilter()
    {
        $filtered = $this->authors->filter(function ($author) {
            return $author->age > 30;
        });

        $this->assertEquals(2, $filtered->count());
    }

    public function testWhere()
    {
        // $result = $this->authors->where('publications', '>', 30);
    }

    public function testWhereIn()
    {
        $data = array(
            array('id' => 1),
            array('id' => 2),
            array('id' => 3)
        );

        $c = new Collection($data);

        $result = $c->whereIn('id', array(1, 3));

        $this->assertEquals(2, $result->count());
    }

    public function testSortBy()
    {
        $data = array(
            array('age' => 30),
            array('age' => 10),
            array('age' => 20)
        );

        $c = new Collection($data);

        $sorted = $c->sortBy('age');

        $this->assertEquals(10, $sorted->toArray()[0]['age']);
    }

    public function testSum()
    {
        $data = array(
            array('price' => 10),
            array('price' => 20),
            array('price' => 30)
        );

        $c = new Collection($data);

        $this->assertEquals(60, $c->sum('price'));
    }

    public function testUnique()
    {
        $data = array(1, 1, 2, 3, 3);

        $c = new Collection($data);

        $unique = $c->unique();

        $this->assertEquals(array(1, 2, 3), $unique->toArray());
    }

    public function testClone()
    {
        $c = new Collection(array(1, 2, 3));

        $clone = $c->cloneCollection();

        $clone->push(4);

        $this->assertEquals(array(1, 2, 3), $c->toArray());
        $this->assertEquals(array(1, 2, 3, 4), $clone->toArray());
    }

    public function testSetAndGet()
    {

        $this->authors->set([
            [
                'name' => 'Cristian',
                'age' => 20
            ],
            [
                'name' => 'Jorge pedroza',
                'age' => 30
            ],
            [
                'name' => 'Manuel Tavarez',
                'age' => 40
            ],
            [
                'name' => 'Erick',
                'age' => 50
            ]
        ]);

        $this->assertEquals(30, $this->authors->get(1)->age);
    }
}
