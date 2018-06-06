<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Samuelnogueira\ExpressiveSwooleTest\app\src\TestHandler;
use Zend\Diactoros\Response\TextResponse;
use Zend\Expressive\Application;

/**
 * Setup routes
 *
 * @param \Zend\Expressive\Application $app
 */
return function (Application $app): void {
    $app->get('/my-get-route', TestHandler::class);
    $app->post('/my-post-route', TestHandler::class);
    $app->get('/large', function (ServerRequestInterface $request) {
        return new TextResponse(str_repeat('a', (int) $request->getQueryParams()['size']));
    });
};
