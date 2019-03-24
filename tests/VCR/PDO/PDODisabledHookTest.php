<?php

namespace VCR\PDO;

use VCR\Drivers\PDO\PDO;

class PDODisabledHookTest extends TestCase
{
    /** @var \PDO $pdo */
    private $origin;
    /** @var \PDO $pdo */
    private $wrapped;

    protected function setUp()
    {
        parent::setUp();

        list($this->origin, $this->wrapped) = $this->createPDO('sqlite::memory:');
    }

    /**
     * @dataProvider queriesProvider
     *
     * @param string $query
     */
    public function testQuery($query)
    {
        $expected = $this->query($this->origin, $query);
        $actual = $this->query($this->wrapped, $query);

        $this->assertEquals($expected, $actual);
        $this->assertErrors($this->origin, $this->wrapped);
    }

    /**
     * @dataProvider queriesProvider
     *
     * @param string $query
     */
    public function testQueryAsObject($query)
    {
        $expected = $this->query($this->origin, $query, \PDO::FETCH_OBJ);
        $actual = $this->query($this->wrapped, $query, \PDO::FETCH_OBJ);

        $this->assertEquals($expected, $actual);
        $this->assertErrors($this->origin, $this->wrapped);
    }

    /**
     * @dataProvider statementsProvider
     *
     * @param $query
     */
    public function testExec($query)
    {
        $expected = $this->exec($this->origin, $query);
        $actual = $this->exec($this->wrapped, $query);

        $this->assertEquals($expected, $actual);
        $this->assertErrors($this->origin, $this->wrapped);
    }

    /**
     * @dataProvider preparedQueriesProvider
     *
     * @param string $query
     * @param array $bindings
     */
    public function testPrepared($query, $bindings)
    {
        $expected = $this->prepareAndExec($this->origin, $query, $bindings);
        $actual = $this->prepareAndExec($this->wrapped, $query, $bindings);

        $this->assertEquals($expected['result'], $actual['result']);
        $this->assertErrors($this->origin, $this->wrapped);


        /** @var \PDOStatement $expectedStatement */
        $expectedStatement = $expected['statement'];
        /** @var \PDOStatement $expectedStatement */
        $actualStatement = $actual['statement'];

        if ($expectedStatement === false) {
            $this->assertEquals($expectedStatement, $actualStatement);

            return;
        }

        $this->assertEquals($expectedStatement->fetchAll(), $actualStatement->fetchAll());
        $this->assertErrors($expectedStatement, $actualStatement);
    }

    public function testGetAttribute()
    {
        $this->assertEquals(
            $this->origin->getAttribute(\PDO::ATTR_SERVER_VERSION),
            $this->wrapped->getAttribute(\PDO::ATTR_SERVER_VERSION)
        );
    }

    public function queriesProvider()
    {
        return array(
            'success' => array('select 1 as test'),
            'error' => array('select * from test')
        );
    }

    public function statementsProvider()
    {
        return array(
           'success' => array('create table test (id int)'),
           'error' => array('create table test id int')
        );
    }

    public function preparedQueriesProvider()
    {
        return array(
            'success' => array('select ? as test', array(1)),
            'error' => array('select ? from test', array(1))
        );
    }

    protected function createPDO($dsn)
    {
        return array(new \PDO($dsn), new PDO($dsn));
    }

    /**
     * @param \PDO $pdo
     * @param string $query
     * @param null $mode
     *
     * @return array|bool
     */
    protected function query($pdo, $query, $mode = null)
    {
        $statement = isset($mode) ? $pdo->query($query, $mode) : $pdo->query($query);

        if ($statement === false) {
            return $statement;
        }

        $rows = array();

        foreach ($statement as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param \PDO $pdo
     * @param string $query
     *
     * @return int|bool
     */
    protected function exec($pdo, $query)
    {
        return $pdo->exec($query);
    }

    /**
     * @param \PDO $pdo
     * @param string $query
     * @param array $bindings
     *
     * @return array|bool
     */
    protected function prepareAndExec($pdo, $query, $bindings)
    {
        $statement = $pdo->prepare($query);

        if ($statement === false) {
            return array(
                'statement' => false,
                'result' => null
            );
        }

        $result = $statement->execute($bindings);

        return array(
            'statement' => $statement,
            'result' => $result
        );
    }
}
