<?php

namespace SsnTestKit;

trait ManagesRewrites
{
    protected function flushRewrites() : string
    {
        return $this->cli()->wpForOutput('rewrite flush');
    }

    protected function setPermalinkStructure(string $structure) : string
    {
        return $this->cli()->wpForOutput(sprintf(
            'rewrite structure %s',
            escapeshellarg($structure)
        ));
    }
}
