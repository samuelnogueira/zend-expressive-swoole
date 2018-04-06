<?php

namespace Samuelnogueira\ExpressiveSwoole;

use Psr\Container\ContainerInterface;

/**
 * This factory could be replaced with Zend Service Manager's Invokable factory.
 * This factory only exists to avoid introducing that Zend Service Manager as a new dependency.
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwoole
 */
class ApplicationBootstrapperFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return ApplicationBootstrapper
     */
    public function __invoke(ContainerInterface $container): ApplicationBootstrapper
    {
        return new ApplicationBootstrapper($container);
    }
}
