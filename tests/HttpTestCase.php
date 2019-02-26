<?php

namespace SsnTestKit\Tests;

use PHPUnit\Framework\TestCase;

class HttpTestCase extends TestCase
{
    protected static $isServerAccessible;

    public static function setUpBeforeClass() : void
    {
        try {
            (new \GuzzleHttp\Client)->request('GET', 'http://localhost');
            // (new Browser())->get('http://localhost');

            static::$isServerAccessible = true;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            static::$isServerAccessible = false;
        }
    }

    public static function tearDownAfterClass() : void
    {
        static::$isServerAccessible = null;
    }

    public function setUp() : void
    {
        if (! static::$isServerAccessible) {
            $this->markTestSkipped('The test server does not appear to be accessible');
        }
    }
}
