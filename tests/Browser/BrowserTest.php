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
}
