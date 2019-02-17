<?php

namespace SsnTestKit;

trait ManagesRewrites
{
    protected function flushRewrites()
    {
        return $this->cli()->wpForOutput('rewrite flush');
    }

    protected function setPermalinkStructure(string $structure)
    {
        return $this->cli()->wpForOutput(sprintf(
            'rewrite structure %s',
            escapeshellarg($structure)
        ));
    }
}
