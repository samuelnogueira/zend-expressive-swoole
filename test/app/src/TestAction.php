<?php

namespace Samuelnogueira\ExpressiveSwooleTest\app\src;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class TestAction
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwooleTest\app\src
 */
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
            'body'            => (string) $request->getBody(),
            'parsedBody'      => $request->getParsedBody(),
            'method'          => $request->getMethod(),
            'uri'             => (string) $request->getUri(),
            'queryParams'     => $request->getQueryParams(),
            'serverParams'    => $request->getServerParams(),
            'requestTarget'   => $request->getRequestTarget(),
        ];
        $status  = 200;
        $headers = [
            'X-My-Header' => 'test',
        ];

        $cookie1 = SetCookie::create('cookie1')
            ->withValue('cookieValue')
            ->withDomain('oreo.com')
            ->withPath('/');
        $cookie2 = SetCookie::create('cookie2')
            ->withValue('anotherCookieValue');

        $response = new JsonResponse($data, $status, $headers);
        $response = FigResponseCookies::set($response, $cookie1);
        $response = FigResponseCookies::set($response, $cookie2);

        return $response;
    }
}
