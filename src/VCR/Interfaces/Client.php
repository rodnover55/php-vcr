<?php

namespace VCR\Interfaces;

interface Client
{
    /**
     * Returns a response for specified request.
     *
     * @param Request $request Request to send.
     *
     * @return Response Response for specified request.
     */
    public function send(Request $request);

    public static function fromArray(array $data);
}
