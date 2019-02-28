<?php

namespace SsnTestKit\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\BrowserKit\CookieJar;

class ResponseAssertionTest extends TestCase
{
    use MakesResponses;

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

    /** @test */
    public function it_can_assert_cookie_is_present()
    {
        $cookieJar = new CookieJar();
        $cookieJar->set(new Cookie('testcookie', 'testvalue'));

        $this->makeResponse([], null, $cookieJar)->assertCookie('testcookie');
    }

    /** @test */
    public function it_can_assert_cookie_is_present_and_set_to_specfic_value()
    {
        $cookieJar = new CookieJar();
        $cookieJar->set(new Cookie('testcookie', 'testvalue'));

        $this->makeResponse([], null, $cookieJar)->assertCookie('testcookie', 'testvalue');
    }

    /** @test */
    public function it_can_fail_to_assert_cookie_is_present()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse()->assertCookie('testcookie');
    }

    /** @test */
    public function it_can_fail_to_assert_cookie_is_present_when_value_does_not_match()
    {
        $this->expectException(AssertionFailedError::class);

        $cookieJar = new CookieJar();
        $cookieJar->set(new Cookie('testcookie', 'testvalue'));

        $this->makeResponse([], null, $cookieJar)->assertCookie('testcookie', 'wrongvalue');
    }

    /** @test */
    public function it_can_assert_cookie_is_absent()
    {
        $this->makeResponse()->assertCookieMissing('testcookie');
    }

    /** @test */
    public function it_can_fail_to_assert_cookie_is_absent()
    {
        $this->expectException(AssertionFailedError::class);

        $cookieJar = new CookieJar();
        $cookieJar->set(new Cookie('testcookie', 'testvalue'));

        $this->makeResponse([], null, $cookieJar)->assertCookieMissing('testcookie');
    }

    /** @test */
    public function it_can_make_assertions_against_a_filtered_crawler()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <p>First</p>
    <p>Second</p>
</body>
</html>
HTML;

        $this->makeResponse($html)
            ->within('p:first-child', function ($response) {
                $response
                    ->assertSee('First')
                    ->assertDontSee('Second');
            });
    }
}
