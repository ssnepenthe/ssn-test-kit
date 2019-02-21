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
    public function it_can_assert_string_is_present()
    {
        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSee('Lions')
            ->assertSee('tigers');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_present()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSee('wolves');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_present_due_to_case_sensitivity()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSee('lions');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_present_due_to_html_tag_interference()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSee('tigers and bears');
    }

    /** @test */
    public function it_can_assert_many_strings_are_present_in_order()
    {
        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeInOrder(['Lions', 'tigers', 'bears']);
    }

    /** @test */
    public function it_can_fail_to_assert_many_strings_are_present_in_order()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeInOrder(['tigers', 'bears', 'Lions']);
    }

    /** @test */
    public function it_can_fail_to_assert_many_strings_are_present_in_order_due_to_tag_interference()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeInOrder(['Lions and', 'tigers', 'bears']);
    }

    /** @test */
    public function it_can_assert_string_is_present_after_removing_tags()
    {
        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeText('Lions and tigers')
            ->assertSeeText('tigers and bears');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_present_after_removing_tags()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeText('tigers and wolves');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_present_after_removing_tags_due_to_case_sensitivity()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeText('lions and tigers');
    }

    /** @test */
    public function it_can_assert_many_strings_are_present_in_order_after_removing_tags()
    {
        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeTextInOrder(['Lions and', 'tigers and', 'bears, oh my!']);
    }

    /** @test */
    public function it_can_fail_to_assert_many_strings_are_present_in_order_after_removing_tags()
    {
        $this->expectException(AssertionFailedError::class);

        $this->makeResponse('<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!')
            ->assertSeeTextInOrder(['tigers and', 'Lions and', 'bears, oh my!']);
    }

    /** @test */
    public function it_can_assert_string_is_absent()
    {
        $response = $this->makeResponse(
            '<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'
        );

        $response->assertDontSee('wolves');

        // Case sensitive.
        $response->assertDontSee('lions');

        // Tags in the way.
        $response->assertDontSee('tigers and bears');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_absent()
    {
        $this->expectException(AssertionFailedError::class);

        $response = $this->makeResponse(
            '<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'
        );

        $response->assertDontSee('Lions');
    }

    /** @test */
    public function it_can_assert_string_is_absent_after_removing_tags()
    {
        $response = $this->makeResponse(
            '<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'
        );

        $response->assertDontSeeText('tigers and wolves');

        // Case sensitive.
        $response->assertDontSeeText('lions and tigers');
    }

    /** @test */
    public function it_can_fail_to_assert_string_is_absent_after_removing_tags()
    {
        $this->expectException(AssertionFailedError::class);

        $response = $this->makeResponse(
            '<em>Lions</em> and <em>tigers<em> and <em>bears</em>, oh my!'
        );

        $response->assertDontSeeText('Lions and tigers');
    }

    /** @test */
    public function it_can_assert_that_a_node_is_checked()
    {
        $crawler = new Crawler('<p><input checked type="checkbox"><p>');

        $this->makeResponse([], null, null, $crawler->filter('input'))->assertChecked();

        $this->makeResponse([], null, null, $crawler->filter('p'))
            ->assertChecked('[type="checkbox"]');
    }

    /** @test */
    public function it_can_fail_to_assert_that_a_root_node_is_checked()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<p><input checked type="checkbox"></p>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertChecked();
    }

    /** @test */
    public function it_can_fail_to_assert_that_a_root_node_is_checked_2()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<input type="checkbox">');

        $this->makeResponse([], null, null, $crawler->filter('input'))->assertChecked();
    }

    /** @test */
    public function it_can_fail_to_assert_that_a_node_is_checked()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<p><input type="checkbox"></p>');

        $this->makeResponse([], null, null, $crawler->filter('p'))
            ->assertChecked('[type="checkbox"]');
    }

    /** @test */
    public function it_can_assert_that_a_node_is_not_checked()
    {
        $crawler = new Crawler('<p><input type="checkbox"><p>');

        $this->makeResponse([], null, null, $crawler->filter('input'))
            ->assertNotChecked();

        $this->makeResponse([], null, null, $crawler->filter('p'))
            ->assertNotChecked('[type="checkbox"]');
    }

    /** @test */
    public function it_can_fail_to_assert_that_a_root_node_is_not_checked()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<input checked type="checkbox">');

        $this->makeResponse([], null, null, $crawler->filter('input'))->assertNotChecked();
    }

    /** @test */
    public function it_can_fail_to_assert_that_a_node_is_not_checked()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<p><input checked type="checkbox"></p>');

        $this->makeResponse([], null, null, $crawler->filter('p'))
            ->assertNotChecked('[type="checkbox"]');
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
