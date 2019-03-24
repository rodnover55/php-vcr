<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\PDO;
use VCR\Drivers\PDO\Response;
use VCR\Interfaces\Request;
use VCR\PDO\Mock\Hook;

class PDOCreateTest extends TestCase
{
    public function testExceptionHookDisabled()
    {
        $expected = null;
        $actual = null;

        try {
            new \PDO('trtr');
        } catch (\Exception $e) {
            $expected = $e;
        }

        $this->assertNotEmpty($expected, 'PDO should no be created');

        try {
            new PDO('trtr');
        } catch (\Exception $e) {
            $actual = $e;
        }

        $this->assertEquals($expected, $actual);
    }

    public function testExceptionHookEnabled()
    {
        $response = Response::fromArray([
            'result' => null,
            'method' => 'create',
            'error' => [
                'class' => 'PDOException',
                'code' => 0,
                'message' => 'invalid data source name'
            ]
        ]);

        $builder = $this->getMockBuilder('VCR\Drivers\PDO\PDO');

        $builder
            ->setMethods(['getLibraryHook'])
            ->disableOriginalConstructor();

        /** @var PDO|\PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $builder->getMock();

        $hook = new Hook();
        $hook->enable(function (Request $request) use ($response) {
            return $response;
        });

        $pdo->expects($this->any())->method('getLibraryHook')->willReturn($hook);

        $exception = null;

        try {
            $pdo->__construct('sqlite::memory:');
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->assertNotEmpty($exception, 'Exception should be thrown');

        $this->assertEquals($response->getError(), [
            'class' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ]);
    }
}
