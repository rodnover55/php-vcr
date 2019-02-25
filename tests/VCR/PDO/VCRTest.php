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
    public static function setupBeforeClass()
    {
        VCR::configure()->setCassettePath('tests/fixtures');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Library hooks don't exist: pdo
     */
    public function testPDONotLoadedByDefault()
    {
        VCR::configure()->enableLibraryHooks('pdo');
    }

    public function testShouldInterceptPDO()
    {
        $configure = VCR::configure();
        $configure
            ->registerDriver('pdo', Registrar::config())
            ->enableLibraryHooks('pdo');

        VCR::turnOn();
        VCR::insertCassette('pdo.yml');

        $result = $this->doPDO();

        VCR::eject();
        VCR::turnOff();

        $this->assertEquals('5', $result['test']);
    }


    protected function doPDO()
    {
        require_once 'Fixtures/Connector.php';

        $connector = new Connector();

        return $connector->exec();
    }
}
