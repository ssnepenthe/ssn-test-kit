<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Response;
use Facebook\WebDriver\WebDriver;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;

trait MakesResponses
{
    protected function makeResponse(
        $args = [],
        BrowserKitResponse $bkResponse = null,
        Crawler $crawler = null
    ) {
        if (is_string($args)) {
            $args = ['content' => $args];
        } elseif (is_int($args)) {
            $args = ['status' => $args];
        }

        $client = $this->createMock(Client::class);

        $client->method('getInternalResponse')
            ->willReturn($bkResponse ?? new BrowserKitResponse(
                $args['content'] ?? '',
                $args['status'] ?? 200,
                $args['headers'] ?? []
            ));

        $client->method('getCrawler')
            ->willReturn($crawler ?? new Crawler());

        return new Response($client);
    }

    protected function makePantherResponse(
        $args = [],
        BrowserKitResponse $bkResponse = null,
        PantherCrawler $crawler = null
    ) {
        return $this->makeResponse(
            $args,
            $bkResponse,
            $crawler ?? new PantherCrawler([], $this->createMock(WebDriver::class))
        );
    }
}
