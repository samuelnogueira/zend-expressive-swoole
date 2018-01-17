<?php

namespace Samuelnogueira\ExpressiveSwoole\Http;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

/**
 * Class Psr7Bridge
 *
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class Psr15RequestHandler implements RequestHandlerInterface
{
    /**
     * @var \Interop\Http\ServerMiddleware\MiddlewareInterface
     */
    private $middleware;

    /**
     * @var \Interop\Http\ServerMiddleware\DelegateInterface
     */
    private $delegator;

    /**
     * Psr15Bridge constructor.
     *
     * @param \Interop\Http\ServerMiddleware\MiddlewareInterface $middleware
     * @param \Interop\Http\ServerMiddleware\DelegateInterface   $delegator
     */
    public function __construct(MiddlewareInterface $middleware, DelegateInterface $delegator)
    {
        $this->middleware = $middleware;
        $this->delegator  = $delegator;
    }

    /**
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     */
    public function __invoke(Request $request, Response $response): void
    {
        $serverRequest  = $this->createServerRequest($request);
        $serverResponse = $this->middleware->process($serverRequest, $this->delegator);

        $this->emitResponse($serverResponse, $response);
    }

    /**
     * @param \Swoole\Http\Request $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createServerRequest(Request $request): ServerRequestInterface
    {
        $body = new Stream('php://memory', 'r+');
        $string = $request->rawcontent();
        $body->write($string);

        $serverRequest = ServerRequestFactory::fromGlobals(
            $this->extractServer($request),
            $request->get ?? null,
            $request->post ?? null,
            $request->cookie ?? null
        );

        // attach body
        $serverRequest = $serverRequest->withBody($body);

        return $serverRequest;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $serverResponse
     * @param \Swoole\Http\Response               $response
     */
    private function emitResponse(ResponseInterface $serverResponse, Response $response): void
    {
        // set response status
        $response->status($serverResponse->getStatusCode());

        // set response headers
        foreach ($serverResponse->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                $response->header($key, $value);
            }
        }

        // set response body
        $body = $serverResponse->getBody()->__toString();
        if ($body) {
            $response->write($body);
        }

        $response->end();
    }

    /**
     * Normalize $server array originated by swoole so that if resembles the classic $_SERVER array
     *
     * @param \Swoole\Http\Request $request
     *
     * @return array
     */
    private function extractServer(Request $request): array
    {
        // swoole provides a mimic of $_SERVER value, but all keys are lower-case (go figure why)
        // convert all keys to upper case, since that's to be expected
        $server = [];
        foreach ($request->server as $key => $value) {
            $server[strtoupper($key)] = $value;
        }

        // put headers in $_SERVER with 'HTTP_' prefix
        foreach ($request->header as $key => $value) {
            $server['HTTP_' . strtoupper($key)] = $value;
        }

        return $server;
    }
}
