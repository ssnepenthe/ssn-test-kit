<?php

namespace SsnTestKit;

trait MakesHttpRequests
{
    protected static $browser;

    public function browser()
    {
        if (null === static::$browser) {
            $baseUri = null;

            if (method_exists($this, 'browserBaseUri')) {
                $baseUri = $this->browserBaseUri();
            } elseif (property_exists($this, 'browserBaseUri')) {
                $baseUri = $this->browserBaseUri;
            }

            static::$browser = new Browser($baseUri);
        }

        return static::$browser;
    }
}
