<?php

namespace SsnTestKit;

trait ManagesThemes
{
    protected function activateTheme(string $slug) : string
    {
        return $this->wp(sprintf('theme activate %s', escapeshellarg($slug)));
    }
}
