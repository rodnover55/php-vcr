<?php

namespace VCR\Drivers\PDO;

use VCR\CodeTransform\AbstractCodeTransform;

class CodeTransform extends AbstractCodeTransform
{
    const NAME = 'vcr_pdo';

    public static $replacements = array(
        '@new\s+\\\?PDO\W*\(@i' => 'new \VCR\Drivers\PDO\PDO(',
        '@(extends\s+\\\?PDO)(?=[\s{]|$)@i'   => 'extends \VCR\Drivers\PDO\PDO',
    );

    // TODO: Wrong change for importing. Could add https://github.com/nikic/PHP-Parser/blob/master/doc/component/Pretty_printing.markdown#formatting-preserving-pretty-printing
    protected function transformCode($code)
    {
        return preg_replace(array_keys(self::$replacements), array_values(self::$replacements), $code);
    }
}
