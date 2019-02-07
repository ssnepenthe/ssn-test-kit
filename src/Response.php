<?php

namespace SsnTestKit;

use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherDomCrawler;

class Response
{
    protected $crawler;
    protected $response;

    public function __construct(BrowserKitResponse $response, DomCrawler $crawler)
    {
        $this->response = $response;
        $this->crawler = $crawler;
    }

    public function content()
    {
        return $this->response->getContent();
    }

    public function status()
    {
        $this->throwForPanther('status');

        return $this->response->getStatus();
    }

    public function header(string $header, bool $first = true)
    {
        $this->throwForPanther('headers');

        return $this->response->getHeader($header, $first);
    }

    public function headers()
    {
        $this->throwForPanther('headers');

        return $this->response->getHeaders();
    }

    public function crawler()
    {
        return $this->crawler;
    }

    public function unwrap()
    {
        return $this->response;
    }

    public function isPanther()
    {
        return $this->crawler instanceof PantherDomCrawler;
    }

    protected function throwForPanther(string $property)
    {
        if ($this->isPanther()) {
            // @todo Better to throw SkippedTestError for PHPUnit's sake?
            throw new \RuntimeException(
                "Response {$property} is not available for inspection when using Panther"
            );
        }
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
}
