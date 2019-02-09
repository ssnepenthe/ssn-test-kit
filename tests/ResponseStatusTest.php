<?php

namespace SsnTestKit\Tests;

use Slim\Http\StatusCode;
use PHPUnit\Framework\TestCase;
use SsnTestKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class ResponseStatusTest extends TestCase
{
    use MakesResponses;

    protected function getStatusBlocksForRange($lower, $upper)
    {
        $codes = $this->getStatusCodes();

        $desired = array_filter($codes, function ($status) use ($lower, $upper) {
            return $status >= $lower && $status < $upper;
        });

        $rest = array_filter($codes, function ($status) use ($lower, $upper) {
            return $status < $lower || $status >= $upper;
        });

        return [$desired, $rest];
    }

    protected function getStatusCodes()
    {
        // Taken from symfony/http-foundation - hopefully this is complete.
        return [
            100, 101, 102, 103, 200, 201, 202, 203, 204, 205, 206, 207, 208, 226, 300, 301, 302,
            303, 304, 305, 307, 308, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411,
            412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
            500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511,
        ];
    }

    /** @test */
    public function it_can_identify_informational_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(100, 200);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isInformational());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isInformational());
        }
    }

    /** @test */
    public function it_can_identify_successful_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(200, 300);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isSuccessful());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isSuccessful());
        }
    }

    /** @test */
    public function it_can_identify_redirection_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(300, 400);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isRedirection());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isRedirection());
        }
    }

    /** @test */
    public function it_can_identify_client_error_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(400, 500);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isClientError());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isClientError());
        }
    }

    /** @test */
    public function it_can_identify_server_error_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(500, 600);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isServerError());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isServerError());
        }
    }

    /** @test */
    public function it_can_identify_ok_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(200, 201);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isOk());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isOk());
        }
    }

    /** @test */
    public function it_can_identify_forbidden_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(403, 404);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isForbidden());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isForbidden());
        }
    }

    /** @test */
    public function it_can_identify_not_found_responses()
    {
        list($desired, $rest) = $this->getStatusBlocksForRange(404, 405);

        foreach ($desired as $status) {
            $this->assertTrue($this->makeResponse($status)->isNotFound());
        }

        foreach ($rest as $status) {
            $this->assertFalse($this->makeResponse($status)->isNotFound());
        }
    }

    /** @test */
    public function it_can_identify_redirect_responses()
    {
        $redirectCodes = [201, 301, 302, 303, 307, 308];

        $allCodes = $this->getStatusCodes();

        $desired = array_filter($allCodes, function ($status) use ($redirectCodes) {
            return \in_array($status, $redirectCodes, true);
        });

        $rest = array_filter($allCodes, function ($status) use ($redirectCodes) {
            return ! \in_array($status, $redirectCodes, true);
        });

        foreach ($desired as $status) {
            $withoutLocation = $this->makeResponse($status);
            $withLocation = $this->makeResponse([
                'status' => $status,
                'headers' => ['location' => '/test'],
            ]);

            $this->assertTrue($withoutLocation->isRedirect());
            $this->assertTrue($withLocation->isRedirect());

            $this->assertFalse($withoutLocation->isRedirect('/test'));
            $this->assertTrue($withLocation->isRedirect('/test'));
        }

        foreach ($rest as $status) {
            $withoutLocation = $this->makeResponse($status);
            $withLocation = $this->makeResponse([
                'status' => $status,
                'headers' => ['location' => '/test'],
            ]);

            $this->assertFalse($withoutLocation->isRedirect());
            $this->assertFalse($withLocation->isRedirect());

            $this->assertFalse($withLocation->isRedirect('/test'));
            $this->assertFalse($withoutLocation->isRedirect('/test'));
        }
    }
}
