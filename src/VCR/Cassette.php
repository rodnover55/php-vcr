<?php

namespace VCR;

use VCR\Interfaces\Request;
use VCR\Interfaces\Response;
use VCR\Storage\Storage;
use VCR\Util\Assertion;

/**
 * A Cassette records and plays back pairs of Requests and Responses in a Storage.
 */
class Cassette
{
    /**
     * Casette name
     * @var string
     */
    protected $name;

    /**
     * VCR configuration.
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Storage used to store records and request pairs.
     *
     * @var Storage
     */
    protected $storage;

    /**
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * Creates a new cassette.
     *
     * @param  string $name Name of the cassette.
     * @param  Configuration $config Configuration to use for this cassette.
     * @param  Storage $storage Storage to use for requests and responses.
     * @param ResourceFactory $resourceFactory
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        $name,
        Configuration $config,
        Storage $storage,
        ResourceFactory $resourceFactory = null
    ) {
        Assertion::string($name, 'Cassette name must be a string, ' . \gettype($name) . ' given.');

        $this->name = $name;
        $this->config = $config;
        $this->storage = $storage;
        // TODO: For backward compatibility. Remove later.
        $this->resourceFactory = $resourceFactory ?: VCRFactory::get('VCR\ResourceFactory');
    }

    /**
     * Returns true if a response was recorded for specified request.
     *
     * @param Request $request Request to check if it was recorded.
     *
     * @return boolean True if a response was recorded for specified request.
     */
    public function hasResponse(Request $request)
    {
        return $this->playback($request) !== null;
    }

    /**
     * Returns a response for given request or null if not found.
     *
     * @param Request $request Request.
     *
     * @return Response|null Response for specified request.
     */
    public function playback(Request $request)
    {
        foreach ($this->storage as $recording) {
            $storedRequest = $this->resourceFactory->makeRequest($recording['request']);
            if ($storedRequest->matches($request, $this->getRequestMatchers())) {
                return $this->resourceFactory->makeResponse($recording['response']);
            }
        }

        return null;
    }

    /**
     * Records a request and response pair.
     *
     * @param Request  $request  Request to record.
     * @param Response $response Response to record.
     *
     * @return void
     */
    public function record(Request $request, Response $response)
    {
        if ($this->hasResponse($request)) {
            return;
        }

        $recording = array(
            'request'  => $request->toArray(),
            'response' => $response->toArray()
        );

        $this->storage->storeRecording($recording);
    }

    /**
     * Returns the name of the current cassette.
     *
     * @return string Current cassette name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns true if the cassette was created recently.
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->storage->isNew();
    }

    /**
     * Returns a list of callbacks to configured request matchers.
     *
     * @return array List of callbacks to configured request matchers.
     */
    protected function getRequestMatchers()
    {
        return $this->config->getRequestMatchers();
    }
}
