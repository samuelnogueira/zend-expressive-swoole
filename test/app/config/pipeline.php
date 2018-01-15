<?php

/**
 * Setup middleware pipeline:
 *
 * @var \Zend\Expressive\Application $app
 */

$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();
