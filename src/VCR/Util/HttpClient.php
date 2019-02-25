<?php

namespace VCR\Util;

use VCR\Request as HttpRequest;
use VCR\Response as HttpResponse;
use VCR\Interfaces\Client;
use VCR\Interfaces\Request;
use VCR\Interfaces\Response;
use VCR\VCRFactory;

/**
 * @deprecated use \VCR\Drivers\Http\Client
 */
class HttpClient implements Client
{
    public static function fromArray(array $data)
    {
        return VCRFactory::get(__CLASS__);
    }
    /**
     * Returns a response for specified HTTP request.
     *
     * @param HttpRequest|Request $request HTTP Request to send.
     *
     * @return HttpResponse|Response Response for specified request.
     */
    public function send(Request $request)
    {
        $ch = curl_init($request->getUrl());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($ch, CURLOPT_HTTPHEADER, HttpUtil::formatHeadersForCurl($request->getHeaders()));
        if (!is_null($request->getBody())) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getBody());
        }

        curl_setopt_array($ch, $request->getCurlOptions());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        list($status, $headers, $body) = HttpUtil::parseResponse(curl_exec($ch));

        return new HttpResponse(
            HttpUtil::parseStatus($status),
            HttpUtil::parseHeaders($headers),
            $body,
            curl_getinfo($ch)
        );
    }
}
