<?php

namespace VCR\Drivers\PDO\Transformers;

class Obj
{
    public static function transform($row)
    {
        $fields = [];

        foreach ($row as $key => $value) {
            if (!is_int($key)) {
                $fields[$key] = $value;
            }
        }
        return (object)$fields;
    }
}
