<?php

declare(strict_types=1);

use Zend\Expressive\Application;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;

/**
 * Setup middleware pipeline:
 *
 * @param \Zend\Expressive\Application $app
 */
return function (Application $app): void {
    $app->pipe(RouteMiddleware::class);
    $app->pipe(DispatchMiddleware::class);
};
