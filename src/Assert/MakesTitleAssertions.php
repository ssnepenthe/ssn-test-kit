<?php

namespace SsnTestKit\Assert;

use PHPUnit\Framework\Assert as PHPUnit;

trait MakesTitleAssertions
{
    public function assertTitleIs(string $title)
    {
        PHPUnit::assertEquals($title, $this->title());

        return $this;
    }

    public function assertTitleIsNot(string $title)
    {
        PHPUnit::assertNotEquals($title, $this->title());

        return $this;
    }

    public function assertTitleContains(string $needle)
    {
        PHPUnit::assertStringContainsString($needle, $this->title());

        return $this;
    }

    public function assertTitleDoesNotContain(string $needle)
    {
        PHPUnit::assertStringNotContainsString($needle, $this->title());

        return $this;
    }

    public function assertTitleStartsWith(string $prefix)
    {
        PHPUnit::assertStringStartsWith($prefix, $this->title());

        return $this;
    }

    public function assertTitleDoesNotStartWith(string $prefix)
    {
        PHPUnit::assertStringStartsNotWith($prefix, $this->title());

        return $this;
    }

    public function assertTitleEndsWith(string $suffix)
    {
        PHPUnit::assertStringEndsWith($suffix, $this->title());

        return $this;
    }

    public function assertTitleDoesNotEndWith(string $suffix)
    {
        PHPUnit::assertStringEndsNotWith($suffix, $this->title());

        return $this;
    }

    public function assertTitleMatches(string $pattern)
    {
        PHPUnit::assertRegExp($pattern, $this->title());

        return $this;
    }

    public function assertTitleDoesNotMatch(string $pattern)
    {
        PHPUnit::assertNotRegExp($pattern, $this->title());

        return $this;
    }
}
