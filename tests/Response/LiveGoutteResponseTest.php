<?php

namespace SsnTestKit\Tests\Response;

use Goutte\Client;
use SsnTestKit\Browser;
use SsnTestKit\MakesHttpRequests;
use SsnTestKit\Tests\HttpTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response;

class LiveGoutteResponseTest extends HttpTestCase
{
    use MakesHttpRequests;

    public function tearDown() : void
    {
        $this->browser()->deleteAllCookies();
    }

    public static function tearDownAfterClass() : void
    {
        if (static::$browser instanceof Browser) {
            static::$browser->quit();

            static::$browser = null;
        }
    }

    protected function browserBaseUri() : string
    {
        return 'http://localhost';
    }

    /** @test */
    public function test_client()
    {
        $response = $this->browser()->get('/');

        $this->assertInstanceOf(Client::class, $response->client());
    }

    /** @test */
    public function test_content()
    {
        $response = $this->browser()->get('/');

        // Content should be the equivalent of document.documentElement.innerHTML.
        // Goutte inserts extra newline characters - may need to address this eventually.
        $this->assertStringStartsWith("\n<head>", $response->content());
        $this->assertStringEndsWith("</body>\n", $response->content());
    }

    /** @test */
    public function test_cookie()
    {
        // @todo Test with path and domain?
        $response = $this->browser()->get('/');

        $this->assertEquals('testcookievalue', $response->cookie('testcookie')->getValue());
    }

    /** @test */
    public function test_cookies()
    {
        $response = $this->browser()->get('/');

        $cookies = array_map(function ($cookie) {
            return $cookie->getName() . ': ' . $cookie->getValue();
        }, $response->cookies());

        $this->assertEquals(['testcookie: testcookievalue'], $cookies);
    }

    /** @test */
    public function test_status()
    {
        $response = $this->browser()->get('/status-client-error');

        $this->assertSame(401, $response->status());
    }

    /** @test */
    public function test_header()
    {
        $response = $this->browser()->get('/');

        $this->assertEquals('Bananas', $response->header('X-Apples'));
    }

    /** @test */
    public function test_headers()
    {
        $response = $this->browser()->get('/');
        $headers = $response->headers();

        $this->assertArrayHasKey('Content-type', $headers);
        $this->assertArrayHasKey('X-Apples', $headers);
    }

    /** @test */
    public function test_crawler()
    {
        $response = $this->browser()->get('/');

        $this->assertInstanceOf(Crawler::class, $response->crawler());
        $this->assertEquals('Home', $response->crawler()->filter('h1')->text());
    }

    /** @test */
    public function test_unwrap()
    {
        $response = $this->browser()->get('/');

        $this->assertInstanceOf(Response::class, $response->unwrap());
    }

    /** @test */
    public function test_within()
    {
        $response = $this->browser()->get('/');

        $this->assertNotEquals('Home', $response->crawler()->text());

        $response->within('h1', function ($response) {
            $this->assertEquals('Home', $response->crawler()->text());
        });
    }

    /** @test */
    public function test_is_panther()
    {
        $response = $this->browser()->get('/');

        $this->assertFalse($response->isPanther());
    }

    /** @test */
    public function test_wait_for()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/')->waitFor('.not-important');
    }

    /** @test */
    public function test_is_informational()
    {
        $response = $this->browser()->get('/status-informational');

        $this->assertTrue($response->isInformational());

        $response = $this->browser()->get('/status-successful');

        $this->assertFalse($response->isInformational());
    }

    /** @test */
    public function test_is_successful()
    {
        $response = $this->browser()->get('/status-successful');

        $this->assertTrue($response->isSuccessful());

        $response = $this->browser()->get('/status-informational');

        $this->assertFalse($response->isSuccessful());
    }

    /** @test */
    public function test_is_redirection()
    {
        $response = $this->browser()->get('/status-redirection');

        $this->assertTrue($response->isRedirection());
    }

    /** @test */
    public function test_is_client_error()
    {
        $response = $this->browser()->get('/status-client-error');

        $this->assertTrue($response->isClientError());

        $response = $this->browser()->get('/status-informational');

        $this->assertFalse($response->isClientError());
    }

    /** @test */
    public function test_is_server_error()
    {
        $response = $this->browser()->get('/status-server-error');

        $this->assertTrue($response->isServerError());

        $response = $this->browser()->get('/status-informational');

        $this->assertFalse($response->isServerError());
    }

    /** @test */
    public function test_is_ok()
    {
        $response = $this->browser()->get('/status-ok');

        $this->assertTrue($response->isOk());

        $response = $this->browser()->get('/status-forbidden');

        $this->assertFalse($response->isOk());
    }

    /** @test */
    public function test_is_forbidden()
    {
        $response = $this->browser()->get('/status-forbidden');

        $this->assertTrue($response->isForbidden());

        $response = $this->browser()->get('/status-ok');

        $this->assertFalse($response->isForbidden());
    }

    /** @test */
    public function test_is_not_found()
    {
        $response = $this->browser()->get('/status-not-found');

        $this->assertTrue($response->isNotFound());

        $response = $this->browser()->get('/status-ok');

        $this->assertFalse($response->isNotFound());
    }

    /** @test */
    public function test_is_redirect()
    {
        // @todo
    }

    /** @test */
    public function test_get_title_text()
    {
        $response = $this->browser()->get('/');

        $this->assertEquals('Test Server Home', $response->title());
    }
}
