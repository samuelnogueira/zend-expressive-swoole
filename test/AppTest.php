<?php


namespace Samuelnogueira\ExpressiveSwooleTest;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
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
        static::$process = new Process('exec ./../../bin/swoole_serve ../../vendor/autoload.php', __DIR__ . '/app');
        static::$process->start();

        // wait for server to boot-up
        sleep(1);
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
        $testServerPort = getenv('TEST_SERVER_PORT') ?: 8080;
        $baseRequest    = new Request("http://localhost:$testServerPort?a=1&b", null, 'php://temp', [
            'X-Header1' => 'Header Value 1',
            'Cookie'    => 'MY_COOKIE=VALUE',
        ]);

        $getRequest = $baseRequest
            ->withUri($baseRequest->getUri()->withPath('/my-get-route'))
            ->withMethod('GET');

        $response = $this->client->send($getRequest);

        $this->assertEquals(
            [
                0 => 'cookie1=cookieValue; path=/; domain=oreo.com',
                1 => 'cookie2=anotherCookieValue',
            ],
            $response->getHeader('Set-Cookie')
        );
    }

    public function provideRequests(): array
    {
        $testServerPort = getenv('TEST_SERVER_PORT') ?: 8080;
        $baseRequest    = new Request("http://localhost:$testServerPort?a=1&b", null, 'php://temp', [
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
}
