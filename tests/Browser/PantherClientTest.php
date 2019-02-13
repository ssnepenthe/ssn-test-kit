<?php

namespace SsnTestKit\Tests\Browser;

use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;

class PantherClientTest extends TestCase
{
    /** @test */
    public function it_provides_the_panther_client_when_javascript_is_enabled()
    {
        $browser = (new Browser())->enableJavascript();

        $this->assertInstanceOf(Client::class, $browser->client());
    }

    /** @test */
    public function it_caches_panther_instances()
    {
        $browser = (new Browser())->enableJavascript();

        $this->assertSame($browser->client(), $browser->client());
        $this->assertSame($browser->panther(), $browser->panther());
        $this->assertSame($browser->client(), $browser->panther());
    }
}
