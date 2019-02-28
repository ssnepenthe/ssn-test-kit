<?php

namespace SsnTestKit\Tests\Response;

use PHPUnit\Framework\TestCase;
use SsnTestKit\Tests\MakesResponses;
use Symfony\Component\BrowserKit\Request;
use PHPUnit\Framework\AssertionFailedError;

class UrlAssertionsTest extends TestCase
{
    use MakesResponses;

    protected $response;

    public function setUp() : void
    {
        $request = new Request('http://example.com/users/abc123', 'GET');

        $this->response = $this->makeResponse();

        // ->client() returns a PHPUnit mock.
        $this->response->client()->method('getInternalRequest')->willReturn($request);
    }

    public function tearDown() : void
    {
        $this->response = null;
    }

    /** @test */
    public function it_can_assert_url_is()
    {
        $this->response->assertUrlIs('http://example.com/users/abc123');
    }

    /** @test */
    public function it_can_fail_to_assert_url_is()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlIs('http://example.com/posts/xyz');
    }

    /** @test */
    public function it_can_assert_url_is_not()
    {
        $this->response->assertUrlIsNot('http://example.com/posts/xyz');
    }

    /** @test */
    public function it_can_fail_to_assert_url_is_not()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlIsNot('http://example.com/users/abc123');
    }

    /** @test */
    public function it_can_assert_url_contains()
    {
        $this->response->assertUrlContains('/users');
    }

    /** @test */
    public function it_can_fail_to_assert_url_contains()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlContains('/posts');
    }

    /** @test */
    public function it_can_assert_url_does_not_contain()
    {
        $this->response->assertUrlDoesNotContain('/posts');
    }

    /** @test */
    public function it_can_fail_to_assert_url_does_not_contain()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlDoesNotContain('/users');
    }

    /** @test */
    public function it_can_assert_url_starts_with()
    {
        $this->response->assertUrlStartsWith('http');
    }

    /** @test */
    public function it_can_fail_to_assert_url_starts_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlStartsWith('https');
    }

    /** @test */
    public function it_can_assert_url_does_not_start_with()
    {
        $this->response->assertUrlDoesNotStartWith('https');
    }

    /** @test */
    public function it_can_fail_to_assert_url_does_not_start_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlDoesNotStartWith('http');
    }

    /** @test */
    public function it_can_assert_url_ends_with()
    {
        $this->response->assertUrlEndsWith('123');
    }

    /** @test */
    public function it_can_fail_to_assert_url_ends_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlEndsWith('xyz');
    }

    /** @test */
    public function it_can_assert_url_does_not_end_with()
    {
        $this->response->assertUrlDoesNotEndWith('xyz');
    }

    /** @test */
    public function it_can_fail_to_assert_url_does_not_end_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlDoesNotEndWith('123');
    }

    /** @test */
    public function it_can_assert_url_matches_regexp()
    {
        $this->response->assertUrlMatches('/\/users\/[a-z0-9]+$/');
    }

    /** @test */
    public function it_can_fail_to_assert_url_matches_regexp()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlMatches('/\/posts\/[a-z]+$/');
    }

    /** @test */
    public function it_can_assert_url_does_not_match_regexp()
    {
        $this->response->assertUrlDoesNotMatch('/\/posts\/[a-z]+$/');
    }

    /** @test */
    public function it_can_fail_to_assert_url_does_not_match_regexp()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertUrlDoesNotMatch('/\/users\/[a-z0-9]+$/');
    }
}
