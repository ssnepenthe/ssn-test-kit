<?php

namespace SsnTestKit\Assert;

use PHPUnit\Framework\Assert as PHPUnit;

trait MakesUrlAssertions
{
    public function assertUrlIs(string $url)
    {
        PHPUnit::assertEquals($url, $this->url());

        return $this;
    }

    public function assertUrlIsNot(string $url)
    {
        PHPUnit::assertNotEquals($url, $this->url());

        return $this;
    }

    public function assertUrlContains(string $needle)
    {
        PHPUnit::assertStringContainsString($needle, $this->url());

        return $this;
    }

    public function assertUrlDoesNotContain(string $needle)
    {
        PHPUnit::assertStringNotContainsString($needle, $this->url());

        return $this;
    }

    public function assertUrlStartsWith(string $prefix)
    {
        PHPUnit::assertStringStartsWith($prefix, $this->url());

        return $this;
    }

    public function assertUrlDoesNotStartWith(string $prefix)
    {
        PHPUnit::assertStringStartsNotWith($prefix, $this->url());

        return $this;
    }

    public function assertUrlEndsWith(string $suffix)
    {
        PHPUnit::assertStringEndsWith($suffix, $this->url());

        return $this;
    }

    public function assertUrlDoesNotEndWith(string $suffix)
    {
        PHPUnit::assertStringEndsNotWith($suffix, $this->url());

        return $this;
    }

    public function assertUrlMatches(string $pattern)
    {
        PHPUnit::assertRegExp($pattern, $this->url());

        return $this;
    }

    public function assertUrlDoesNotMatch(string $pattern)
    {
        PHPUnit::assertNotRegExp($pattern, $this->url());

        return $this;
    }
}
