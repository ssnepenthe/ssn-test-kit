<?php

namespace SsnTestKit\Tests;

use PHPUnit\Framework\TestCase;
use SsnTestKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class ResponseAssertionTest extends TestCase
{
    protected function makeResponse($status = 200)
    {
        return new Response(new BrowserKitResponse('', $status), new Crawler());
    }

    protected function assertExpectationFailedExceptionIsThrownFor(callable $callable)
    {
        $e = null;

        try {
            call_user_func($callable);
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_informational()
    {
        // @todo Weird pattern... This is technically making an extra assertion which fails but
        // results in the PHPUnit assertion count being incremented while the failure count is not.

        $informational = $this->makeResponse(101);
        $nonInformational = $this->makeResponse();

        $informational->assertInformational();
        $this->assertExpectationFailedExceptionIsThrownFor(
            [$nonInformational, 'assertInformational']
        );
    }

    /** @test */
    public function it_can_assert_that_a_response_is_successful()
    {
        $successful = $this->makeResponse();
        $nonSuccessful = $this->makeResponse(101);

        $successful->assertSuccessful();
        $this->assertExpectationFailedExceptionIsThrownFor([$nonSuccessful, 'assertSuccessful']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_a_redirection()
    {
        $redirection = $this->makeResponse(301);
        $nonRedirection = $this->makeResponse();

        $redirection->assertRedirection();
        $this->assertExpectationFailedExceptionIsThrownFor([$nonRedirection, 'assertRedirection']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_a_client_error()
    {
        $clientError = $this->makeResponse(401);
        $nonClientError = $this->makeResponse();

        $clientError->assertClientError();
        $this->assertExpectationFailedExceptionIsThrownFor([$nonClientError, 'assertClientError']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_a_server_error()
    {
        $serverError = $this->makeResponse(501);
        $nonServerError = $this->makeResponse();

        $serverError->assertServerError();
        $this->assertExpectationFailedExceptionIsThrownFor([$nonServerError, 'assertServerError']);
    }

    /** @test */
    public function it_can_assert_that_a_response_has_a_specific_status_code()
    {
        $twoHundred = $this->makeResponse();
        $twoOhOne = $this->makeResponse(201);
        $e = null;

        $twoHundred->assertStatus(200);

        try {
            $twoOhOne->assertStatus(200);
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_ok()
    {
        $twoHundred = $this->makeResponse();
        $twoOhOne = $this->makeResponse(201);

        $twoHundred->assertOk();
        $this->assertExpectationFailedExceptionIsThrownFor([$twoOhOne, 'assertOk']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_forbidden()
    {
        $fourOhThree = $this->makeResponse(403);
        $twoHundred = $this->makeResponse();

        $fourOhThree->assertForbidden();
        $this->assertExpectationFailedExceptionIsThrownFor([$twoHundred, 'assertForbidden']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_not_found()
    {
        $fourOhFour = $this->makeResponse(404);
        $twoHundred = $this->makeResponse();

        $fourOhFour->assertNotFound();
        $this->assertExpectationFailedExceptionIsThrownFor([$twoHundred, 'assertNotFound']);
    }

    /** @test */
    public function it_can_assert_that_a_response_is_a_redirect()
    {
        foreach ([201, 301, 302, 303, 307, 308] as $status) {
            $response = new Response(
                new BrowserKitResponse('', $status, ['Location' => 'http://localhost/redirect']),
                new Crawler()
            );

            $response->assertRedirect();
            $response->assertRedirect('http://localhost/redirect');
        }

        $response = new Response(
            new BrowserKitResponse('', 200, ['Location' => 'http://localhost/redirect']),
            new Crawler()
        );
        $e = null;

        try {
            $response->assertRedirect();
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);

        $e = null;

        try {
            // Shouldn't matter because status code doesn't match.
            $response->assertRedirect('http://localhost/redirect');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }
}
