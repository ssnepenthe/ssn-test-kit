<?php

namespace SsnTestKit\Tests;

use SsnTestKit\Response;
use Facebook\WebDriver\WebDriver;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\Panther\Cookie\CookieJar as PantherCookieJar;
use Symfony\Component\Panther\DomCrawler\Crawler as PantherCrawler;

trait MakesResponses
{
    protected function makeResponse(
        $args = [],
        BrowserKitResponse $bkResponse = null,
        CookieJar $cookieJar = null,
        Crawler $crawler = null
    ) {
        if (is_string($args)) {
            $args = ['content' => $args];
        } elseif (is_int($args)) {
            $args = ['status' => $args];
        }

        $client = $this->createMock(Client::class);

        if (null === $bkResponse) {
            $bkResponse = new BrowserKitResponse(
                $args['content'] ?? '',
                $args['status'] ?? 200,
                $args['headers'] ?? []
            );
        }

        $client->method('getInternalResponse')->willReturn($bkResponse);

        if (null === $cookieJar) {
            $cookieJar = new CookieJar();
            $cookieJar->updateFromResponse($bkResponse);
        }

        $client->method('getCookieJar')->willReturn($cookieJar);

        $client->method('getCrawler')->willReturn($crawler ?? new Crawler());

        return new Response($client);
    }

    protected function makePantherResponse(
        $args = [],
        BrowserKitResponse $bkResponse = null,
        PantherCookieJar $cookieJar = null,
        PantherCrawler $crawler = null
    ) {
        $webdriver = $this->createMock(WebDriver::class);

        return $this->makeResponse(
            $args,
            $bkResponse,
            $cookieJar ?? new PantherCookieJar($webdriver),
            $crawler ?? new PantherCrawler([], $webdriver)
        );
    }
}
