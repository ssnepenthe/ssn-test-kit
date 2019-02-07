<?php

namespace SsnTestKit\Tests;

use PHPUnit\Framework\TestCase;
use Goutte\Client as GoutteClient;
use Psr\Http\Message\UriInterface;
use SsnTestKit\Browser;
use Symfony\Component\Panther\Client as PantherClient;

class BrowserClientTest extends TestCase
{
    /** @test */
    public function it_provides_the_panther_client_when_javascript_is_enabled()
    {
        $browser = (new Browser())->enableJavascript();

        $this->assertInstanceOf(PantherClient::class, $browser->client());
    }

    /** @test */
    public function it_provides_the_goutte_client_when_javascript_is_disabled()
    {
        $browser = new Browser();

        $this->assertInstanceOf(GoutteClient::class, $browser->client());
    }

    /** @test */
    public function it_disables_redirects_by_default_for_goutte()
    {
        $browser = new Browser();

        $this->assertFalse($browser->goutte()->isFollowingRedirects());
    }

    /** @test */
    public function it_caches_client_instances()
    {
        $browser = new Browser();

        $this->assertSame($browser->client(), $browser->client());
        $this->assertSame($browser->goutte(), $browser->goutte());
        $this->assertSame($browser->panther(), $browser->panther());
    }

    /** @test */
    public function it_allows_a_user_to_enable_javascript()
    {
        $browser = new Browser();

        $this->assertFalse($browser->isJavascriptEnabled());

        $browser->enableJavascript();

        $this->assertTrue($browser->isJavascriptEnabled());
    }

    /** @test */
    public function it_allows_a_user_to_disable_javascript()
    {
        $browser = (new Browser())->enableJavascript();

        $this->assertTrue($browser->isJavascriptEnabled());

        $browser->disableJavascript();

        $this->assertFalse($browser->isJavascriptEnabled());
    }

    /** @test */
    public function it_provides_a_shorthand_for_enabling_javascript_for_a_single_task()
    {
        $browser = new Browser();

        // JavaScript is disabled outside.
        $this->assertFalse($browser->isJavascriptEnabled());

        $browser->withJavascript(function ($browser) {
            // JavaScript is enabled in here.
            $this->assertTrue($browser->isJavascriptEnabled());
        });

        // JavaScript is still disabled out here :).
        $this->assertFalse($browser->isJavascriptEnabled());
    }

    /** @test */
    public function it_provides_a_chainable_interface_where_appropriate()
    {
        $browser = new Browser();

        $this->assertSame($browser, $browser->enableJavascript());
        $this->assertSame($browser, $browser->disableJavascript());
        $this->assertSame($browser, $browser->withJavascript(function () {
        }));
    }
}
