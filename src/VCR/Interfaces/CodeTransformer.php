<?php

namespace VCR\Interfaces;

interface CodeTransformer
{
    /**
     * Attaches the current filter to a stream.
     *
     * @return bool true on success or false on failure.
     */
    public function register();
}
