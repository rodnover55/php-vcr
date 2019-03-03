<?php

namespace VCR\PDO\Fixtures;

class Connector
{
    public function query()
    {
        $pdo = new \PDO('sqlite::memory:');

        $statement = $pdo->query('select 1 as test');
        foreach ($statement as $row) {
            return $row;
        }
    }

    public function exec()
    {
        $pdo = new \PDO('sqlite::memory:');

        $pdo->exec('create table test id int');

        return $pdo->errorInfo();
    }
}
