<?php

use Samuelnogueira\ExpressiveSwoole\ConfigProvider;
use Samuelnogueira\ExpressiveSwooleTest\app\src\TestAction;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

// Initialize config
$config = [
    'dependencies'       => [
        'factories' => [
            Application::class => ApplicationFactory::class,
            TestAction::class  => InvokableFactory::class,
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
$config = array_merge_recursive($config, (new ConfigProvider)());

// Build container
$container = new ServiceManager();
(new Config($config['dependencies']))->configureServiceManager($container);

// Inject config
$container->setService('config', $config);

return $container;
