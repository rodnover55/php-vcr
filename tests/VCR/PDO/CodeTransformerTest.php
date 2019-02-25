<?php

namespace VCR\PDO;

use lapistano\ProxyObject\ProxyBuilder;

class CodeTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider codeSnippetProvider
     */
    public function testTransformCode($expected, $code)
    {
        $proxy = new ProxyBuilder('\VCR\Drivers\PDO\CodeTransform');
        $filter = $proxy
            ->setMethods(array('transformCode'))
            ->getProxy();

        $this->assertEquals($expected, $filter->transformCode($code));
    }

    public function codeSnippetProvider()
    {
        return array(
            // Simple change in root namespace
            // Simple change in nonroot namespace
            // Change with import
            // Change with import with alias
            // Nonchanging for non PDO alias to PDO

            // Change extends class
            // Nonchaging for not \PDO class
            // Nonchanging in string
            array('new \VCR\Drivers\PDO\PDO(', 'new \PDO('),
            array('new \VCR\Drivers\PDO\PDO(', 'new PDO('),
            array('extends \VCR\Drivers\PDO\PDO', 'extends \PDO'),
            array('extends \PDOStatement', 'extends \PDOStatement'),
            array("extends \VCR\Drivers\PDO\PDO\n", "extends \PDO\n"),
            array("extends \VCR\Drivers\PDO\PDO {", "extends \PDO {"),
            array("extends \VCR\Drivers\PDO\PDO{", "extends \PDO{"),
        );
    }
}
