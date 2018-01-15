<?php

namespace Samuelnogueira\ExpressiveSwoole;

use Samuelnogueira\ExpressiveSwoole\Http\Factory\Psr15RequestHandlerFactory;
use Samuelnogueira\ExpressiveSwoole\Http\Psr15RequestHandler;
use Samuelnogueira\ExpressiveSwoole\Http\RequestHandlerInterface;
use Swoole\Http\Server;

/**
 * Class ConfigProvider
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                RequestHandlerInterface::class => Psr15RequestHandler::class,
            ],
            'factories' => [
                Server::class              => Http\Factory\SwooleHttpServerFactory::class,
                Psr15RequestHandler::class => Psr15RequestHandlerFactory::class,
            ],
        ];
    }
}
