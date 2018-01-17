<?php

/** @var \Zend\Expressive\Application $app */

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

    return new JsonResponse($data, $status, $headers);
};

$app->get('/my-get-route', $testAction);
$app->post('/my-post-route', $testAction);
