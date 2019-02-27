<?php

namespace SsnTestKit;

trait ManagesPlugins
{
    protected function activatePlugin(string $slug) : string
    {
        return $this->wp(sprintf('plugin activate %s', escapeshellarg($slug)));
    }
}
