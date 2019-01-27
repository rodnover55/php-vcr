<?php

namespace VCR;

use org\bovigo\vfs\vfsStream;

/**
 * Test integration of PHPVCR with PHPUnit.
 */
class CassetteNewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Cassette
     */
    private $cassette;

    public function setUp()
    {
        vfsStream::setup('test');
        $config = new Configuration();

        $this->cassette = new Cassette(
            'test',
            $config,
            new Storage\Yaml(vfsStream::url('test/'), 'json_test'),
            new ResourceFactory($config)
        );
    }

    public function testInvalidCassetteName()
    {
        $this->setExpectedException('\VCR\VCRException', 'Cassette name must be a string, array given.');
        $config = new Configuration();

        new Cassette(
            array(),
            $config,
            new Storage\Yaml(vfsStream::url('test/'), 'json_test'),
            new ResourceFactory($config)
        );
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->cassette->getName());
    }

    public function testDontOverwriteRecord()
    {
        $request = new Request('GET', 'https://example.com');
        $response1 = new Response(200, array(), 'sometest');
        $response2 = new Response(200, array(), 'sometest');
        $this->cassette->record($request, $response1);
        $this->cassette->record($request, $response2);

        $this->assertEquals($response1->toArray(), $this->cassette->playback($request)->toArray());
    }

    public function testPlaybackAlreadyRecordedRequest()
    {
        $request = new Request('GET', 'https://example.com');
        $response = new Response(200, array(), 'sometest');
        $this->cassette->record($request, $response);

        $this->assertEquals($response->toArray(), $this->cassette->playback($request)->toArray());
    }

    public function testHasResponseNotFound()
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertFalse($this->cassette->hasResponse($request), 'Expected false if request not found.');
    }

    public function testHasResponseFound()
    {
        $request = new Request('GET', 'https://example.com');
        $response = new Response(200, array(), 'sometest');
        $this->cassette->record($request, $response);

        $this->assertTrue($this->cassette->hasResponse($request), 'Expected true if request was found.');
    }
}
