<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\Registrar;
use VCR\PDO\Fixtures\Connector;
use VCR\VCR;

/**
 * @requires extension sqlite3 >= 1
 * @requires extension pdo_sqlite
 */
class VCRTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        VCR::configure()->setCassettePath('tests/fixtures');

        $configure = VCR::configure();
        $configure
            ->registerDriver('pdo', Registrar::config())
            ->enableLibraryHooks('pdo');

        VCR::turnOn();
        VCR::insertCassette('pdo.yml');
    }

    protected function tearDown()
    {
        VCR::eject();
        VCR::turnOff();

        parent::tearDown();
    }


    public function testQuery()
    {
        $result = $this->createConnector()->query();

        $this->assertEquals('5', $result['test']);
    }

    public function testExec()
    {
        $error = $this->createConnector()->exec();

        $this->assertEquals('near "id_change": syntax error', $error[2]);
    }

    public function testPrepare()
    {
        $result = $this->createConnector()->execPrepared();

        $this->assertCount(2, $result);
        $this->assertEquals('5', $result[0]['test']);
        $this->assertEquals('3', $result[1]['test']);
    }


    /**
     * @return Connector
     */
    protected function createConnector()
    {
        require_once 'Fixtures/Connector.php';

        return new Connector();
    }
}
