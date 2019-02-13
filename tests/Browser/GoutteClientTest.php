<?php

namespace SsnTestKit\Tests\Browser;

use Goutte\Client;
use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;

class GoutteClientTest extends TestCase
{
    /** @test */
    public function it_provides_the_goutte_client_when_javascript_is_disabled()
    {
        $browser = new Browser();

        $this->assertFalse($browser->isJavascriptEnabled());
        $this->assertInstanceOf(Client::class, $browser->client());
    }

    /** @test */
    public function it_disables_redirects_by_default_for_goutte()
    {
        $browser = new Browser();

        $this->assertFalse($browser->goutte()->isFollowingRedirects());
    }

    /** @test */
    public function it_caches_goutte_instances()
    {
        $browser = new Browser();

        $this->assertSame($browser->client(), $browser->client());
        $this->assertSame($browser->goutte(), $browser->goutte());
        $this->assertSame($browser->client(), $browser->goutte());
    }
}
