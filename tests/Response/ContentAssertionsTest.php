<?php

namespace SsnTestKit\Tests\Response;

use PHPUnit\Framework\TestCase;
use SsnTestKit\Tests\MakesResponses;
use PHPUnit\Framework\AssertionFailedError;

class ContentAssertionsTest extends TestCase
{
    use MakesResponses;

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
}
