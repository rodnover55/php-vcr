<?php

namespace VCR\Drivers\PDO;

use VCR\Interfaces\Response as ResponseInterface;
use VCR\Type;
use PDOStatement;

class Response implements ResponseInterface
{
    /** @var array */
    private $rows;
    /** @var string */
    private $method;

    public function toArray()
    {
        return array(
            'type' => Type::PDO,
            'method' => $this->method,
            'rows' => $this->rows
        );
    }

    public static function fromArray(array $data)
    {
        $response = new static();
        $response->rows = $data['rows'];
        $response->method = $data['method'];

        return $response;
    }

    public static function fromQuery(PDOStatement $query)
    {
        return static::fromArray(array(
            'rows' => $query->fetchAll(),
            'method' => 'query'
        ));
    }

    public function getResult()
    {
        return $this->rows;
    }
}
