<?php

namespace VCR\PDO\Mock;

use VCR\Drivers\PDO\Hook as ParentHook;
use VCR\Interfaces\Request;

class Hook extends ParentHook
{
    private $callback;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
    }

    public function enable(\Closure $requestCallback)
    {
        $this->callback = $requestCallback;
    }

    public function disable()
    {
    }

    public function isEnabled()
    {
        return true;
    }

    public function getResponse(Request $request)
    {
        return call_user_func($this->callback, $request);
    }
}
