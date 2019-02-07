<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class ResponseAssertionTest extends TestCase
{
    protected function makeResponse($args = [])
    {
        if (is_string($args)) {
            $args = ['content' => $args];
        } elseif (is_int($args)) {
            $args = ['status' => $args];
        }

        return new Response(
            new BrowserKitResponse(
                $args['content'] ?? '',
                $args['status'] ?? 200,
                $args['headers'] ?? []
            ),
            new Crawler()
        );
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

    /** @test */
    public function it_can_assert_response_is_a_redirect()
    {
        foreach ([201, 301, 302, 303, 307, 308] as $status) {
            $response = $this->makeResponse([
                'status' => $status,
                'headers' => ['Location' => 'http://localhost/redirect']
            ]);

            $response->assertRedirect();
            $response->assertRedirect('http://localhost/redirect');
        }
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_a_redirect_based_on_status()
    {
        $this->expectException(AssertionFailedError::class);

        $response = $this->makeResponse(['headers' => ['Location' => 'http://localhost/redirect']]);

        $response->assertRedirect();
    }

    /** @test */
    public function it_can_fail_to_assert_response_is_a_redirect_based_on_location()
    {
        $this->expectException(AssertionFailedError::class);

        $response = $this->makeResponse([
            'status' => 301,
            'headers' => ['Location' => 'http://localhost/redirect']
        ]);

        $response->assertRedirect('http://localhost/different/location');
    }

    /** @test */
    public function it_can_assert_header_is_present()
    {
        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeader('apple');
    }

    /** @test */
    public function it_can_fail_to_assert_header_is_present()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertHeader('apple');
    }

    /** @test */
    public function it_can_assert_header_is_present_and_set_to_specific_value()
    {
        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeader('apple', 'red');
    }

    /** @test */
    public function it_can_fail_to_assert_header_is_present_when_value_does_not_match()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeader('apple', 'yellow');
    }

    /** @test */
    public function it_can_fail_to_assert_header_is_present_regardless_of_value()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeader('banana', 'yellow');
    }

    /** @test */
    public function it_can_assert_header_is_absent()
    {
        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeaderMissing('banana');
    }

    /** @test */
    public function it_can_fail_to_assert_header_is_absent()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse(['headers' => ['apple' => 'red']])->assertHeaderMissing('apple');
    }

    }
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
