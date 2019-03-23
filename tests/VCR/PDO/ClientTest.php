<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\Client;
use VCR\Drivers\PDO\PDO;
use VCR\Drivers\PDO\Request;

class ClientTest extends TestCase
{
    /** @var Client */
    private $client;

    private $connection = array(
        'dsn' => 'sqlite::memory:',
        'username' => null,
        'password' => null,
        'options' => null
    );

    protected function setUp()
    {
        parent::setUp();

        $this->client = new Client();

        $connection = new PDO($this->connection['dsn']);

        Client::register($connection);
    }

    /**
     * @dataProvider requestsProvider
     *
     * @param Request $request
     */
    public function testSend(Request $request)
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr . ' on line ' . $errline . ' in file ' . $errfile);
        });

        try {
            $this->assertEqualsSnapshot($this->client->send($request)->toArray());
        } finally {
            restore_error_handler();
        }
    }

    public function requestsProvider()
    {
        return [
            'query' => [
                new Request($this->connection, 'query', 'select 1 as test', [
                    'options' => [
                        'mode' => 19,
                        'object' => null,
                        'ctorargs' => []
                    ]
                ])
            ],
            'exec' => [
                new Request($this->connection, 'exec', 'create table test (id int)')
            ],
            'execPreparedArgs' => [
                new Request($this->connection, 'prepared', 'select ? as test', [
                    'options' => null,
                    'bindings' => [1]
                ])
            ],
            'execPrepared' => [
                new Request($this->connection, 'prepared', 'select ? as test', [
                    'options' => null,
                    'bindings' => [1 => [21, \PDO::PARAM_INT]]
                ])
            ],
            'execPreparedEmpty' => [
                new Request($this->connection, 'prepared', 'select 13 as test', [
                    'options' => null,
                    'bindings' => []
                ])
            ]
        ];
    }
}
