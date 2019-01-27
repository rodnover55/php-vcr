<?php

namespace VCR;

use VCR\Interfaces\Request;
use VCR\Interfaces\Response;

class ResourceFactory
{
    const DEFAULT_TYPE = Type::HTTP;

    /** @var Configuration */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $request
     *
     * @return Request
     */
    public function makeRequest(array $request)
    {
        return $this->make('request', $request);
    }

    /**
     * @param array $response
     * @return Response
     */
    public function makeResponse(array $response)
    {
        return $this->make('response', $response);
    }

    public function makeClient($instance)
    {
        $type = $this->getType($instance);

        return $this->make('client', array(
            'type' => $type
        ));
    }

    protected function make($resource, array $data)
    {
        $type = isset($data['type']) ? $data['type'] : self::DEFAULT_TYPE;

        $factories = $this->getFactories();

        if (!isset($factories[$type])) {
            throw new \InvalidArgumentException("Request type '{$type}' doesn't exists");
        }

        $definition = $factories[$type][$resource];

        return call_user_func($definition['creator'], $data);
    }

    public function getType($instance)
    {
        foreach ($this->getFactories() as $type => $resources) {
            foreach ($resources as $definition) {
                if (is_a($instance, $definition['class'])) {
                    return $type;
                }
            }
        }

        $class = get_class($instance);

        throw new \RuntimeException("Type for instance '{$class}' not found");

    }

    protected function getFactories() {
        return $this->config->getFactories();
    }
}
