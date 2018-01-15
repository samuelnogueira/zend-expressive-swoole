<?php

/** @var \Zend\Expressive\Application $app */

use Samuelnogueira\ExpressiveSwooleTest\TestAction;

$app->get('/my-get-route', TestAction::class);
$app->post('/my-post-route', TestAction::class);
