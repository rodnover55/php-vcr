<?php

namespace VCR\Drivers\PDO;

use VCR\CodeTransform\AbstractCodeTransform;
use VCR\Interfaces\LibraryHook;
use VCR\Util\Assertion;
use VCR\Util\StreamProcessor;
use VCR\Interfaces\Request as RequestInterface;
use VCR\Interfaces\Response as ResponseInterface;

class Hook implements LibraryHook
{
    /**
     * @var callable
     */
    private $requestCallback;


    private $state = self::DISABLED;

    /**
     * @var AbstractCodeTransform
     */
    private $codeTransformer;

    /**
     * @var StreamProcessor
     */
    private $processor;

    /**
     * Creates a new cURL hook instance.
     *
     * @param AbstractCodeTransform  $codeTransformer
     * @param StreamProcessor $processor
     *
     * @throws \BadMethodCallException in case the cURL extension is not installed.
     */
    public function __construct(AbstractCodeTransform $codeTransformer, StreamProcessor $processor)
    {
        $this->processor = $processor;
        $this->codeTransformer = $codeTransformer;
    }

    /**
     * @param \Closure $requestCallback
     */
    public function enable(\Closure $requestCallback)
    {
        Assertion::isCallable($requestCallback, 'No valid callback for handling requests defined.');
        $this->requestCallback = $requestCallback;

        if ($this->isEnabled()) {
            return;
        }

        $this->codeTransformer->register();
        $this->processor->appendCodeTransformer($this->codeTransformer);
        $this->processor->intercept();


        $this->state = self::ENABLED;
    }

    public function disable()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->requestCallback = null;
        $this->state = self::DISABLED;
    }

    public function isEnabled()
    {
        return $this->state == self::ENABLED;
    }

    /**
     * @param RequestInterface|Request $request
     * @return ResponseInterface|Response
     */
    public function getResponse(RequestInterface $request)
    {
        return call_user_func($this->requestCallback, $request);
    }

    /**
     * @param $connection
     * @param $statement
     * @param $options
     *
     * @return Response|ResponseInterface
     */
    public function query($connection, $statement, $options)
    {
        $request = new Request($connection, 'query', $statement, array(
            'options' => $options
        ));

        return $this->getResponse($request);
    }

    public function exec($connection, $statement)
    {
        $request = new Request($connection, 'exec', $statement);

        return $this->getResponse($request);
    }

    public function execPrepared($connection, $statement, $bindings, $options)
    {
        $request = new Request($connection, 'prepared', $statement, [
            'options' => $options,
            'bindings' => $bindings,
        ]);

        return $this->getResponse($request);
    }

    public function create($connection)
    {
        $request = new Request($connection, 'create');

        return $this->getResponse($request);
    }

    public function getAttribute($connection, $attribute)
    {
        $request = new Request($connection, 'getAttribute', $attribute);

        return $this->getResponse($request);
    }
}
