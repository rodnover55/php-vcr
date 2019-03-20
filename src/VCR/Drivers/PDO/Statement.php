<?php

namespace VCR\Drivers\PDO;

use PDO;
use PDOStatement;
use VCR\VCRFactory;

class Statement extends PDOStatement implements \IteratorAggregate
{
    /** @var Hook */
    private $hook;
    /** @var array */
    private $connection;
    private $lastErrorInfo;


    /** @var string|null */
    private $statement;
    /** @var array|null */
    private $options;
    /** @var Response */
    private $response;
    /** @var \Iterator|null */
    private $iterator;

    /**
     * @param Response $response
     * @return Statement
     */
    public static function fromQuery(Response $response)
    {
        $statement = new static();

        return $statement
            ->setResponse($response);
    }

    public static function prepared($sql, $connection, $hook, $options = null)
    {
        $statement = new static();

        return $statement
            ->setStatement($sql)
            ->setOptions($options)
            ->setConnection($connection)
            ->setLibraryHook($hook);
    }

    public function execute($input_parameters = null)
    {
        $hook = $this->getLibraryHook();

        $response = $hook->execPrepared($this->connection, $this->statement, $input_parameters, $this->options);

        $this->setResponse($response);

        return $response->isSuccess();
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        $iterator = $this->getIterator();

        if (!$iterator->valid()) {
            return false;
        }

        $row = $iterator->current();
        $iterator->next();

        return $row;
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
        $rows = [];

        $iterator = $this->getIterator();

        while ($iterator->valid()) {
            $rows[] = $iterator->current();
            $iterator->next();
        }

        return $rows;
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
        return $this->lastErrorInfo;
    }

    protected function setErrorInfo(Response $response)
    {
        $error = $response->getError();

        if (isset($error['info'])) {
            $this->lastErrorInfo = $error['info'];
        }
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
        if (is_null($this->iterator)) {
            $this->iterator =  new \ArrayIterator($this->response->getResult());
        }

        return $this->iterator;
    }

    /**
     * @return string|null
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param string|null $statement
     *
     * @return $this
     */
    protected function setStatement($statement)
    {
        $this->statement = $statement;

        return $this;
    }

    /**
     * @return array
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $connection
     * @return Statement
     */
    protected function setConnection(array $connection)
    {
        $this->connection = $connection;

        return $this;
    }



    /**
     * @return array|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     *
     * @return $this
     */
    protected function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    protected function setResponse($response)
    {
        $this->response = $response;
        $this->setErrorInfo($response);
        $this->iterator = null;

        return $this;
    }



    protected function getLibraryHook()
    {
        if (empty($this->hook)) {
            $this->hook = VCRFactory::get('VCR\Drivers\PDO\Hook');
        }

        return $this->hook;
    }

    /**
     * @param Hook $hook
     *
     * @return $this
     */
    protected function setLibraryHook($hook)
    {
        $this->hook = $hook;

        return $this;
    }
}
