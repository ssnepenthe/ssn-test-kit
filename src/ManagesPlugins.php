<?php

namespace SsnTestKit;

trait ManagesPlugins
{
    protected function activatePlugin(string $slug)
    {
        return $this->cli()->wpForOutput(sprintf('plugin activate %s', escapeshellarg($slug)));
    }
}
