<?php

namespace VCR\PDO;

use VCR\VCR;

/**
 * @requires extension sqlite3 >= 1
 * @requires extension pdo_sqlite
 */
class VCRDisableTest extends \PHPUnit_Framework_TestCase
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
}
