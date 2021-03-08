<?php

declare(strict_types=1);

namespace App\Tests;

class AnonymousSmokeFunctionalTest extends LoggedWebTestCase
{
    /**
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful(string $url)
    {
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function isSuccessfulUrlProvider(): \Generator
    {
        //Main
        yield ["/"];
    }
}
