<?php

namespace Samuelnogueira\ExpressiveSwoole\Tick;

use Interop\Container\ContainerInterface;
use Swoole\Server;

/**
 * Class HotCodeReloadDelegatorFactory
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 * @package Samuelnogueira\ExpressiveSwoole\Tick
 */
class HotCodeReloadDelegatorFactory
{
    /**
     * @param  ContainerInterface $container
     * @param  string             $name
     * @param  callable           $callback
     *
     * @return \Swoole\Server
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback): Server
    {
        $server = $callback();
        assert($server instanceof Server);

        $config              = $container->get('config')['swoole_http_server'] ?? [];
        $hotCodeReloadConfig = $config['hot_code_reload'] ?? [];
        $enabled             = $hotCodeReloadConfig['enabled'] ?? false;
        $interval            = $hotCodeReloadConfig['interval'] ?? 1000;

        if ($enabled) {
            $server->on('WorkerStart', function (Server $server) use ($interval) {
                $server->tick($interval, new HotCodeReloader($server));
            });
        }

        return $server;
    }
}
