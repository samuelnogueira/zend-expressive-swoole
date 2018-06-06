<?php

namespace Samuelnogueira\ExpressiveSwoole\Http;

use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;

/**
 * Class Psr7Bridge
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class Psr15SwooleRequestHandler implements SwooleRequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * Psr15Bridge constructor.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface $requestHandler
     */
    public function __construct(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param \Swoole\Http\Request  $request
     * @param \Swoole\Http\Response $response
     */
    public function __invoke(Request $request, Response $response): void
    {
        $serverRequest  = $this->createServerRequest($request);
        $serverResponse = $this->requestHandler->handle($serverRequest);

        $this->emitResponse($serverResponse, $response);
    }

    /**
     * @param \Swoole\Http\Request $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createServerRequest(Request $request): ServerRequestInterface
    {
        $body   = new Stream('php://memory', 'r+');
        $string = $request->rawContent();
        $body->write($string);

        $serverRequest = ServerRequestFactory::fromGlobals(
            $this->extractServer($request),
            $request->get ?? null,
            $request->post ?? null,
            $request->cookie ?? null
        );

        // attach body
        return $serverRequest->withBody($body);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $serverResponse
     * @param \Swoole\Http\Response               $response
     */
    private function emitResponse(ResponseInterface $serverResponse, Response $response): void
    {
        // set response status
        $response->status($serverResponse->getStatusCode());
        $headers = $serverResponse->getHeaders();

        //set cookies
        $this->setResponseCookies($response, $headers['Set-Cookie'] ?? []);
        unset($headers['Set-Cookie']);

        // set response headers
        foreach ($headers as $key => $values) {
            foreach ($values as $value) {
                $response->header($key, $value);
            }
        }

        // set response body
        $bodyStream = $serverResponse->getBody();
        if ($bodyStream->isSeekable()) {
            $bodyStream->rewind();
        }
        $contentLength = 0;
        while (!$bodyStream->eof()) {
            // buffer at most 1 Mb
            $buffer = $bodyStream->read(1024 * 1024);
            if ($buffer) {
                $contentLength += strlen($buffer);
                $response->write($buffer);
            }
        }
        $response->header('Content-Length', $contentLength);
        $response->end();
    }

    private function setResponseCookies(Response $response, array $cookies)
    {
        foreach ($cookies as $cookieString) {
            $cookie = SetCookie::fromSetCookieString($cookieString);
            $response->cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpires(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->getSecure(),
                $cookie->getHttpOnly()
            );
        }
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
