<?php

namespace SsnTestKit\Tests\Browser;

use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Exception\GuzzleException;

class GoutteClientRequestTest extends TestCase
{
    protected static $isServerAccessible;

    public static function setUpBeforeClass() : void
    {
        try {
            (new Browser())->get('http://localhost');

            static::$isServerAccessible = true;
        } catch (GuzzleException $e) {
            static::$isServerAccessible = false;
        }
    }

    public function setUp() : void
    {
        if (! static::$isServerAccessible) {
            $this->markTestSkipped('The test server does not appear to be accessible');
        }
    }

    /** @test */
    public function it_correctly_sets_the_base_uri_with_guzzle_for_goutte()
    {
        // Default.
        $browser = new Browser();
        $response = $browser->request('GET', 'http://localhost/status-200');

        $this->assertNull($browser->client()->getClient()->getConfig('base_uri'));
        $this->assertEquals('200', $response->crawler()->filter('p')->text());

        // Custom.
        $browser = new Browser('http://localhost');
        $response = $browser->request('GET', '/status-200');

        $guzzleBaseUri = $browser->client()->getClient()->getConfig('base_uri');

        $this->assertInstanceOf(UriInterface::class, $guzzleBaseUri);
        $this->assertEquals('http', $guzzleBaseUri->getScheme());
        $this->assertEquals('localhost', $guzzleBaseUri->getHost());
        $this->assertEquals('200', $response->crawler()->filter('p')->text());
    }

    /** @test */
    public function it_makes_requests_using_goutte_when_javascript_is_disabled()
    {
        $browser = new Browser();
        $response = $browser->request('GET', 'http://localhost/js-dom-mod');

        $this->assertFalse($response->isPanther());
        $this->assertEquals('This is without JavaScript.', $response->crawler()->filter('p')->text());
    }
}
