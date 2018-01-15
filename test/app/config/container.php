<?php

use Samuelnogueira\ExpressiveSwoole\ConfigProvider;
use Samuelnogueira\ExpressiveSwooleTest\TestAction;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

// Initialize config
$config = [
    'dependencies' => [
        'factories' => [
            Application::class => ApplicationFactory::class,
            TestAction::class  => InvokableFactory::class,
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
