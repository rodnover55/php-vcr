<?php

namespace VCR\Drivers\PDO;

use PDO;
use PDOStatement;

class Statement extends PDOStatement implements \IteratorAggregate
{
    /** @var PDOStatement|null */
    private $statement;

    private $response;

    /** @var Response|PDOStatement $data */
    public function __construct($data)
    {
        if ($data instanceof PDOStatement) {
            $this->statement = $data;

            $data = $this->statement->fetchAll();

            return;
        }

        $this->response = $data;
    }

    public function execute($input_parameters = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = null, $driver_options = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function rowCount()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function fetchColumn($column_number = 0)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function fetchObject($class_name = null, $ctor_args = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function errorCode()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function errorInfo()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function setAttribute($attribute, $value)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function getAttribute($attribute)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function columnCount()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function getColumnMeta($column)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function setFetchMode($mode, $classNameObject = null, $ctorarfg = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function nextRowset()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function closeCursor()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function debugDumpParams()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->response->getResult());
    }
}
