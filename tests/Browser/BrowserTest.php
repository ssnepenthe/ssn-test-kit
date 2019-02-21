<?php

namespace SsnTestKit\Tests\Browser;

use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;

class BrowserTest extends TestCase
{
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
    public function it_provides_a_method_for_performing_an_action_on_all_active_clients()
    {
        $browser = new Browser();

        $goutteCount = 0;
        $pantherCount = 0;

        $clientCounter = function ($client) use (&$goutteCount, &$pantherCount) {
            if ($client instanceof \Goutte\Client) {
                $goutteCount++;
            }

            if ($client instanceof \Symfony\Component\Panther\Client) {
                $pantherCount++;
            }
        };

        $browser->forEachClient($clientCounter);

        $this->assertSame(0, $goutteCount);
        $this->assertSame(0, $pantherCount);

        $browser->goutte();

        $browser->forEachClient($clientCounter);

        $this->assertSame(1, $goutteCount);
        $this->assertSame(0, $pantherCount);

        $browser->panther();

        $browser->forEachClient($clientCounter);

        $this->assertSame(2, $goutteCount);
        $this->assertSame(1, $pantherCount);
    }
}
