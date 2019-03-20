<?php

namespace VCR\Support;

use Madewithlove\PhpunitSnapshots\SnapshotsManager as ParentManager;

class SnapshotsManager extends ParentManager
{
    public static function setSuite($suite)
    {
        if (empty(static::$suite) || (get_class(static::$suite) !== get_class($suite))) {
            static::$assertionsInTest = [];
        }

        static::$suite = $suite;
    }
}
