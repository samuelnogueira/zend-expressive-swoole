<?php

namespace Samuelnogueira\ExpressiveSwoole;

use Swoole\Http\Server;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Class Bootstrap
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwoole
 */
class Bootstrap
{
    /** @var Server */
    private $server;

    /**
     * Bootstrap constructor.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = require 'config/container.php';

        /** @var \Zend\Expressive\Application $app */
        $app     = $container->get(Application::class);
        $factory = $container->get(MiddlewareFactory::class);

        // Execute programmatic/declarative middleware pipeline and routing
        // configuration statements
        if (file_exists('config/pipeline.php')) {
            (require 'config/pipeline.php')($app, $factory, $container);
        }
        if (file_exists('config/routes.php')) {
            (require 'config/routes.php')($app, $factory, $container);
        }

        $this->server = $container->get('Swoole\Http\Server');
    }

    /**
     * @return \Swoole\Http\Server
     */
    public function getSwooleHttpServer(): Server
    {
        return $this->server;
    }

    public function __invoke(): void
    {
        $this->getSwooleHttpServer()->start();
    }
}
