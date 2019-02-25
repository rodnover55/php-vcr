<?php

namespace VCR\Drivers\PDO;

class Matcher
{
    public static function matchMethod(Request $first, Request $second)
    {
        return $first->getMethod() == $second->getMethod();
    }

    public static function matchStatement(Request $first, Request $second)
    {
        return $first->getStatement() == $second->getStatement();
    }

    public static function matchConnection(Request $first, Request $second)
    {
        return
            ($first->getConnection() == $second->getConnection()) &&
            $first->getOptions() == $second->getOptions();
    }
}
