<?php


namespace Samuelnogueira\ExpressiveSwooleTest;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Zend\Diactoros\Request;
use function GuzzleHttp\Psr7\stream_for;

/**
 * @author  Samuel Nogueira <samuel.nogueira@jumia.com>
 */
class AppTest extends TestCase
{
    /** @var Process */
    private static $process;

    /** @var \GuzzleHttp\ClientInterface */
    private $client;

    public static function setUpBeforeClass()
    {
        // start swoole server loaded up with test app (expressive)
        $commandLine = getenv('WITH_COVERAGE')
            ? ['../../bin/swoole_serve_test', '../reports/coverage.xml']
            : ['../../bin/swoole_serve_test'];
        $process     = new Process($commandLine, __DIR__ . '/app');
        $process->start(function ($type, $buffer) {
            echo $buffer;
        });

        // wait for server to boot-up
        sleep(1);

        if (!$process->isRunning()) {
            throw new ProcessFailedException($process);
        }

        static::$process = $process;
    }

    public static function tearDownAfterClass()
    {
        static::$process->stop();
    }

    protected function setUp()
    {
        $this->client = new Client();
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $expectedParsedBody
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @dataProvider provideRequests
     */
    public function testRequest(RequestInterface $request, array $expectedParsedBody = [])
    {
        $requestBody        = (string) $request->getBody();
        $requestCookies     = ['MY_COOKIE' => 'VALUE'];
        $requestQueryParams = ['a' => 1, 'b' => null];

        $response     = $this->client->send($request);
        $responseBody = (string) $response->getBody();

        // response headers assertions
        static::assertEquals(200, $response->getStatusCode());
        static::assertEquals('test', $response->getHeaderLine('X-My-Header'));

        // body assertions
        static::assertJson($responseBody);

        // parsed request assertions
        $data = json_decode($responseBody);
        static::assertAttributeEquals($request->getProtocolVersion(), 'protocolVersion', $data);
        static::assertAttributeEquals($request->getMethod(), 'method', $data);
        static::assertAttributeEquals((string) $request->getUri(), 'uri', $data);
        static::assertAttributeEquals((object) $requestCookies, 'cookies', $data);
        static::assertAttributeEquals((object) $requestQueryParams, 'queryParams', $data);
        foreach ($request->withoutHeader('Cookie')->getHeaders() as $key => $values) {
            self::assertAttributeEquals($values, strtolower($key), $data->headers);
        }
        static::assertAttributeEquals($requestBody, 'body', $data);

        if ($expectedParsedBody) {
            static::assertAttributeEquals((object) $expectedParsedBody, 'parsedBody', $data);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCookies()
    {
        $baseRequest = new Request("http://localhost:{$this->getPort()}?a=1&b", null, 'php://temp', [
            'X-Header1' => 'Header Value 1',
            'Cookie'    => 'MY_COOKIE=VALUE',
        ]);

        $getRequest = $baseRequest
            ->withUri($baseRequest->getUri()->withPath('/my-get-route'))
            ->withMethod('GET');

        $response = $this->client->send($getRequest);

        static::assertEquals(
            [
                0 => 'cookie1=cookieValue; path=/; domain=oreo.com',
                1 => 'cookie2=anotherCookieValue',
            ],
            $response->getHeader('Set-Cookie')
        );
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testLargeResponse()
    {
        $size     = 2 * 1024 * 1024;
        $request  = new Request("http://localhost:{$this->getPort()}/large?size=$size", RequestMethodInterface::METHOD_GET);
        $response = $this->client->send($request);

        static::assertSame($size, $response->getBody()->getSize());
        static::assertSame($size, strlen($response->getBody()->__toString()));
    }

    public function provideRequests(): array
    {
        $baseRequest = new Request("http://localhost:{$this->getPort()}?a=1&b", null, 'php://temp', [
            'X-Header1' => 'Header Value 1',
            'Cookie'    => 'MY_COOKIE=VALUE',
        ]);

        $getRequest = $baseRequest
            ->withUri($baseRequest->getUri()->withPath('/my-get-route'))
            ->withMethod('GET');

        $jsonPayload     = json_encode(['key1' => 'value1']);
        $jsonPostRequest = $baseRequest
            ->withUri($baseRequest->getUri()->withPath('/my-post-route'))
            ->withMethod('POST')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Content-Length', strlen($jsonPayload))
            ->withBody(stream_for($jsonPayload));

        $formData        = ['username' => 'john.doe', 'password' => 'password1'];
        $formPayload     = http_build_query($formData);
        $formPostRequest = $baseRequest
            ->withUri($baseRequest->getUri()->withPath('/my-post-route'))
            ->withMethod('POST')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Content-Length', strlen($formPayload))
            ->withBody(stream_for($formPayload));

        return [
            [$getRequest],
            [$jsonPostRequest],
            [$formPostRequest, $formData],
        ];
    }

    private function getPort(): int
    {
        return getenv('TEST_SERVER_PORT') ?: 8080;
    }
}
