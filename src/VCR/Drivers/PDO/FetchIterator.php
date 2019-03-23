<?php

namespace VCR\Drivers\PDO;

use VCR\Drivers\PDO\Transformers\Both;

class FetchIterator extends \ArrayIterator
{
    private $mode = \PDO::FETCH_BOTH;

    /** @var array|callable[] */
    private $transformers = [
        \PDO::FETCH_BOTH => ['VCR\Drivers\PDO\Transformers\Both', 'transform'],
        \PDO::FETCH_OBJ => ['VCR\Drivers\PDO\Transformers\Obj', 'transform']
    ];

    public function current()
    {
        $row = parent::current();

        $transform = $this->transformers[$this->mode];

        if (is_null($transform)) {
            throw new \LogicException("Mode {$this->mode} unknown");
        }

        return $transform($row);
    }


    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     *
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }
}
