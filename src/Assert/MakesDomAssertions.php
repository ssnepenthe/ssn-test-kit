<?php

namespace SsnTestKit\Assert;

use SsnTestKit\ElementHasAttribute;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\Constraint\LogicalNot;

trait MakesDomAssertions
{
    /**
     * @return self
     */
    public function assertChecked(string $selector = null)
    {
        $element = null === $selector
            ? $this->crawler()->getNode(0)
            : $this->crawler()->filter($selector)->getNode(0);

        PHPUnit::assertThat($element, new ElementHasAttribute('checked'));

        return $this;
    }

    /**
     * @return self
     */
    public function assertNotChecked(string $selector = null)
    {
        $element = null === $selector
            ? $this->crawler()->getNode(0)
            : $this->crawler()->filter($selector)->getNode(0);

        // @todo What happens if the element is not present in the crawler? I think this assertion passes... That should not be the case...

        PHPUnit::assertThat($element, new LogicalNot(new ElementHasAttribute('checked')));

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCount(int $count, string $selector = null)
    {
        PHPUnit::assertCount(
            $count,
            null === $selector ? $this->crawler() : $this->crawler()->filter($selector)
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCountGreaterThan(int $count, string $selector = null)
    {
        $actual = null === $selector
            ? $this->crawler()->count()
            : $this->crawler()->filter($selector)->count();

        PHPUnit::assertGreaterThan($count, $actual);

        return $this;
    }

    /**
     * @return self
     */
    public function assertNodeCountLessThan(int $count, string $selector = null)
    {
        $actual = null === $selector
            ? $this->crawler()->count()
            : $this->crawler()->filter($selector)->count();

        PHPUnit::assertLessThan($count, $actual);

        return $this;
    }

    /**
     * @return self
     */
    public function assertPresent(string $selector)
    {
        PHPUnit::assertGreaterThan(0, $this->crawler()->filter($selector)->count());

        return $this;
    }

    /**
     * @return self
     */
    public function assertAbsent(string $selector)
    {
        PHPUnit::assertCount(0, $this->crawler()->filter($selector));

        return $this;
    }
}
