<?php

namespace SsnTestKit\Tests;

use Slim\Http\StatusCode;
use PHPUnit\Framework\TestCase;
use SsnTestKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class ResponseStatusTest extends TestCase {
	protected function createResponse($status)
	{
		return new Response(new BrowserKitResponse('', $status), new Crawler());
	}

	protected function getStatusBlocksForRange($lower, $upper)
	{
		$codes = (new \ReflectionClass(StatusCode::class))->getConstants();

		$desired = array_filter($codes, function($status) use ($lower, $upper) {
			return $status >= $lower && $status < $upper;
		});

		$rest = array_filter($codes, function($status) use ($lower, $upper) {
			return $status < $lower || $status >= $upper;
		});

		return [$desired, $rest];
	}

	/** @test */
	public function it_can_identify_informational_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(100, 200);

    	foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isInformational());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isInformational());
    	}
	}

	/** @test */
	public function it_can_identify_successful_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(200, 300);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isSuccessful());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isSuccessful());
    	}
	}

	/** @test */
	public function it_can_identify_redirection_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(300, 400);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isRedirection());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isRedirection());
    	}
	}

	/** @test */
	public function it_can_identify_client_error_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(400, 500);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isClientError());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isClientError());
    	}
	}

	/** @test */
	public function it_can_identify_server_error_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(500, 600);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isServerError());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isServerError());
    	}
	}

	/** @test */
	public function it_can_identify_ok_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(200, 201);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isOk());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isOk());
    	}
	}

	/** @test */
	public function it_can_identify_forbidden_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(403, 404);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isForbidden());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isForbidden());
    	}
	}

	/** @test */
	public function it_can_identify_not_found_responses()
	{
		list($desired, $rest) = $this->getStatusBlocksForRange(404, 405);

		foreach ($desired as $status) {
    		$this->assertTrue($this->createResponse($status)->isNotFound());
    	}

    	foreach ($rest as $status) {
    		$this->assertFalse($this->createResponse($status)->isNotFound());
    	}
	}

	/** @test */
	public function it_can_identify_redirect_responses()
	{
		$redirectCodes = [201, 301, 302, 303, 307, 308];

		$allCodes = (new \ReflectionClass(StatusCode::class))->getConstants();

		$desired = array_filter($allCodes, function($status) use ($redirectCodes) {
			return \in_array($status, $redirectCodes, true);
		});

		$rest = array_filter($allCodes, function($status) use ($redirectCodes) {
			return ! \in_array($status, $redirectCodes, true);
		});

		foreach ($desired as $status) {
			$withoutLocation = new Response(
				new BrowserKitResponse('', $status),
				new Crawler()
			);

			$withLocation = new Response(
				new BrowserKitResponse('', $status, ['location' => '/test']),
				new Crawler()
			);

			$this->assertTrue($withoutLocation->isRedirect());
			$this->assertTrue($withLocation->isRedirect());

			$this->assertFalse($withoutLocation->isRedirect('/test'));
			$this->assertTrue($withLocation->isRedirect('/test'));
		}

		foreach ($rest as $status) {
			$withoutLocation = new Response(
				new BrowserKitResponse('', $status),
				new Crawler()
			);

			$withLocation = new Response(
				new BrowserKitResponse('', $status, ['location' => '/test']),
				new Crawler()
			);

			$this->assertFalse($withoutLocation->isRedirect());
			$this->assertFalse($withLocation->isRedirect());

			$this->assertFalse($withLocation->isRedirect('/test'));
			$this->assertFalse($withoutLocation->isRedirect('/test'));
		}
	}
}