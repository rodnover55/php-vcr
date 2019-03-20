<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\PDO;
use VCR\Drivers\PDO\Response;
use VCR\Drivers\PDO\Statement;
use VCR\Interfaces\Request;
use VCR\PDO\Mock\Hook;

class PDOEnabledHookTest extends TestCase
{
    /** @var PDO */
    private $pdo;
    /** @var Hook */
    private $hook;
    /** @var Response */
    private $response;

    protected function setUp()
    {
        parent::setUp();


        $builder = $this->getMockBuilder('VCR\Drivers\PDO\PDO');

        $builder
            ->setMethods(['getLibraryHook'])
            ->setConstructorArgs(['sqlite::memory:']);

        $this->pdo = $builder->getMock();

        $this->hook = new Hook();

        $this->hook->enable(function (Request $request) {
            return $this->response;
        });

        $this->pdo->expects($this->any())->method('getLibraryHook')->willReturn($this->hook);
    }

    public function testQuery()
    {
        $error = [
            'info' => ['06660', null, null]
        ];

        $this->response = Response::fromArray([
            'result' => [[0, 1, 2]],
            'method' => 'query',
            'error' => $error
        ]);

        $statement = $this->pdo->query('sql');

        $this->assertInstanceOf('VCR\Drivers\PDO\Statement', $statement);
        $this->assertEquals([
            'result' => $this->response->getResult(),
            'error' => $error['info']
        ], [
            'result' => iterator_to_array($statement),
            'error' => $this->pdo->errorInfo()
        ]);
    }

    public function testExec()
    {
        $error = [
            'info' => ['00000', null, null]
        ];

        $this->response = Response::fromArray([
            'result' => 5,
            'method' => 'exec',
            'error' => $error
        ]);

        $count = $this->pdo->exec('sql');

        $this->assertEquals([
            'result' => $this->response->getResult(),
            'error' => $error['info']
        ], [
            'result' => $count,
            'error' => $this->pdo->errorInfo()
        ]);
    }

    public function testPrepared()
    {
        $error = [
            'info' => ['06660', null, null]
        ];

        $this->response = Response::fromArray([
            'result' => [[0, 1, 2]],
            'method' => 'query',
            'error' => $error
        ]);

        /** @var Statement $statement */
        $statement = $this->pdo->prepare('sql');

        $this->assertInstanceOf('VCR\Drivers\PDO\Statement', $statement);
        $this->assertEquals($this->response->isSuccess(), $statement->execute([1]));

        $this->assertEquals([
            'result' => $this->response->getResult(),
            'error' => $error['info']
        ], [
            'result' => iterator_to_array($statement),
            'error' => $statement->errorInfo()
        ]);
    }
}
