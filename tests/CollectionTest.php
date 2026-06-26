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
    protected $authors;

    protected function setUp()
    {
        $this->authors = new Authors([
            [
                'id' => 1,
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
                'id' => 2,
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
                'id' => 3,
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
                'id' => 4,
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
        $result = $this->authors->pluck('publications')->flatten()->where('rating', '>', 5);
        $this->assertEquals(3, $result->count());
    }

    public function testWhereIn()
    {

        $result = $this->authors->whereIn('id', array(1, 3));

        $this->assertEquals(2, $result->count());
    }

    public function testSortBy()
    {
        $this->assertEquals(20, $this->authors->sortBy('age')->first()['age']);
    }

    public function testSum()
    {
        $this->assertEquals(140, $this->authors->sum('age'));
    }

    public function testTake()
    {
        $this->assertEquals(2, $this->authors->take(2)->count());
    }

    public function testTransform()
    {

        $this->authors->transform(function ($author) {
            $author->totalPublications = count($author->publications);
            return $author;
        });

        $this->assertEquals(7, $this->authors->sum('totalPublications'));
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

    public function testConcat()
    {

        $this->authors->concat([
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

        $this->authors->concat(new Collection([
            [
                'name' => 'Cristian',
                'age' => 20
            ]
        ]));

        $this->assertEquals(9, $this->authors->count());
    }
}
