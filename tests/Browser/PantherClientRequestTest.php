<?php

namespace SsnTestKit\Tests\Browser;

use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\GuzzleException;

class PantherClientRequestTest extends TestCase
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
    public function it_correctly_sets_the_base_uri_for_panther()
    {
        // Default.
        $browser = (new Browser())->enableJavascript();
        $response = $browser->request('GET', 'http://localhost/status-200');

        $this->assertEquals('200', $response->crawler()->filter('p')->text());

        $browser->quit();

        // Custom.
        $browser = (new Browser('http://localhost'))->enableJavascript();
        $response = $browser->request('GET', '/status-200');

        $this->assertEquals('200', $response->crawler()->filter('p')->text());

        $browser->quit();
    }

    /** @test */
    public function it_makes_requests_using_panther_when_javascript_is_enabled()
    {
        $browser = (new Browser())->enableJavascript();
        $response = $browser->request('GET', 'http://localhost/js-dom-mod');

        $this->assertTrue($response->isPanther());
        $this->assertEquals('This is with JavaScript.', $response->crawler()->filter('p')->text());

        // Panther automatically quits on destruct... Is this necessary?
        $browser->quit();
    }

    /** @test */
    public function it_can_make_a_one_off_javascript_enabled_request()
    {
        $browser = new Browser();

        $this->assertFalse($browser->isJavascriptEnabled());

        $browser->withJavascript(function ($browser) {
            $response = $browser->request('GET', 'http://localhost/js-dom-mod');

            $this->assertEquals(
                'This is with JavaScript.',
                $response->crawler()->filter('p')->text()
            );
        });

        $this->assertFalse($browser->isJavascriptEnabled());
    }

    /** @test */
    public function it_can_wait_for_an_element_to_become_visible_before_acting()
    {
        $browser = new Browser();

        $browser->withJavascript(function ($browser) {
            $response = $browser->request('GET', 'http://localhost/js-delayed-visibility');

            $start = microtime(true);
            $response->waitFor('.test');
            $end = microtime(true);

            // Sanity.
            $this->assertStringContainsString(
                'test',
                $response->crawler()->filter('.test')->attr('class')
            );

            $this->assertStringNotContainsString(
                'hidden',
                $response->crawler()->filter('.test')->attr('class')
            );

            // Should need about 3000ms to become visible.
            $this->assertEqualsWithDelta(3, $end - $start, 0.1);
        });
    }
}
