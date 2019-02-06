<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Browser;
use SsnTestKit\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

// @todo Skip when slim server isn't running?
class BrowserRequestTest extends TestCase {
	/** @test */
	public function it_wraps_responses() {
		$browser = new Browser();
		$response = $browser->request('GET', 'http://localhost');

		$this->assertInstanceOf(Response::class, $response);
	}

	/** @test */
	public function it_can_make_requests() {

	}

	/** @test */
	public function it_provides_shorthand_for_get_requests() {
		$browser = new Browser();
		$response = $browser->get('http://localhost');

		$this->assertEquals('GET', $browser->client()->getInternalRequest()->getMethod());
	}

	/** @test */
	public function it_provides_shorthand_for_post_requests() {
		$browser = new Browser();
		$response = $browser->post('http://localhost');

		$this->assertEquals('POST', $browser->client()->getInternalRequest()->getMethod());
	}

	/** @test */
	public function it_correctly_sets_the_base_uri_with_guzzle_for_goutte() {
		// Default.
		$browser = new Browser();
		$response = $browser->request('GET', 'http://localhost/status-code/200');

		$this->assertNull($browser->client()->getClient()->getConfig('base_uri'));
		$this->assertEquals('200', $response->crawler()->filter('.code')->text());

		// Custom.
		$browser = new Browser('http://localhost');
		$response = $browser->request('GET', '/status-code/200');

		$guzzleBaseUri = $browser->client()->getClient()->getConfig('base_uri');

		$this->assertInstanceOf(UriInterface::class, $guzzleBaseUri);
		$this->assertEquals('http', $guzzleBaseUri->getScheme());
		$this->assertEquals('localhost', $guzzleBaseUri->getHost());
		$this->assertEquals('200', $response->crawler()->filter('.code')->text());
	}

	/** @test */
	public function it_correctly_sets_the_base_uri_for_panther() {
		// Default.
		$browser = (new Browser())->enableJavascript();
		$response = $browser->request('GET', 'http://localhost/status-code/200');

		$this->assertEquals('200', $response->crawler()->filter('.code')->text());

		$browser->quit();

		// Custom.
		$browser = (new Browser('http://localhost'))->enableJavascript();
		$response = $browser->request('GET', '/status-code/200');

		$this->assertEquals('200', $response->crawler()->filter('.code')->text());

		$browser->quit();
	}

	/** @test */
	public function it_uses_the_correct_client_depending_on_javascript_configuration() {
		$browser = new Browser();
		$response = $browser->request('GET', 'http://localhost/js/enabled');

		$this->assertFalse($response->isPanther());
		$this->assertEquals('This is without JavaScript.', $response->crawler()->filter('p')->text());

		$browser->enableJavascript();
		$response = $browser->request('GET', 'http://localhost/js/enabled');

		$this->assertTrue($response->isPanther());
		$this->assertEquals('This is with JavaScript.', $response->crawler()->filter('p')->text());

		// Panther automatically quits on destruct... Is this necessary?
		$browser->quit();
	}

	/** @test */
	public function it_can_make_a_one_off_javascript_enabled_request() {

	}

	/** @test */
	public function it_can_wait_for_an_element_to_become_visible_before_acting() {

	}
}
