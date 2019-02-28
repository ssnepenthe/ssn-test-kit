<?php

namespace SsnTestKit\Tests\Response;

use SsnTestKit\Browser;
use PHPUnit\Framework\TestCase;
use SsnTestKit\MakesHttpRequests;
use SsnTestKit\Tests\HttpTestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;

class LivePantherResponseTest extends HttpTestCase
{
    use MakesHttpRequests;

    public function setUp() : void
    {
        parent::setUp();

        $this->browser()->enableJavascript();
    }

    public function tearDown() : void
    {
        $this->browser()->disableJavascript();
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
        $this->assertStringStartsWith('<head>', $response->content());
        $this->assertStringEndsWith('</body>', $response->content());
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
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-client-error');

        $response->status();
    }

    /** @test */
    public function test_header()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/');

        $response->header('X-Apples');
    }

    /** @test */
    public function test_headers()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/');

        $response->headers();
    }

    /** @test */
    public function test_crawler()
    {
        $response = $this->browser()->get('/');

        $this->assertInstanceOf(Crawler::class, $response->crawler());
        $this->assertInstanceOf(PantherCrawler::class, $response->crawler());
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

        $this->assertTrue($response->isPanther());
    }

    /** @test */
    public function test_wait_for()
    {
        $response = $this->browser()->get('/');
        $response->waitFor('.js-delayed-visibility');

        $this->assertEquals(
            'This is visible now',
            $response->crawler()->filter('.js-delayed-visibility')->text()
        );
    }

    /** @test */
    public function test_is_informational()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-informational');

        $response->isInformational();
    }

    /** @test */
    public function test_is_successful()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-successful');

        $response->isSuccessful();
    }

    /** @test */
    public function test_is_redirection()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-redirection');

        $response->isRedirection();
    }

    /** @test */
    public function test_is_client_error()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-client-error');

        $response->isClientError();
    }

    /** @test */
    public function test_is_server_error()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-server-error');

        $response->isServerError();
    }

    /** @test */
    public function test_is_ok()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-ok');

        $response->isOk();
    }

    /** @test */
    public function test_is_forbidden()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-forbidden');

        $response->isForbidden();
    }

    /** @test */
    public function test_is_not_found()
    {
        $this->expectException(\RuntimeException::class);

        $response = $this->browser()->get('/status-not-found');

        $response->isNotFound();
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
