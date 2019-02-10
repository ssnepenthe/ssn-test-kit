<?php

namespace SsnTestKit;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

class Response
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client()
    {
        return $this->client;
    }

    public function content()
    {
        if ($this->isPanther()) {
            // We can't rely on $this->unwrap()->getContent() when using panther - The response may
            // have been built before JavaScript execution has completed.
            //
            // Here we access the web driver instance directly for the page source.
            //
            // Not sure if this is preferable or we should try something like
            // $this->crawler()->html().
            //
            // @todo Get some automated tests set up for this.
            return $this->client->getWebDriver()->getPageSource();
        }

        return $this->unwrap()->getContent();
    }

    public function cookie($name, $path = '/', $domain = null)
    {
        return $this->client->getCookieJar()->get($name, $path, $domain);
    }

    public function cookies()
    {
        return $this->client->getCookieJar()->all();
    }

    public function status()
    {
        $this->throwForPanther('status');

        return $this->unwrap()->getStatus();
    }

    public function header(string $header, bool $first = true)
    {
        $this->throwForPanther('headers');

        return $this->unwrap()->getHeader($header, $first);
    }

    public function headers()
    {
        $this->throwForPanther('headers');

        return $this->unwrap()->getHeaders();
    }

    public function crawler()
    {
        return $this->client->getCrawler();
    }

    public function unwrap()
    {
        return $this->client->getInternalResponse();
    }

    public function isPanther()
    {
        // Maybe not ideal - by testing $this->crawler() instead of $this->client I am able to avoid
        // situations where I would otherwise be trying to mock a (final) panther client object.
        return $this->crawler() instanceof Crawler;
    }

    protected function throwForPanther(string $property)
    {
        if ($this->isPanther()) {
            // @todo Better to throw SkippedTestError for PHPUnit's sake? Or something specific to
            //       this package to avoid accidental catches?
            throw new \RuntimeException(
                "Response {$property} not available for inspection when using Panther"
            );
        }
    }

    public function waitFor(
        string $cssSelector,
        int $timeoutInSeconds = 30,
        int $intervalInMilliseconds = 250
    ) {
        if (! $this->isPanther()) {
            throw new \RuntimeException(
                'It is not possible to wait for an element to become visible when using Goutte'
            );
        }

        $this->client->waitFor($cssSelector, $timeoutInSeconds, $intervalInMilliseconds);

        return $this;
    }

    public function isInformational()
    {
        return $this->status() >= 100 && $this->status() < 200;
    }

    public function isSuccessful()
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isRedirection()
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function isClientError()
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function isServerError()
    {
        return $this->status() >= 500 && $this->status() < 600;
    }

    public function isOk()
    {
        return 200 === $this->status();
    }

    public function isForbidden()
    {
        return 403 === $this->status();
    }

    public function isNotFound()
    {
        return 404 === $this->status();
    }

    public function isRedirect(string $location = null)
    {
        return \in_array($this->status(), [ 201, 301, 302, 303, 307, 308 ], true) && (
            null === $location ?: $location === $this->header('Location')
        );
    }

    public function assertInformational()
    {
        Assert::assertTrue(
            $this->isInformational(),
            "Response status {$this->status()} is not an informational status code"
        );

        return $this;
    }

    public function assertSuccessful()
    {
        Assert::assertTrue(
            $this->isSuccessful(),
            "Response status {$this->status()} is not a successful status code"
        );

        return $this;
    }

    public function assertRedirection()
    {
        Assert::assertTrue(
            $this->isRedirection(),
            "Response status {$this->status()} is not a redirection status code"
        );

        return $this;
    }

    public function assertClientError()
    {
        Assert::assertTrue(
            $this->isClientError(),
            "Response status {$this->status()} is not a client error status code"
        );

        return $this;
    }

    public function assertServerError()
    {
        Assert::assertTrue(
            $this->isServerError(),
            "Response status {$this->status()} is not a server error status code"
        );

        return $this;
    }

    public function assertStatus(int $status)
    {
        Assert::assertTrue(
            $status === $this->status(),
            "Response code {$this->status()} does not match expected {$status} status code"
        );

        return $this;
    }

    public function assertOk()
    {
        return $this->assertStatus(200);
    }

    public function assertForbidden()
    {
        return $this->assertStatus(403);
    }

    public function assertNotFound()
    {
        return $this->assertStatus(404);
    }

    public function assertRedirect(string $uri = null)
    {
        Assert::assertTrue(
            $this->isRedirect(),
            "Response status code {$this->status()} is not a redirect status code"
        );

        if (null !== $uri) {
            // Move to ->assertLocation()?
            Assert::assertEquals($uri, $this->header('Location'));
        }

        return $this;
    }

    public function assertHeader(string $name, $value = null)
    {
        $header = $this->header($name);

        Assert::assertTrue(
            null !== $header && [] !== $header,
            "Header {$name} is not present on response"
        );

        if (null !== $value) {
            Assert::assertEquals(
                $value,
                $header,
                "Header {$name} was found but value does not match expected {$value}"
            );
        }

        return $this;
    }

    public function assertHeaderMissing(string $name)
    {
        $header = $this->header($name);

        Assert::assertTrue(
            null === $header || [] === $header,
            "Unexpected header {$name} is present on response"
        );

        return $this;
    }

    public function assertCookie(string $name, $value = null)
    {
        $cookie = $this->cookie($name);

        Assert::assertNotNull($cookie, "Cookie {$name} not present on response");

        if (null !== $value) {
            $actual = $cookie->getValue();

            Assert::assertEquals(
                $value,
                $actual,
                "Cookie {$name} was found but value does not match expected {$value}"
            );
        }

        return $this;
    }

    public function assertCookieMissing(string $name)
    {
        Assert::assertNull($this->cookie($name), "Unexpected cookie {$name} present on response");

        return $this;
    }

    public function assertSee(string $value)
    {
        Assert::assertStringContainsString($value, $this->content());

        return $this;
    }

    public function assertSeeInOrder(array $values)
    {
        Assert::assertThat($values, new SeeInOrder($this->content()));

        return $this;
    }

    public function assertSeeText(string $value)
    {
        Assert::assertStringContainsString($value, strip_tags($this->content()));

        return $this;
    }

    public function assertSeeTextInOrder(array $values)
    {
        Assert::assertThat($values, new SeeInOrder(strip_tags($this->content())));

        return $this;
    }

    public function assertDontSee(string $value)
    {
        Assert::assertStringNotContainsString($value, $this->content());

        return $this;
    }

    public function assertDontSeeText(string $value)
    {
        Assert::assertStringNotContainsString($value, strip_tags($this->content()));

        return $this;
    }
}
