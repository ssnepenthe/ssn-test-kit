<?php

namespace SsnTestKit;

use PHPUnit\Framework\TestCase;
use function SsnTestKit\class_uses_recursive;

class StkTestCase extends TestCase
{
    use InteractsWithCli,
        MakesHttpRequests;

    public function setUp() : void
    {
        $uses = class_uses_recursive(static::class);

        if (array_key_exists(ResetsSite::class, $uses)) {
            /**
             * @psalm-suppress UndefinedMethod
             */
            $this->resetSite();
        }

        if (array_key_exists(ManagesUserSessions::class, $uses)) {
            /**
             * @psalm-suppress UndefinedMethod
             */
            $this->logout();
        }

        $annotations = $this->getAnnotations();

        if (
            array_key_exists('javascript', $annotations['class'])
            || array_key_exists('js', $annotations['class'])
            || array_key_exists('javascript', $annotations['method'])
            || array_key_exists('js', $annotations['method'])
        ) {
            $this->browser()->enableJavascript();
        }
    }

    public function tearDown() : void
    {
        $this->browser()->disableJavascript();
    }

    public static function tearDownAfterClass() : void
    {
        // $uses = class_uses(static::class);

        // if (array_key_exists(ResetsSite::class, $uses)) {
        //     // @todo Can't access in static context...
        //     $this->resetSite();
        // }

        if (static::$browser instanceof Browser) {
            static::$browser->quit();

            static::$browser = null;
        }
    }
}
