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

    /** @test */
    public function it_can_assert_that_a_header_is_present()
    {
        $response = new Response(
            new BrowserKitResponse('', 200, ['apple' => 'red']),
            new Crawler()
        );
        $e = null;

        $response->assertHeader('apple');

        try {
            $response->assertHeader('banana');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_header_is_present_and_set_to_a_specific_value()
    {
        $response = new Response(
            new BrowserKitResponse('', 200, ['apple' => 'red']),
            new Crawler()
        );
        $e = null;

        // Matching name => value pair.
        $response->assertHeader('apple', 'red');

        try {
            // Name is present, value is wrong.
            $response->assertHeader('apple', 'yellow');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);

        $e = null;

        try {
            // Name is absent.
            $response->assertHeader('banana', 'yellow');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_header_is_absent()
    {
        $response = new Response(
            new BrowserKitResponse('', 200, ['apple' => 'red']),
            new Crawler()
        );
        $e = null;

        $response->assertHeaderMissing('banana');

        try {
            $response->assertHeaderMissing('apple');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_string_is_present()
    {
        $response = new Response(
            new BrowserKitResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'),
            new Crawler()
        );

        $response->assertSee('Lions');
        $response->assertSee('tigers');

        $e = null;

        try {
            // Case sensitive.
            $response->assertSee('lions');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);

        $e = null;

        try {
            // Absent.
            $response->assertSee('wolves');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);

        $e = null;

        try {
            // Tags in the way.
            $response->assertSee('tigers and bears');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_string_is_present_after_removing_tags()
    {
        $response = new Response(
            new BrowserKitResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'),
            new Crawler()
        );

        $response->assertSeeText('Lions and tigers');
        $response->assertSeeText('tigers and bears');

        $e = null;

        try {
            // Case sensitive.
            $response->assertSeeText('lions and tigers');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);

        $e = null;

        try {
            // Absent.
            $response->assertSeeText('tigers and wolves');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_string_is_absent()
    {
        $response = new Response(
            new BrowserKitResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'),
            new Crawler()
        );

        $response->assertDontSee('wolves');

        // Case sensitive.
        $response->assertDontSee('lions');

        // Tags in the way.
        $response->assertDontSee('tigers and bears');

        $e = null;

        try {
            $response->assertDontSee('Lions');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }

    /** @test */
    public function it_can_assert_that_a_string_is_absent_after_removing_tags()
    {
        $response = new Response(
            new BrowserKitResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'),
            new Crawler()
        );

        $response->assertDontSeeText('tigers and wolves');

        // Case sensitive.
        $response->assertDontSeeText('lions and tigers');

        $e = null;

        try {
            // Case sensitive.
            $response->assertDontSeeText('Lions and tigers');
        } catch (\Exception $e) {
            // ...
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $e);
    }
}
