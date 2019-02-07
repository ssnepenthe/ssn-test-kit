<?php

namespace SsnTestKit\Tests;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\WebDriver;
use SsnTestKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;

class ResponseInitializationTest extends TestCase
{
    /** @test */
    public function it_provides_access_to_the_raw_response_content()
    {
        $empty = new Response(new BrowserKitResponse(), new Crawler());
        $full = new Response(new BrowserKitResponse('Test Content'), new Crawler());

        // @todo Check on panther... I think this is initial page content so probably before JS execution...
        $this->assertEquals('', $empty->content());
        $this->assertEquals('Test Content', $full->content());
    }

    /** @test */
    public function it_provides_access_to_the_raw_status_code()
    {
        $twoHundred = new Response(new BrowserKitResponse(), new Crawler());
        $fourOhFour = new Response(new BrowserKitResponse('', 404), new Crawler());

        $this->assertSame(200, $twoHundred->status());
        $this->assertSame(404, $fourOhFour->status());
    }

    /** @test */
    public function it_throws_when_attempting_to_inspect_status_of_panther_request()
    {
        $this->expectException(\RuntimeException::class);

        $response = new Response(
            new BrowserKitResponse(),
            new PantherCrawler([], $this->createMock(WebDriver::class))
        );

        $response->status();
    }

    /** @test */
    public function it_provides_access_to_single_response_headers()
    {
        $empty = new Response(new BrowserKitResponse(), new Crawler());
        $full = new Response(
            new BrowserKitResponse('', 200, ['one' => 'first', 'two' => 'second']),
            new Crawler()
        );

        $this->assertNull($empty->header('one'));
        $this->assertEquals('first', $full->header('one'));
        $this->assertEquals(['first'], $full->header('one', false));
    }

    /** @test */
    public function it_normalizes_header_names_when_retrieving_a_single_header()
    {
        // Header name is converted to lowercase and '-' is replaced with '_'.
        $response = new Response(
            new BrowserKitResponse('', 200, [
                'Header-One' => 'Value one',
                'Header_Two' => 'Value two',
                'header-three' => 'Value three',
            ]),
            new Crawler()
        );

        $this->assertEquals('Value one', $response->header('Header-One'));
        $this->assertEquals('Value one', $response->header('Header_One'));
        $this->assertEquals('Value one', $response->header('header-one'));
        $this->assertEquals('Value one', $response->header('header_one'));

        $this->assertEquals('Value two', $response->header('Header-Two'));
        $this->assertEquals('Value two', $response->header('Header_Two'));
        $this->assertEquals('Value two', $response->header('header-two'));
        $this->assertEquals('Value two', $response->header('header_two'));

        $this->assertEquals('Value three', $response->header('Header-Three'));
        $this->assertEquals('Value three', $response->header('Header_Three'));
        $this->assertEquals('Value three', $response->header('header-three'));
        $this->assertEquals('Value three', $response->header('header_three'));
    }

    /** @test */
    public function it_throws_when_attempting_to_inspect_single_header_of_panther_request()
    {
        $this->expectException(\RuntimeException::class);

        $response = new Response(
            new BrowserKitResponse(),
            new PantherCrawler([], $this->createMock(WebDriver::class))
        );

        $response->header('one');
    }

    /** @test */
    public function it_provides_access_to_the_raw_response_headers()
    {
        $empty = new Response(new BrowserKitResponse(), new Crawler());
        $full = new Response(
            new BrowserKitResponse('', 200, ['one' => 'first', 'two' => 'second']),
            new Crawler()
        );

        $this->assertEquals([], $empty->headers());
        $this->assertEquals(['one' => 'first', 'two' => 'second'], $full->headers());
    }

    /** @test */
    public function it_throws_when_attempting_to_inspect_headers_of_panther_request()
    {
        $this->expectException(\RuntimeException::class);

        $response = new Response(
            new BrowserKitResponse(),
            new PantherCrawler([], $this->createMock(WebDriver::class))
        );

        $response->headers();
    }

    /** @test */
    public function it_provides_access_to_a_dom_crawler_instance()
    {
        $crawler = new Crawler();
        $response = new Response(new BrowserKitResponse(), $crawler);

        $this->assertInstanceOf(Crawler::class, $response->crawler());
        $this->assertSame($crawler, $response->crawler());
    }

    /** @test */
    public function it_provides_access_to_the_browser_kit_response_object()
    {
        $bk = new BrowserKitResponse();
        $response = new Response($bk, new Crawler());

        $this->assertInstanceOf(BrowserKitResponse::class, $response->unwrap());
        $this->assertSame($bk, $response->unwrap());
    }

    /** @test */
    public function it_knows_whether_a_response_was_generated_by_panther()
    {
        $goutte = new Response(new BrowserKitResponse(), new Crawler());
        $panther = new Response(
            new BrowserKitResponse(),
            new PantherCrawler([], $this->createMock(WebDriver::class))
        );

        $this->assertFalse($goutte->isPanther());
        $this->assertTrue($panther->isPanther());
    }
}
