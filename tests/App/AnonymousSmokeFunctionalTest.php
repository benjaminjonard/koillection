<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\AppTestCase;

class AnonymousSmokeFunctionalTest extends AppTestCase
{
    /**
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function isSuccessfulUrlProvider(): \Generator
    {
        // Main
        yield ['/'];
    }
}
