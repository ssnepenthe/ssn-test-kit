<?php

namespace SsnTestKit;

trait ManagesThemes
{
    protected function activateTheme(string $slug)
    {
        return $this->cli()->wpForOutput(sprintf('theme activate %s', escapeshellarg($slug)));
    }
}
