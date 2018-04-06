<?php

namespace Samuelnogueira\ExpressiveSwoole;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;

/**
 * Class ApplicationBootstrapper
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwoole
 */
class ApplicationBootstrapper
{
    /** @var ContainerInterface */
    private $container;

    /**
     * ApplicationBootstrapper constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Bootstraps a Zend Expressive 3 application
     */
    public function __invoke(): void
    {
        /** @var \Zend\Expressive\Application $app */
        $app     = $this->container->get(Application::class);
        $factory = $this->container->get(MiddlewareFactory::class);

        // Execute programmatic/declarative middleware pipeline and routing
        // configuration statements
        if (file_exists('config/pipeline.php')) {
            (require 'config/pipeline.php')($app, $factory, $this->container);
        }
        if (file_exists('config/routes.php')) {
            (require 'config/routes.php')($app, $factory, $this->container);
        }
    }
}
