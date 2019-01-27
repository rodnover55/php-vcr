<?php

namespace VCR\Interfaces;

/**
 * Encapsulates a response.
 */
interface Response
{
    /**
     * Returns an array representation of this Response.
     *
     * @return array Array representation of this Request.
     */
    public function toArray();

    /**
     * Creates a new Response from a specified array.
     *
     * @param  array  $response Array representation of a Response.
     * @return Response A new Response from a specified array
     */
    public static function fromArray(array $response);
}
