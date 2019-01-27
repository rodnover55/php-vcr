<?php

namespace VCR\Interfaces;

/**
 *
 */
interface Request
{
    /**
     * Returns true if specified request matches the current one
     * with specified request matcher callbacks.
     *
     * @param  Request $request Request to check if it matches the current one.
     * @param  \callable[] $requestMatchers Request matcher callbacks.
     *
     * @throws \BadFunctionCallException If one of the specified request matchers is not callable.
     * @return boolean True if specified request matches the current one.
     */
    public function matches(Request $request, array $requestMatchers);

    /**
     * Returns an array representation of this request.
     *
     * @return array Array representation of this request.
     */
    public function toArray();

    /**
     * Creates a new Request from a specified array.
     *
     * @param  array $request Request represented as an array.
     *
     * @return Request A new Request from specified array.
     */
    public static function fromArray(array $request);
}
