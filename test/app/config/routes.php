<?php

declare(strict_types=1);

use Samuelnogueira\ExpressiveSwooleTest\app\src\TestHandler;
use Zend\Expressive\Application;

/**
 * Setup routes
 *
 * @param \Zend\Expressive\Application $app
 */
return function (Application $app): void {
    $app->get('/my-get-route', TestHandler::class);
    $app->post('/my-post-route', TestHandler::class);
};
