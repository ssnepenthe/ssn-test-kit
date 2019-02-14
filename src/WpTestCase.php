<?php

namespace SsnTestKit;

use PHPUnit\Framework\TestCase;

class WpTestCase extends TestCase
{
    use InteractsWithCli,
        MakesHttpRequests;

    public function setUp()
    {
        $uses = class_uses(static::class);

        if (array_key_exists(ResetsSite::class, $uses)) {
            $this->resetSite();
        }

        if (array_key_exists(ManagesUserSessions::class, $uses)) {
            $this->logout();
        }
    }

    public static function tearDownAfterClass()
    {
        if (static::$browser instanceof Browser) {
            static::$browser->quit();
        }
    }
}
