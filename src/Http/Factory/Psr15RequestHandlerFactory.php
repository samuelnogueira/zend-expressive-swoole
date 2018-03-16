<?php

namespace Samuelnogueira\ExpressiveSwoole\Http\Factory;

use Psr\Container\ContainerInterface;
use Samuelnogueira\ExpressiveSwoole\Http\Psr15SwooleRequestHandler;

/**
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class Psr15RequestHandlerFactory
{
    /**
     * @param \Psr\Container\ContainerInterface $container
     *
     * @return \Samuelnogueira\ExpressiveSwoole\Http\Psr15SwooleRequestHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Psr15SwooleRequestHandler
    {
        $app = $container->get('Zend\Expressive\Application');

        return new Psr15SwooleRequestHandler($app);
    }
}
