<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\Hook;
use VCR\Interfaces\Request;
use VCR\VCRFactory;

class HookTest extends TestCase
{
    /** @var Hook|\PHPUnit_Framework_MockObject_MockObject */
    private $hook;

    private $connection = array(
        'dsn' => 'sqlite::memory:',
        'username' => null,
        'password' => null,
        'options' => null
    );

    // TODO: Test for hook with snapshots
    protected function setUp()
    {
        parent::setUp();

        $builder = $this->getMockBuilder('VCR\Drivers\PDO\Hook');


        $builder
            ->setMethods(array('getResponse'))
            ->disableOriginalConstructor();


        $this->hook = $builder->getMock();

        $this->hook
            ->expects($this->once())
            ->method('getResponse')
            ->willReturnCallback(function (Request $request) {
                $this->assertEqualsSnapshot($request->toArray());
            });
    }

    public function testQuery()
    {
        $this->hook->query($this->connection, 'select 1 as test', array(
            'mode' => 19,
            'object' => null,
            'ctorargs' => array()
        ));
    }

    public function testExec()
    {
        $this->hook->exec($this->connection, 'create table test (id int)');
    }

    public function testExecPrepared()
    {
        $this->hook->execPrepared($this->connection, 'select ? as test', [1], null);
    }
}
