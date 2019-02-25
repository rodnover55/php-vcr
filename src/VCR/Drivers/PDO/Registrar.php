<?php

namespace VCR\Drivers\PDO;

use VCR\Type;

class Registrar
{
    public static function config()
    {
        return array(
            'factory' => array(
                'request' => '\VCR\Drivers\PDO\Request',
                'response' => '\VCR\Drivers\PDO\Response',
                'client' => '\VCR\Drivers\PDO\Client'
            ),
            'hooks' => array(
                Type::PDO => 'VCR\Drivers\PDO\Hook'
            ),
            'matchers' => array(
                'connection' => array('VCR\Drivers\PDO\Matcher', 'matchConnection'),
                'method' => array('VCR\Drivers\PDO\Matcher', 'matchMethod'),
                'statement' => array('VCR\Drivers\PDO\Matcher', 'matchStatement'),
            )
        );
    }
}
