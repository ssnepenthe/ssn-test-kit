<?php

namespace SsnTestKit\Tests\Response;

use PHPUnit\Framework\TestCase;
use SsnTestKit\Tests\MakesResponses;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Framework\AssertionFailedError;

class DomAssertionsTest extends TestCase
{
    use MakesResponses;

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
    public function it_can_assert_crawler_node_count()
    {
        $crawler = new Crawler('<div><p>One</p><p>Two</p></div>');

        // Empty.
        $this->makeResponse()->assertNodeCount(0);

        // Single node.
        $this->makeResponse([], null, null, $crawler->filter('div'))->assertNodeCount(1);

        // Multiple nodes.
        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCount(2);

        // Single node filtered via assertion method.
        $response = $this->makeResponse([], null, null, $crawler->filter('div'));

        $response->assertNodeCount(1);
        $response->assertNodeCount(2, 'p');
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<p>One</p>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCount(2);
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count_no_selector()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<p>One</p>');

        $this->makeResponse([], null, null, $crawler)->assertNodeCount(2, 'p');
    }

    /** @test */
    public function it_can_assert_crawler_node_count_is_greater_than_number()
    {
        $crawler = new Crawler('<div><p>One</p><p>Two</p><p>Three</p></div>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCountGreaterThan(2);
        $this->makeResponse([], null, null, $crawler)->assertNodeCountGreaterThan(2, 'p');
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count_is_greater_than_number()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p>One</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertNodeCountGreaterThan(1, 'p');
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count_is_greater_than_number_no_selector()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p>One</p></div>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCountGreaterThan(1);
    }

    /** @test */
    public function it_can_assert_crawler_node_count_is_less_than_number()
    {
        $crawler = new Crawler('<div><p>One</p><p>Two</p><p>Three</p></div>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCountLessThan(4);
        $this->makeResponse([], null, null, $crawler)->assertNodeCountLessThan(4, 'p');
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count_is_less_than_number()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p>One</p><p>Two</p><p>Three</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertNodeCountGreaterThan(3, 'p');
    }

    /** @test */
    public function it_can_fail_to_assert_crawler_node_count_is_less_than_number_no_selector()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p>One</p><p>Two</p><p>Three</p></div>');

        $this->makeResponse([], null, null, $crawler->filter('p'))->assertNodeCountGreaterThan(3);
    }

    /** @test */
    public function it_can_assert_node_presence_by_selector()
    {
        $crawler = new Crawler('<div><p class="one">One<p><p class="two">Two</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertPresent('.one');
    }

    /** @test */
    public function it_can_fail_to_assert_node_presence_by_selector()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p class="one">One<p><p class="two">Two</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertPresent('.three');
    }

    /** @test */
    public function it_can_assert_node_absence_by_selector()
    {
        $crawler = new Crawler('<div><p class="one">One<p><p class="two">Two</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertAbsent('.three');
    }

    /** @test */
    public function it_can_fail_to_assert_node_absence_by_selector()
    {
        $this->expectException(AssertionFailedError::class);

        $crawler = new Crawler('<div><p class="one">One<p><p class="two">Two</p></div>');

        $this->makeResponse([], null, null, $crawler)->assertAbsent('.one');
    }
}
