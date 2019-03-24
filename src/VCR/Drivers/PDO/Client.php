<?php

namespace VCR\Drivers\PDO;

use VCR\Interfaces\Client as ClientInterface;
use VCR\Interfaces\Request as RequestInterface;
use VCR\Interfaces\Response as ResponseInterface;

class Client implements ClientInterface
{
    // TODO: Separate equal connections
    /** @var PDO[] */
    private static $connections = array();

    /**
     * @param RequestInterface|Request $request
     *
     * @return ResponseInterface|Response
     */
    public function send(RequestInterface $request)
    {
        switch ($request->getMethod()) {
            case 'query':
                return $this->query($request);
            case 'exec':
                return $this->exec($request);
            case 'prepared':
                return $this->execPrepared($request);
            case 'create':
                return $this->createPDO($request);
            case 'getAttribute':
                return $this->getAttribute($request);
        }

        throw new \LogicException('Unknown method:' . $request->getMethod());
    }

    public static function fromArray(array $data)
    {
        return new Client();
    }

    public static function register(PDO $connection)
    {
        self::$connections[self::getConnectionID($connection->getConnection())] = $connection;
    }

    protected static function getConnectionID($connection)
    {
        return md5(serialize($connection));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function query(Request $request)
    {
        $connection = $this->getConnection($request);

        $extra = $request->getExtra();
        $options = $extra['options'];

        $result = $connection->query(
            $request->getStatement(),
            $options['mode'],
            $options['object'],
            $options['ctorargs']
        );

        return Response::fromQuery($result, $this->getError($connection));
    }

    protected function exec(Request $request)
    {
        $connection = $this->getConnection($request);
        $result = $connection->exec($request->getStatement());

        return Response::fromExec($result, $this->getError($connection));
    }

    protected function execPrepared(Request $request)
    {
        $connection = $this->getConnection($request);
        $statement = $connection->prepare($request->getStatement());

        $options = $request->getExtra();
        $bindings = $options['bindings'];

        if (count($bindings) == 0 || is_array(array_values($bindings)[0])) {
            foreach ($bindings as $param => $item) {
                list($value, $type) = $item;

                $statement->bindValue($param, $value, $type);
            }

            $statement->execute();
        } else {
            $statement->execute($bindings);
        }

        return Response::fromPrepared($statement, $this->getError($statement));
    }

    protected function createPDO(Request $request)
    {
        $exception = null;

        try {
            $connection = $request->getConnection();

            new \PDO($connection['dsn'], $connection['username'], $connection['password'], $connection['options']);
        } catch (\Exception $e) {
            $exception = $e;
        }

        return Response::fromException($exception);
    }

    protected function getAttribute(Request $request)
    {
        $connection = $this->getConnection($request);
        $result = $connection->getAttribute($request->getStatement());

        return Response::fromArray(array(
            'result' => $result,
            'method' => 'getAttribute',
            'error' => $this->getError($connection),
        ));
    }

    /**
     * @param Request $request
     *
     * @return PDO
     */
    protected function getConnection(Request $request)
    {
        return self::$connections[self::getConnectionID($request->getConnection())];
    }

    /**
     * @param \PDO|\PDOStatement $connection
     * @return array|null
     */
    protected function getError($connection)
    {
        $info = $connection->errorInfo();

        return ($info === null) ? null : array(
            'info' => $connection->errorInfo()
        );
    }
}
