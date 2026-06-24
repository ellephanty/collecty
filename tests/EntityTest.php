<?php

class AuthorsTest extends PHPUnit_Framework_TestCase
{
    public function testAuthorEntityAccess()
    {
        $data = array(
            'name' => 'Octavio Paz',
            'age' => 30
        );

        $author = new Author($data);

        $this->assertEquals('Octavio Paz', $author->name);
        $this->assertEquals(30, $author->age);
    }

    public function testAuthorIsEntity()
    {
        $author = new Author(array('name' => 'Test'));

        $this->assertInstanceOf(\Ellephanty\Collecty\Entity::class, $author);
    }

    public function testAuthorsCollectionReturnsAuthorEntities()
    {
        $data = array(
            array('name' => 'A'),
            array('name' => 'B'),
            array('name' => 'C')
        );

        $authors = new Authors($data);

        $items = array();

        foreach ($authors as $author) {
            $items[] = $author;
        }

        $this->assertInstanceOf('Author', $items[0]);
        $this->assertInstanceOf('Author', $items[1]);
        $this->assertInstanceOf('Author', $items[2]);
    }

    public function testAuthorsFirst()
    {
        $data = array(
            array('name' => 'First'),
            array('name' => 'Second')
        );

        $authors = new Authors($data);

        $first = $authors->first();

        $this->assertInstanceOf('Author', $first);
        $this->assertEquals('First', $first->name);
    }

    public function testAuthorsFilter()
    {
        $data = array(
            array('name' => 'A', 'active' => true),
            array('name' => 'B', 'active' => false),
            array('name' => 'C', 'active' => true)
        );

        $authors = new Authors($data);

        $active = $authors->where('active', true);

        $this->assertEquals(2, $active->count());
    }

    public function testAuthorsMapReturnsCollectionOfAuthors()
    {
        $data = array(
            array('name' => 'A'),
            array('name' => 'B')
        );

        $authors = new Authors($data);

        $mapped = $authors->map(function ($author) {
            return $author->name;
        });

        $this->assertEquals(array('A', 'B'), $mapped->toArray());
    }

    public function testAuthorsChainMethods()
    {
        $data = array(
            array('name' => 'A', 'age' => 20),
            array('name' => 'B', 'age' => 30),
            array('name' => 'C', 'age' => 40)
        );

        $authors = new Authors($data);

        $result = $authors
            ->where('age', '>', 25)
            ->map(function ($a) {
                return $a->name;
            });

        $this->assertEquals(array('B', 'C'), $result->toArray());
    }
}