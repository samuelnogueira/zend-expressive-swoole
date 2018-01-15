<?php

namespace Samuelnogueira\ExpressiveSwooleTest;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class TestAction implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data    = [
            'protocolVersion' => $request->getProtocolVersion(),
            'headers'         => $request->getHeaders(),
            'cookies'         => $request->getCookieParams(),
            'body'            => $request->getBody(),
            'method'          => $request->getMethod(),
            'uri'             => $request->getUri(),
            'queryParams'     => $request->getQueryParams(),
            'serverParams'    => $request->getServerParams(),
            'requestTarget'   => $request->getRequestTarget(),
        ];
        $status  = 200;
        $headers = [
            'X-My-Header' => 'test',
        ];

        return new JsonResponse($data, $status, $headers);
    }
}
