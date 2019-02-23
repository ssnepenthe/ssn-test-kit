<?php

namespace SsnTestKit\Assert;

use SsnTestKit\SeeInOrder;
use PHPUnit\Framework\Assert as PHPUnit;

trait MakesContentAssertions
{
    /**
     * @return self
     */
    public function assertSee(string $value)
    {
        PHPUnit::assertStringContainsString($value, $this->content());

        return $this;
    }

    /**
     * @param string[] $values
     * @return self
     */
    public function assertSeeInOrder(array $values)
    {
        PHPUnit::assertThat($values, new SeeInOrder($this->content()));

        return $this;
    }

    /**
     * @return self
     */
    public function assertSeeText(string $value)
    {
        PHPUnit::assertStringContainsString($value, strip_tags($this->content()));

        return $this;
    }

    /**
     * @param string[] $values
     * @return self
     */
    public function assertSeeTextInOrder(array $values)
    {
        PHPUnit::assertThat($values, new SeeInOrder(strip_tags($this->content())));

        return $this;
    }

    /**
     * @return self
     */
    public function assertDontSee(string $value)
    {
        PHPUnit::assertStringNotContainsString($value, $this->content());

        return $this;
    }

    /**
     * @return self
     */
    public function assertDontSeeText(string $value)
    {
        PHPUnit::assertStringNotContainsString($value, strip_tags($this->content()));

        return $this;
    }
}
