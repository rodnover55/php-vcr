<?php

namespace VCR\Drivers\PDO;

use VCR\Interfaces\Request as RequestInterface;
use VCR\Type;

class Request implements RequestInterface
{
    private $connection;
    private $method;
    private $statement;
    private $options;

    /**
     * Returns true if specified request matches the current one
     * with specified request matcher callbacks.
     *
     * @param  RequestInterface $request Request to check if it matches the current one.
     * @param  \callable[] $requestMatchers Request matcher callbacks.
     *
     * @throws \BadFunctionCallException If one of the specified request matchers is not callable.
     * @return boolean True if specified request matches the current one.
     */
    public function matches(RequestInterface $request, array $requestMatchers)
    {
        foreach ($requestMatchers as $matcher) {
            if (!is_callable($matcher)) {
                throw new \BadFunctionCallException(
                    'Matcher could not be executed. ' . print_r($matcher, true)
                );
            }

            if (call_user_func_array($matcher, array($this, $request)) === false) {
                return false;
            }
        }

        return true;
    }

    public function toArray()
    {
        return array(
            'type' => Type::PDO,
            'connection' => $this->connection,
            'method' => $this->method,
            'statement' => $this->statement,
            'options' => $this->options
        );
    }

    public static function fromArray(array $request)
    {
        return new static($request['connection'], $request['method'], $request['statement'], $request['options']);
    }

    public function __construct($connection, $method, $statement, $options)
    {
        $this->connection = $connection;
        $this->method = $method;
        $this->options = $options;
        $this->statement = $statement;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }
}
