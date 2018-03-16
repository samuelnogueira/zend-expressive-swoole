<?php

use Samuelnogueira\ExpressiveSwoole\ConfigProvider;
use Samuelnogueira\ExpressiveSwooleTest\app\src\TestHandler;
use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

// Initialize config
$config = [
    'dependencies'       => [
        'factories' => [
            TestHandler::class => InvokableFactory::class,
        ],
    ],
    'swoole_http_server' => [
        'settings'        => [
            // we are collecting code coverage, we don't want processes to overwrite each-other's coverage.xml file
            'worker_num' => 1,
        ],

        // run hot code reload ticks to improve code coverage a bit
        // we don't have tests for this feature though
        'hot_code_reload' => [
            'enabled'  => true,
            'interval' => 100,
        ],
    ],
];

$aggregator = new ConfigAggregator([
    \Zend\HttpHandlerRunner\ConfigProvider::class,
    \Zend\Expressive\ConfigProvider::class,
    \Zend\Expressive\Router\ConfigProvider::class,
    \Zend\Expressive\Router\FastRouteRouter\ConfigProvider::class,
    ConfigProvider::class,
    new ArrayProvider($config),
]);
$config     = $aggregator->getMergedConfig();

$dependencies                       = $config['dependencies'];
$dependencies['services']['config'] = $config;

// Build container
return new ServiceManager($dependencies);
