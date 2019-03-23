<?php

namespace VCR\Drivers\PDO;

use PDO as ParentPDO;
use VCR\VCRFactory;

class PDO extends ParentPDO
{
    /** @var Hook */
    private $hook;

    private $connection;

    private $lastErrorInfo;

    public function __construct($dsn, $username = null, $passwd = null, array $options = null)
    {
        // TODO: Mock exception on connect

        $this->connection = array(
            'dsn' => $dsn,
            'username' => $username,
            'password' => $passwd,
            'options' => $options
        );

        parent::__construct($dsn, $username, $passwd, $options);

        Client::register($this);
    }


    public function prepare($statement, $options = null)
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            if ($options === null) {
                return parent::prepare($statement);
            } else {
                return parent::prepare($statement, $options);
            }
        }

        return Statement::prepared($statement, $this->connection, $hook, $options);
    }

    public function beginTransaction()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function commit()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function rollBack()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function inTransaction()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function exec($statement)
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            return parent::exec($statement);
        }

        $response = $hook->exec($this->connection, $statement);

        $this->setErrorInfo($response);

        return $response->getResult();
    }

    public function query($statement, $mode = ParentPDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            switch ($mode) {
                case ParentPDO::ATTR_DEFAULT_FETCH_MODE:
                    return parent::query($statement);
                case ParentPDO::FETCH_OBJ:
                    return parent::query($statement, $mode);
                default:
                    return parent::query($statement, $mode, $arg3, $ctorargs);

            }
        }

        $response = $hook->query($this->connection, $statement, array(
            'mode' => $mode,
            'object' => $arg3,
            'ctorargs' => $ctorargs
        ));

        $this->setErrorInfo($response);

        return Statement::fromQuery($response);
    }

    public function lastInsertId($name = null)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function errorCode()
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    public function errorInfo()
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            $this->lastErrorInfo = parent::errorInfo();
        }

        return $this->lastErrorInfo;
    }

    protected function setErrorInfo(Response $response)
    {
        $error = $response->getError();

        $this->lastErrorInfo = $error['info'];
    }

    public function sqliteCreateFunction($function_name, $callback, $num_args = -1, $flags = 0)
    {
        throw new \LogicException('Function ' . __FUNCTION__ . ' not implemented');
    }

    protected function getLibraryHook()
    {
        if (empty($this->hook)) {
            $this->hook = VCRFactory::get('VCR\Drivers\PDO\Hook');
        }

        return $this->hook;
    }

    /**
     * @return array
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
