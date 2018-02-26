<?php

/** @var \Zend\Expressive\Application $app */

use Samuelnogueira\ExpressiveSwooleTest\app\src\TestAction;

$app->get('/my-get-route', TestAction::class);
$app->post('/my-post-route', TestAction::class);
