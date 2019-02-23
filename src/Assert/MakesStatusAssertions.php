<?php

namespace SsnTestKit\Assert;

use PHPUnit\Framework\Assert as PHPUnit;

trait MakesStatusAssertions
{
    /**
     * @return self
     */
    public function assertInformational()
    {
        PHPUnit::assertTrue(
            $this->isInformational(),
            "Response status {$this->status()} is not an informational status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertSuccessful()
    {
        PHPUnit::assertTrue(
            $this->isSuccessful(),
            "Response status {$this->status()} is not a successful status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertRedirection()
    {
        PHPUnit::assertTrue(
            $this->isRedirection(),
            "Response status {$this->status()} is not a redirection status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertClientError()
    {
        PHPUnit::assertTrue(
            $this->isClientError(),
            "Response status {$this->status()} is not a client error status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertServerError()
    {
        PHPUnit::assertTrue(
            $this->isServerError(),
            "Response status {$this->status()} is not a server error status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertStatus(int $status)
    {
        PHPUnit::assertTrue(
            $status === $this->status(),
            "Response code {$this->status()} does not match expected {$status} status code"
        );

        return $this;
    }

    /**
     * @return self
     */
    public function assertOk()
    {
        return $this->assertStatus(200);
    }

    /**
     * @return self
     */
    public function assertForbidden()
    {
        return $this->assertStatus(403);
    }

    /**
     * @return self
     */
    public function assertNotFound()
    {
        return $this->assertStatus(404);
    }
}
