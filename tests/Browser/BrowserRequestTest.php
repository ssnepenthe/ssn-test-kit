<?php

namespace SsnTestKit\Tests\Browser;

use SsnTestKit\Browser;
use SsnTestKit\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\GuzzleException;

class BrowserRequestTest extends TestCase
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
    public function it_wraps_responses()
    {
        $browser = new Browser();
        $response = $browser->request('GET', 'http://localhost');

        $this->assertInstanceOf(Response::class, $response);
    }

    /** @test */
    public function it_can_make_requests()
    {
        $browser = new Browser();
        $response = $browser->get('http://localhost');

        $this->assertTrue($response->isSuccessful());
    }

    /** @test */
    public function it_provides_shorthand_for_get_requests()
    {
        $browser = new Browser();
        $response = $browser->get('http://localhost');

        $this->assertEquals('GET', $browser->client()->getInternalRequest()->getMethod());
    }

    /** @test */
    public function it_provides_shorthand_for_post_requests()
    {
        $browser = new Browser();
        $response = $browser->post('http://localhost');

        $this->assertEquals('POST', $browser->client()->getInternalRequest()->getMethod());
    }
}
