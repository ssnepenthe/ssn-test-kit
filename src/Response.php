<?php

namespace SsnTestKit;

use PHPUnit\Framework\Assert as PHPUnit;
use Symfony\Component\BrowserKit\Client;
use PHPUnit\Framework\Constraint\LogicalNot;
use Symfony\Component\Panther\DomCrawler\Crawler;

class Response
{
    use Assert\MakesContentAssertions,
        Assert\MakesDomAssertions,
        Assert\MakesStatusAssertions,
        Assert\MakesTitleAssertions,
        Assert\MakesUrlAssertions;

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
        if ($this->crawler()->count() < 1) {
            return '';
        }

        // @todo When using Goutte there will be an extra newline character before and after - trim?
        // Also looks like Panther may be inserting an extra newline before </body>?
        return $this->isPanther() ? $this->crawler()->attr('innerHTML') : $this->crawler()->html();
    }

    /**
     * @see https://github.com/symfony/panther/issues/6
     */
    public function title() : string
    {
        $crawler = $this->crawler()->filter('title');

        if ($crawler->count() < 1) {
            return '';
        }

        return $this->isPanther() ? $crawler->attr('textContent') : $crawler->text();
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
    public function assertRedirect(string $uri = null)
    {
        PHPUnit::assertTrue(
            $this->isRedirect(),
            "Response status code {$this->status()} is not a redirect status code"
        );

        if (null !== $uri) {
            // Move to ->assertLocation()? Can we get access to browser base_uri setting to support relative URLs?
            PHPUnit::assertEquals($uri, $this->header('Location'));
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

        PHPUnit::assertTrue(
            null !== $header && [] !== $header,
            "Header {$name} is not present on response"
        );

        if (null !== $value) {
            PHPUnit::assertEquals(
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

        PHPUnit::assertTrue(
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

        PHPUnit::assertNotNull($cookie, "Cookie {$name} not present on response");

        if (null !== $value) {
            /**
             * @psalm-suppress PossiblyNullReference
             */
            $actual = $cookie->getValue();

            PHPUnit::assertEquals(
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
        PHPUnit::assertNull($this->cookie($name), "Unexpected cookie {$name} present on response");

        return $this;
    }
}
