<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\FetchIterator;

class FetchIteratorTest extends TestCase
{
    /** @var FetchIterator */
    private $iterator;

    protected function setUp()
    {
        parent::setUp();

        $this->iterator = new FetchIterator([
            ['test' => 1, 0 => 1],
        ]);

        $this->iterator->rewind();
    }

    public function testSimple()
    {
        $row = $this->iterator->current();

        $this->assertEqualsSnapshot($row);
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnknown()
    {
        $this->iterator->setMode(-1);

        $this->iterator->current();
    }

    /**
     * @dataProvider modesProvider
     * @param $mode
     */
    public function testModes($mode)
    {
        $this->iterator->setMode($mode);

        $pdo = new \PDO('sqlite::memory:');

        $actualStatement = $pdo->query('select 1 as test');

        $this->assertEquals(
            $actualStatement->fetch($mode),
            $this->iterator->current()
        );
    }

    public function modesProvider()
    {
        return [
            'obj' => [\PDO::FETCH_OBJ]
        ];
    }
}
