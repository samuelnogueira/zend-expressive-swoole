<?php

namespace Samuelnogueira\ExpressiveSwoole\Http;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Interface RequestHandlerInterface
 */
interface RequestHandlerInterface
{
    /**
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     */
    public function __invoke(Request $request, Response $response): void;
}
