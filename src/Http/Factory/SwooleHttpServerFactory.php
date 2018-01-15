<?php

namespace Samuelnogueira\ExpressiveSwoole\Http\Factory;

use Psr\Container\ContainerInterface;
use Samuelnogueira\ExpressiveSwoole\Http\RequestHandlerInterface;
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
        $config   = $container->get('config')['swoole_http_server'] ?? [];
        $host     = $config['host'] ?? '0.0.0.0';
        $port     = $config['port'] ?? 8080;
        $settings = $config['settings'] ?? [];

        $server = new Server($host, $port);
        $server->set($settings);
        $server->on('request', $container->get(RequestHandlerInterface::class));

        return $server;
    }
}
