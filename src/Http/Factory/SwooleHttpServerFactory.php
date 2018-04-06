<?php

namespace Samuelnogueira\ExpressiveSwoole\Http\Factory;

use Psr\Container\ContainerInterface;
use Samuelnogueira\ExpressiveSwoole\ApplicationBootstrapper;
use Samuelnogueira\ExpressiveSwoole\Http\SwooleRequestHandlerInterface;
use Samuelnogueira\ExpressiveSwoole\Tick\HotCodeReloader;
use Swoole\Http\Server;

/**
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class SwooleHttpServerFactory
{
    /**
     * @param \Psr\Container\ContainerInterface $container
     *
     * @return \Swoole\Http\Server
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Server
    {
        $bootstrap = $container->get(ApplicationBootstrapper::class);
        $config    = $container->get('config')['swoole_http_server'] ?? [];
        $host      = $config['host'] ?? '0.0.0.0';
        $port      = $config['port'] ?? 8080;
        $settings  = $config['settings'] ?? [];

        $hotCodeReloadConfig   = $config['hot_code_reload'] ?? [];
        $hotCodeReloadEnabled  = $hotCodeReloadConfig['enabled'] ?? false;
        $hotCodeReloadInterval = $hotCodeReloadConfig['interval'] ?? 1000;

        $server = new Server($host, $port);
        $server->set($settings);
        $server->on('request', $container->get(SwooleRequestHandlerInterface::class));

        if ($hotCodeReloadEnabled) {
            $server->on(
                'WorkerStart',
                function (Server $server) use ($bootstrap, $hotCodeReloadInterval) {
                    // bootstrap application on server start so included files can be reloaded
                    $bootstrap();
                    $server->tick($hotCodeReloadInterval, new HotCodeReloader($server));
                }
            );
        } else {
            // bootstrap application now (application state after bootstrap will be shared across all workers)
            $bootstrap();
        }

        return $server;
    }
}
