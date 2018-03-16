<?php

namespace Samuelnogueira\ExpressiveSwooleTest\app\src;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class TestHandler
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwooleTest\app\src
 */
class TestHandler implements RequestHandlerInterface
{
    /**
     * Handle the request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
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
