<?php

namespace SsnTestKit;

trait ManagesRewrites
{
    protected function flushRewrites() : string
    {
        return $this->wp('rewrite flush');
    }

    protected function setPermalinkStructure(string $structure) : string
    {
        return $this->wp(sprintf('rewrite structure %s', escapeshellarg($structure)));
    }
}
