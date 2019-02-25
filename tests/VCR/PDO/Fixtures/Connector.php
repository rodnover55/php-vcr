<?php

namespace VCR\PDO\Fixtures;

class Connector
{
    public function exec()
    {
        $pdo = new \PDO('sqlite::memory:');

        $statement = $pdo->query('select 1 as test');
        foreach ($statement as $row) {
            return $row;
        }
    }
}
