<?php

namespace SsnTestKit;

trait MakesHttpRequests
{
    /**
     * @var Browser|null
     */
    protected static $browser;

    /**
     * @return Browser
     *
     * @psalm-suppress InvalidNullableReturnType
     */
    public function browser() : Browser
    {
        if (null === static::$browser) {
            $baseUri = null;

            if (method_exists($this, 'browserBaseUri')) {
                $baseUri = $this->browserBaseUri();
            } elseif (property_exists($this, 'browserBaseUri')) {
                /**
                 * @psalm-suppress UndefinedThisPropertyFetch
                 */
                $baseUri = $this->browserBaseUri;
            }

            static::$browser = new Browser($baseUri);
        }

        return static::$browser;
    }
}
