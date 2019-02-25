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
        }
        return new Response();
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

        $options = $request->getOptions();

        $result = $connection->query(
            $request->getStatement(),
            $options['mode'],
            $options['object'],
            $options['ctorargs']
        );

        return Response::fromQuery($result);
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
}
