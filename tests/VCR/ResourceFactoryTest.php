<?php

namespace VCR;

class ResourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testChangeConfiguration()
    {
        $config = new Configuration();

        $factory = new ResourceFactory($config);

        $config->setFactories(array(
                Type::HTTP => array(
                    'test' => 'stdClass'
                )
            ));

        $this->assertEquals(Type::HTTP, $factory->getType(new \stdClass()));
    }

    /**
     * @dataProvider instancesProvider
     * @param mixed $instance
     * @param string $type
     */
    public function testType($instance, $type)
    {
        $factory = new ResourceFactory(new Configuration());

        $this->assertEquals($type, $factory->getType($instance));
    }

    public function instancesProvider()
    {
        return array(
            'http-request' => array(
                new Request('GET', '/'),
                Type::HTTP
            ),
            'http-response' => array(
                new Response(200),
                Type::HTTP
            )
        );
    }
}
