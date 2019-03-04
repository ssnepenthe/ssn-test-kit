<?php

namespace SsnTestKit;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\BrowserKit\Client as BrowserKitClient;

class Browser
{
    /**
     * @var string|null
     */
    protected $baseUri;

    /**
     * @var GoutteClient|null
     */
    protected $goutte;

    /**
     * @var PantherClient|null
     */
    protected $panther;

    /**
     * @var boolean
     */
    protected $withJavascript = false;

    public function __construct(string $baseUri = null)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @todo Panther already quits on __destruct...
     */
    public function quit() : void
    {
        if (null !== $this->panther) {
            $this->panther->quit();
        }

        $this->goutte = null;
        $this->panther = null;
    }

    public function client() : BrowserKitClient
    {
        return $this->withJavascript ? $this->panther() : $this->goutte();
    }

    public function goutte() : GoutteClient
    {
        if (null === $this->goutte) {
            $this->goutte = new GoutteClient();
            $this->goutte->followRedirects(false);

            if ($this->baseUri) {
                $this->goutte->setClient(new GuzzleClient([
                    'allow_redirects' => false, // Always disable so browser-kit can handle redirects.
                    'base_uri' => $this->baseUri,
                    'cookies' => true,
                ]));
            }
        }

        return $this->goutte;
    }

    public function panther() : PantherClient
    {
        if (! $this->panther) {
            $this->panther = PantherClient::createChromeClient(null, null, [], $this->baseUri);
        }

        return $this->panther;
    }

    /**
     * @return self
     */
    public function deleteAllCookies()
    {
        return $this->forEachClient(function (BrowserKitClient $client) : void {
            $client->getCookieJar()->clear();
        });
    }

    /**
     * @return self
     */
    public function forEachClient(\Closure $callback)
    {
        if (null !== $this->goutte) {
            $callback($this->goutte);
        }

        if (null !== $this->panther) {
            $callback($this->panther);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function disableJavascript()
    {
        $this->withJavascript = false;

        return $this;
    }

    /**
     * @return self
     */
    public function enableJavascript()
    {
        $this->withJavascript = true;

        return $this;
    }

    public function isJavascriptEnabled() : bool
    {
        return $this->withJavascript;
    }

    /**
     * Provides a method of avoiding having to disable JavaScript after each test...
     *
     * @return self
     */
    public function withJavascript(\Closure $callback)
    {
        $this->enableJavascript();

        $callback($this);

        $this->disableJavascript();

        return $this;
    }

    /**
     * @return self
     *
     * @todo Maybe trait?
     * @todo Look into what the default window size is...
     */
    public function resize(int $width, int $height) {
        if (! $this->isJavascriptEnabled()) {
            throw new \RuntimeException(
                'It is not possible to resize the browser window when using Goutte'
            );
        }

        $this->client()->manage()->window()->setSize(
            new \Facebook\WebDriver\WebDriverDimension($width, $height)
        );

        return $this;
    }

    /**
     * @todo Consider simplifying method signatures a bit?
     */
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $change_history = true
    ) : Response {
        $this->client()->request(
            $method,
            $uri,
            $parameters,
            $files,
            $server,
            $content,
            $change_history
        );

        // It is important to note that responses from panther don't include status and headers.
        //
        // It may be beneficial for the response to know which client it was generated from so that
        // we can trigger some sort of notice if attempting to check status or headers - Or maybe we
        // just infer this from type of crawler as there is a panther-specific crawler class.
        //
        // On the other hand - not totally certain it is beneficial to include both the crawler when
        // creating a test response instance...
        //
        // If we keep the crawler, may akso want to also include cookie jar?
        return new Response($this->client());
    }

    // @todo Json variants?

    public function get(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $change_history = true
    ) : Response {
        return $this->request(
            'GET',
            $uri,
            $parameters,
            $files,
            $server,
            $content,
            $change_history
        );
    }

    public function post(
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $change_history = true
    ) : Response {
        // Will throw on Panther...
        return $this->request(
            'POST',
            $uri,
            $parameters,
            $files,
            $server,
            $content,
            $change_history
        );
    }
}
