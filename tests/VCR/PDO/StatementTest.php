<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\Response;
use VCR\Drivers\PDO\Statement;

class StatementTest extends TestCase
{
    private $defaultResult;

    protected function setUp()
    {
        parent::setUp();

        $this->defaultResult = [
            ['test' => 1, 0 => 1],
            ['test' => 2, 0 => 2],
        ];
    }


    // TODO: Test different styles
    public function testFetchAllDefault()
    {
        $statement = $this->createStatement($this->defaultResult);

        $this->assertEquals($this->defaultResult, $statement->fetchAll());
    }

    public function testFetchAllNotFirst()
    {
        $statement = $this->createStatement($this->defaultResult);

        $this->assertEquals($this->defaultResult[0], $statement->fetch());
        $this->assertEquals([$this->defaultResult[1]], $statement->fetchAll());
    }

    public function testFetchDefault()
    {
        $statement = $this->createStatement($this->defaultResult);

        $this->assertEquals($this->defaultResult[0], $statement->fetch());
        $this->assertEquals($this->defaultResult[1], $statement->fetch());
    }

    public function testFetchEmpty()
    {
        $statement = $this->createStatement([]);

        $this->assertFalse($statement->fetch());
    }

    public function testFetchIterator()
    {
        $statement = $this->createStatement($this->defaultResult);

        $this->assertEquals($this->defaultResult, iterator_to_array($statement));
    }

    protected function createStatement($result)
    {
        return Statement::fromQuery(Response::fromArray([
            'method' => 'prepared',
            'result' => $result,
            'error' => [
                'info' => ['00000', null, null]
            ]
        ]));
    }
}
