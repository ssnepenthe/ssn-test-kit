<?php

namespace SsnTestKit\Assert;

use PHPUnit\Framework\Assert as PHPUnit;

trait MakesTitleAssertions
{
    protected function getTitleText()
    {
        $crawler = $this->crawler()->filter('title');

        return $this->isPanther() ? $crawler->attr('textContent') : $crawler->text();
    }

    public function assertTitleIs(string $title)
    {
        PHPUnit::assertEquals($title, $this->getTitleText());

        return $this;
    }

    public function assertTitleIsNot(string $title)
    {
        PHPUnit::assertNotEquals($title, $this->getTitleText());

        return $this;
    }

    public function assertTitleContains(string $needle)
    {
        PHPUnit::assertStringContainsString($needle, $this->getTitleText());

        return $this;
    }

    public function assertTitleDoesNotContain(string $needle)
    {
        PHPUnit::assertStringNotContainsString($needle, $this->getTitleText());

        return $this;
    }

    public function assertTitleStartsWith(string $prefix)
    {
        PHPUnit::assertStringStartsWith($prefix, $this->getTitleText());

        return $this;
    }

    public function assertTitleDoesNotStartWith(string $prefix)
    {
        PHPUnit::assertStringStartsNotWith($prefix, $this->getTitleText());

        return $this;
    }

    public function assertTitleEndsWith(string $suffix)
    {
        PHPUnit::assertStringEndsWith($suffix, $this->getTitleText());

        return $this;
    }

    public function assertTitleDoesNotEndWith(string $suffix)
    {
        PHPUnit::assertStringEndsNotWith($suffix, $this->getTitleText());

        return $this;
    }

    public function assertTitleMatches(string $pattern)
    {
        PHPUnit::assertRegExp($pattern, $this->getTitleText());

        return $this;
    }

    public function assertTitleDoesNotMatch(string $pattern)
    {
        PHPUnit::assertNotRegExp($pattern, $this->getTitleText());

        return $this;
    }
}
