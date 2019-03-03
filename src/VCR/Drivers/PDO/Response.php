<?php

namespace VCR\Drivers\PDO;

use VCR\Interfaces\Response as ResponseInterface;
use VCR\Type;
use PDOStatement;

class Response implements ResponseInterface
{
    /** @var mixed */
    private $result;
    /** @var string */
    private $method;
    /** @var array|null */
    private $error;

    public function toArray()
    {
        return array(
            'type' => Type::PDO,
            'method' => $this->method,
            'result' => $this->result,
            'error' => $this->error
        );
    }

    public static function fromArray(array $data)
    {
        $response = new static();
        $response->result = $data['result'];
        $response->method = $data['method'];
        $response->error = $data['error'];

        return $response;
    }

    public static function fromQuery(PDOStatement $query, $error)
    {
        return static::fromArray(array(
            'result' => $query->fetchAll(),
            'method' => 'query',
            'error' => $error
        ));
    }

    public static function fromExec($count, $error)
    {
        return static::fromArray(array(
            'result' => $count,
            'method' => 'exec',
            'error' => $error,
        ));
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return array|null
     */
    public function getError()
    {
        return $this->error;
    }
}
