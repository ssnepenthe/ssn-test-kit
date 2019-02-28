<?php

namespace SsnTestKit\Tests\Response;

use PHPUnit\Framework\TestCase;
use SsnTestKit\Tests\MakesResponses;
use PHPUnit\Framework\AssertionFailedError;

class StatusAssertionsTest extends TestCase
{
    use MakesResponses;

    /** @test */
    public function it_can_assert_response_is_informational()
    {
        $this->makeResponse(101)->assertInformational();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_informational()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertInformational();
    }

    /** @test */
    public function it_can_assert_response_is_successful()
    {
        $this->makeResponse()->assertSuccessful();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_successful()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(101)->assertSuccessful();
    }

    /** @test */
    public function it_can_assert_response_is_a_redirection()
    {
        $this->makeResponse(301)->assertRedirection();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_a_redirection()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertRedirection();
    }

    /** @test */
    public function it_can_assert_response_is_a_client_error()
    {
        $this->makeResponse(401)->assertClientError();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_a_client_error()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertClientError();
    }

    /** @test */
    public function it_can_assert_response_is_a_server_error()
    {
        $this->makeResponse(501)->assertServerError();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_a_server_error()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertServerError();
    }

    /** @test */
    public function it_can_assert_response_has_a_specific_status_code()
    {
        $this->makeResponse()->assertStatus(200);
    }

    /** @test */
    public function is_can_fail_to_assert_response_has_a_specific_status_code()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(201)->assertStatus(200);
    }

    /** @test */
    public function it_can_assert_response_is_ok()
    {
        $this->makeResponse()->assertOk();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_ok()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(201)->assertOk();
    }

    /** @test */
    public function it_can_assert_response_is_forbidden()
    {
        $this->makeResponse(403)->assertForbidden();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_forbidden()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertForbidden();
    }

    /** @test */
    public function it_can_assert_response_is_not_found()
    {
        $this->makeResponse(404)->assertNotFound();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_not_found()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertNotFound();
    }
}
