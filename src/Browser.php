<?php

namespace SsnTestKit;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Panther\Client as PantherClient;

class Browser
{
    protected $baseUri;
    protected $goutte;
    protected $panther;
    protected $withJavascript = false;

    public function __construct(string $baseUri = null)
    {
        $this->baseUri = $baseUri;
    }

    // @todo Panther already quits on __destruct...
    public function quit()
    {
        if (null !== $this->panther) {
            $this->panther->quit();
        }

        $this->goutte = null;
        $this->panther = null;
    }

    public function client()
    {
        return $this->withJavascript ? $this->panther() : $this->goutte();
    }

    public function goutte()
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

    public function panther()
    {
        if (! $this->panther) {
            $this->panther = PantherClient::createChromeClient(null, null, [], $this->baseUri);
        }

        return $this->panther;
    }

    public function disableJavascript()
    {
        $this->withJavascript = false;

        return $this;
    }

    public function enableJavascript()
    {
        $this->withJavascript = true;

        return $this;
    }

    public function isJavascriptEnabled()
    {
        return $this->withJavascript;
    }

    // Provides a method of avoiding having to disable JavaScript after each test...
    public function withJavascript(\Closure $callback)
    {
        $this->enableJavascript();

        $callback($this);

        $this->disableJavascript();

        return $this;
    }

    // @todo Consider simplifying method signatures a bit?
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $change_history = true
    ) {
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
    ) {
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
        $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        string $content = null,
        bool $change_history = true
    ) {
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
