<?php

namespace VCR\Drivers\PDO;

use PDO as ParentPDO;
use VCR\VCRFactory;

class PDO extends ParentPDO
{
    /** @var Hook */
    private $hook;
    /** @var array  */
    private $connection;
    /** @var ParentPDO */
    private $pdo;

    private $lastErrorInfo;

    public function __construct($dsn, $username = null, $passwd = null, array $options = null)
    {
        $this->connection = array(
            'dsn' => $dsn,
            'username' => $username,
            'password' => $passwd,
            'options' => $options
        );

        $hook = $this->getLibraryHook();

        if ($hook->isEnabled()) {
            $response = $hook->create($this->connection);

            $error = $response->getError();

            if (isset($error)) {
                $reflection = new \ReflectionClass($error['class']);

                /** @var \Exception $exception */
                $exception = $reflection->newInstanceWithoutConstructor();


                $this->setProperty($reflection, $exception, 'code', $error['code']);
                $this->setProperty($reflection, $exception, 'message', $error['message']);

                throw $exception;
            }
        } else {
            $this->getPDO();
        }

        Client::register($this);
    }

    protected function setProperty(\ReflectionClass $reflection, $exception, $property, $value)
    {
        $propertyReflection = $reflection->getProperty($property);
        $isPublic = $propertyReflection->isPublic();

        if (!$isPublic) {
            $propertyReflection->setAccessible(true);
        }

        try {
            $propertyReflection->setValue($exception, $value);
        } finally {
            if (!$isPublic) {
                $propertyReflection->setAccessible(false);
            }
        }
    }

    public function prepare($statement, $options = null)
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            $pdo = $this->getPDO();

            if ($options === null) {
                return $pdo->prepare($statement);
            } else {
                return $pdo->prepare($statement, $options);
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
            $pdo = $this->getPDO();

            return $pdo->exec($statement);
        }

        $response = $hook->exec($this->connection, $statement);

        $this->setErrorInfo($response);

        return $response->getResult();
    }

    public function query($statement, $mode = ParentPDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        $hook = $this->getLibraryHook();

        if (!$hook->isEnabled()) {
            $pdo = $this->getPDO();

            switch ($mode) {
                case ParentPDO::ATTR_DEFAULT_FETCH_MODE:
                    return $pdo->query($statement);
                case ParentPDO::FETCH_OBJ:
                    return $pdo->query($statement, $mode);
                default:
                    return $pdo->query($statement, $mode, $arg3, $ctorargs);

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
            $pdo = $this->getPDO();

            $this->lastErrorInfo = $pdo->errorInfo();
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

    protected function getPDO()
    {
        if (empty($this->pdo)) {
            $this->pdo = new ParentPDO(
                $this->connection['dsn'],
                $this->connection['username'],
                $this->connection['password'],
                $this->connection['options']
            );
        }

        return $this->pdo;
    }

    /**
     * @return array
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
