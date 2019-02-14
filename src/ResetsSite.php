<?php

namespace SsnTestKit;

// YIKES! Talk about destructive - be careful with this trait!
trait ResetsSite
{
    public function resetSite()
    {
        // @todo Better method name? Allow property as well?
        if (! method_exists($this, 'wpSqlDump')) {
            throw new \RuntimeException('Unable to locate sql file to reset site');
        }

        $this->cli()->wp('db reset --yes');
        $this->cli()->wp(sprintf('db import %s', escapeshellarg($this->wpSqlDump())));
    }
}
