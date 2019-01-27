<?php

namespace VCR\Event;

use VCR\Request;
use VCR\Cassette;
use VCR\Configuration;
use VCR\Storage;
use VCR\ResourceFactory;

class BeforePlaybackEventNewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BeforePlaybackEvent
     */
    private $event;

    protected function setUp()
    {
        $config = new Configuration();
        $this->event = new BeforePlaybackEvent(
            new Request('GET', 'http://example.com'),
            new Cassette('test', $config, new Storage\Blackhole(), new ResourceFactory($config))
        );
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('VCR\Request', $this->event->getRequest());
    }

    public function testGetCassette()
    {
        $this->assertInstanceOf('VCR\Cassette', $this->event->getCassette());
    }
}
