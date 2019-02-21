<?php

namespace SsnTestKit;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\Client;
use PHPUnit\Framework\Constraint\LogicalNot;
use Symfony\Component\Panther\DomCrawler\Crawler;

class Response
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client() : Client
    {
        return $this->client;
    }

    public function content() : string
    {
        try {
            return $this->crawler()->html();
        } catch (\InvalidArgumentException $e) {
            return '';
        }
    }

    /**
     * @return \Symfony\Component\BrowserKit\Cookie|null
     */
    public function cookie(string $name, string $path = '/', string $domain = null)
    {
        return $this->client->getCookieJar()->get($name, $path, $domain);
    }

    /**
     * @return \Symfony\Component\BrowserKit\Cookie[]
     */
    public function cookies() : array
    {
        return $this->client->getCookieJar()->all();
    }

    public function status() : int
    {
        $this->throwForPanther('status');

        return $this->unwrap()->getStatus();
    }

    /**
     * @return string|string[]|null
     */
    public function header(string $header, bool $first = true)
    {
        $this->throwForPanther('headers');

        return $this->unwrap()->getHeader($header, $first);
    }

    /**
     * @return array<string, string>
     */
    public function headers() : array
    {
        $this->throwForPanther('headers');

        return $this->unwrap()->getHeaders();
    }

    public function crawler() : \Symfony\Component\DomCrawler\Crawler
    {
        return $this->client->getCrawler();
    }

    public function unwrap() : \Symfony\Component\BrowserKit\Response
    {
        return $this->client->getInternalResponse();
    }

    /**
     * @return self
     */
    public function within(string $selector, \Closure $callback)
    {
        $callback(new class($this->client(), $selector) extends Response {
            /**
             * @var string
             */
            protected $selector;

            public function __construct(Client $client, string $selector)
            {
                parent::__construct($client);

                $this->selector = $selector;
            }

            public function crawler() : \Symfony\Component\DomCrawler\Crawler
            {
                // @todo Cache filtered crawler?
                return parent::crawler()->filter($this->selector);
            }
        });

        return $this;
    }

    public function isPanther() : bool
    {
        // Maybe not ideal - by testing $this->crawler() instead of $this->client I am able to avoid
        // situations where I would otherwise be trying to mock a (final) panther client object.
        return $this->crawler() instanceof Crawler;
    }

    protected function throwForPanther(string $property) : void
    {
        if ($this->isPanther()) {
            // @todo Better to throw SkippedTestError for PHPUnit's sake? Or something specific to
            //       this package to avoid accidental catches?
            throw new \RuntimeException(
                "Response {$property} not available for inspection when using Panther"
            );
        }
    }

    /**
     * @return self
     */
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

        /**
         * @psalm-suppress UndefinedMethod
         */
        $this->client->waitFor($cssSelector, $timeoutInSeconds, $intervalInMilliseconds);

        return $this;
    }

    public function isInformational() : bool
    {
        return $this->status() >= 100 && $this->status() < 200;
    }

    public function isSuccessful() : bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isRedirection() : bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function isClientError() : bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function isServerError() : bool
    {
        return $this->status() >= 500 && $this->status() < 600;
    }

    public function isOk() : bool
    {
        return 200 === $this->status();
    }

    public function isForbidden() : bool
    {
        return 403 === $this->status();
    }

    public function isNotFound() : bool
    {
        return 404 === $this->status();
    }

    public function isRedirect(string $location = null) : bool
    {
        return \in_array($this->status(), [ 201, 301, 302, 303, 307, 308 ], true) && (
            null === $location ?: $location === $this->header('Location')
        );
    }

    /**
     * @return self
     */
    public function assertInformational()
    {
        Assert::assertTrue(
            $this->isInformational(),
            "Response status {$this->status()} is not an informational status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertSuccessful()
    {
        Assert::assertTrue(
            $this->isSuccessful(),
            "Response status {$this->status()} is not a successful status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertRedirection()
    {
        Assert::assertTrue(
            $this->isRedirection(),
            "Response status {$this->status()} is not a redirection status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertClientError()
    {
        Assert::assertTrue(
            $this->isClientError(),
            "Response status {$this->status()} is not a client error status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertServerError()
    {
        Assert::assertTrue(
            $this->isServerError(),
            "Response status {$this->status()} is not a server error status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertStatus(int $status)
    {
        Assert::assertTrue(
            $status === $this->status(),
            "Response code {$this->status()} does not match expected {$status} status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertOk()
    {
        return $this->assertStatus(200);
    }

    /**
     * @return self
     */
    public function assertForbidden()
    {
        return $this->assertStatus(403);
    }

    /**
     * @return self
     */
    public function assertNotFound()
    {
        return $this->assertStatus(404);
    }

    /**
     * @return self
     */
    public function assertRedirect(string $uri = null)
    {
        Assert::assertTrue(
            $this->isRedirect(),
            "Response status code {$this->status()} is not a redirect status code"
        );

        if (null !== $uri) {
            // Move to ->assertLocation()? Can we get access to browser base_uri setting to support relative URLs?
            Assert::assertEquals($uri, $this->header('Location'));
        }

        return $this;
    }

    /**
     * @return self
     */
    public function assertHeader(string $name, string $value = null)
    {
        // @todo Handle headers with multiple values. ($this->header($name, false))
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

    /**
     * @return self
     */
    public function assertHeaderMissing(string $name)
    {
        $header = $this->header($name);

        Assert::assertTrue(
            null === $header || [] === $header,
            "Unexpected header {$name} is present on response"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertCookie(string $name, string $value = null)
    {
        $cookie = $this->cookie($name);

        Assert::assertNotNull($cookie, "Cookie {$name} not present on response");

        if (null !== $value) {
            /**
             * @psalm-suppress PossiblyNullReference
             */
            $actual = $cookie->getValue();

            Assert::assertEquals(
                $value,
                $actual,
                "Cookie {$name} was found but value does not match expected {$value}"
            );
        }

        return $this;
    }

    /**
     * @return self
     */
    public function assertCookieMissing(string $name)
    {
        Assert::assertNull($this->cookie($name), "Unexpected cookie {$name} present on response");

        return $this;
    }

    /**
     * @return self
     */
    public function assertSee(string $value)
    {
        Assert::assertStringContainsString($value, $this->content());

        return $this;
    }

    /**
     * @param string[] $values
     * @return self
     */
    public function assertSeeInOrder(array $values)
    {
        Assert::assertThat($values, new SeeInOrder($this->content()));

        return $this;
    }

    /**
     * @return self
     */
    public function assertSeeText(string $value)
    {
        Assert::assertStringContainsString($value, strip_tags($this->content()));

        return $this;
    }

    /**
     * @param string[] $values
     * @return self
     */
    public function assertSeeTextInOrder(array $values)
    {
        Assert::assertThat($values, new SeeInOrder(strip_tags($this->content())));

        return $this;
    }

    /**
     * @return self
     */
    public function assertDontSee(string $value)
    {
        Assert::assertStringNotContainsString($value, $this->content());

        return $this;
    }

    /**
     * @return self
     */
    public function assertDontSeeText(string $value)
    {
        Assert::assertStringNotContainsString($value, strip_tags($this->content()));

        return $this;
    }

    /**
     * @return self
     */
    public function assertChecked(string $selector = null)
    {
        $element = null === $selector
            ? $this->crawler()->getNode(0)
            : $this->crawler()->filter($selector)->getNode(0);

        Assert::assertThat($element, new ElementHasAttribute('checked'));

        return $this;
    }

    /**
     * @return self
     */
    public function assertNotChecked(string $selector = null)
    {
        $element = null === $selector
            ? $this->crawler()->getNode(0)
            : $this->crawler()->filter($selector)->getNode(0);

        Assert::assertThat($element, new LogicalNot(new ElementHasAttribute('checked')));

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCount(int $count, string $selector = null)
    {
        Assert::assertCount(
            $count,
            null === $selector ? $this->crawler() : $this->crawler()->filter($selector)
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCountGreaterThan(int $count, string $selector = null)
    {
        $actual = null === $selector
            ? $this->crawler()->count()
            : $this->crawler()->filter($selector)->count();

        Assert::assertGreaterThan($count, $actual);

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCountLessThan(int $count, string $selector = null)
    {
        $actual = null === $selector
            ? $this->crawler()->count()
            : $this->crawler()->filter($selector)->count();

        Assert::assertLessThan($count, $actual);

        return $this;
    }

    /**
     * @return self
     */
    public function assertPresent(string $selector)
    {
        Assert::assertGreaterThan(0, $this->crawler()->filter($selector)->count());

        return $this;
    }

    /**
     * @return self
     */
    public function assertAbsent(string $selector)
    {
        Assert::assertCount(0, $this->crawler()->filter($selector));

        return $this;
    }
}
