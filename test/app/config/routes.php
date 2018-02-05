<?php

/** @var \Zend\Expressive\Application $app */

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

$testAction = function (ServerRequestInterface $request) {
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
};

$app->get('/my-get-route', $testAction);
$app->post('/my-post-route', $testAction);
