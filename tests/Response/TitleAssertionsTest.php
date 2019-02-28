<?php

use PHPUnit\Framework\TestCase;
use SsnTestKit\Tests\MakesResponses;
use PHPUnit\Framework\AssertionFailedError;

class TitleAssertionsTest extends TestCase
{
    use MakesResponses;

    protected $response;

    public function setUp() : void
    {
        $html = <<<HTML
<html>
	<head>
		<title>Hello 123!</title>
	</head>
	<body>
	</body>
</html>
HTML;

        $this->response = $this->makeResponse($html);
    }

    public function tearDown() : void
    {
        $this->html = null;
    }

    /** @test */
    public function it_can_assert_title_is()
    {
        $this->response->assertTitleIs('Hello 123!');
    }

    /** @test */
    public function it_can_fail_to_assert_title_is()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleIs('Hi 321?');
    }

    /** @test */
    public function it_can_assert_title_is_not()
    {
        $this->response->assertTitleIsNot('Hi 321?');
    }

    /** @test */
    public function it_can_fail_to_assert_title_is_not()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleIsNot('Hello 123!');
    }

    /** @test */
    public function it_can_assert_title_contains()
    {
        $this->response->assertTitleContains('12');
    }

    /** @test */
    public function it_can_fail_to_assert_title_contains()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleContains('32');
    }

    /** @test */
    public function it_can_assert_title_does_not_contain()
    {
        $this->response->assertTitleDoesNotContain('32');
    }

    /** @test */
    public function it_can_fail_to_assert_title_does_not_contain()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleDoesNotContain('12');
    }

    /** @test */
    public function it_can_assert_title_starts_with()
    {
        $this->response->assertTitleStartsWith('Hello');
    }

    /** @test */
    public function it_can_fail_to_assert_title_starts_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleStartsWith('Hi');
    }

    /** @test */
    public function it_can_assert_title_does_not_start_with()
    {
        $this->response->assertTitleDoesNotStartWith('Hi');
    }

    /** @test */
    public function it_can_fail_to_assert_title_does_not_start_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleDoesNotStartWith('Hello');
    }

    /** @test */
    public function it_can_assert_title_ends_with()
    {
        $this->response->assertTitleEndsWith('!');
    }

    /** @test */
    public function it_can_fail_to_assert_title_ends_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleEndsWith('?');
    }

    /** @test */
    public function it_can_assert_title_does_not_end_with()
    {
        $this->response->assertTitleDoesNotEndWith('?');
    }

    /** @test */
    public function it_can_fail_to_assert_title_does_not_end_with()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleDoesNotEndWith('!');
    }

    /** @test */
    public function it_can_assert_title_matches_regexp()
    {
        $this->response->assertTitleMatches('/[a-zA-Z]+\s[0-9]+!$/');
    }

    /** @test */
    public function it_can_fail_to_assert_title_matches_regexp()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleMatches('/[0-9]+\s[a-zA-Z]+!$/');
    }

    /** @test */
    public function it_can_assert_title_does_not_match_regexp()
    {
        $this->response->assertTitleDoesNotMatch('/[0-9]+\s[a-zA-Z]+!$/');
    }

    /** @test */
    public function it_can_fail_to_assert_title_does_not_match_regexp()
    {
        $this->expectException(AssertionFailedError::class);

        $this->response->assertTitleDoesNotMatch('/[a-zA-Z]+\s[0-9]+!$/');
    }
}
